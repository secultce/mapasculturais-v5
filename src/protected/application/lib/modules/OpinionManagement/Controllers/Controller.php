<?php

declare(strict_types=1);

namespace OpinionManagement\Controllers;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use EvaluationMethodTechnical\Plugin as EvaluationTechnicalPlugin;
use MapasCulturais\App;
use MapasCulturais\i;
use MapasCulturais\Entities\EvaluationMethodConfigurationMeta;
use MapasCulturais\Entities\Notification;
use MapasCulturais\Entities\Opportunity;
use MapasCulturais\Entities\Registration;
use MapasCulturais\Entities\RegistrationEvaluation;
use MapasCulturais\Exceptions\PermissionDenied;
use MapasCulturais\Exceptions\WorkflowRequest;
use OpinionManagement\Helpers\EvaluationList;
use OpinionManagement\Module;
use OpinionManagement\Services\OpinionPublication;
use MapasCulturais\Services\AmqpQueueService;

class Controller extends  \MapasCulturais\Controller
{
    public function GET_index(): void
    {
        $app = App::i();

        if(!$app->user->is('superAdmin')) {
            $this->layout = 'error-404';
            return;
        }

        $config = (object) [
            'autopublish' => true
        ];

        $this->render('index', ['config' => $config]);

    }
     public function GET_opinions(): void
    {
        $app = App::i();
        $this->requireAuthentication();

        /**
         * @var $registration Registration
         */

        $registrationId = $this->data['id'] ?? null;
        $registration = $registrationId ? $app->repo(Registration::class)->find($registrationId) : null;

        if (!$registration) {
            $this->json(['error' => i::__('Inscrição não encontrada.')], 404);
            return;
        }

        $opportunity = $registration->opportunity;
        $canManageOpportunity = $opportunity->canUser('@control', $app->user);
        $canViewPublishedOpinions = OpinionPublication::isPublished($registration) && $registration->canUser('view', $app->user);

        if ($canManageOpportunity || $canViewPublishedOpinions) {
            $evaluationsAvg = $this->getEvaluationsAvg($registration);

            (new EvaluationTechnicalPlugin())->applyAffirmativePolicies($evaluationsAvg, $registration);

            $opinions = new EvaluationList($registration);

            $data = [
                'appliedAffirmativePolicy' => $registration->appliedAffirmativePolicy ?? false,
                'evaluationMethod' => (string) $registration->opportunity->evaluationMethodConfiguration->type,
                'opinions' => $opinions,
                'registration' => $registration,
            ];

            if ('technical' === $data['evaluationMethod']) {
                $data['criteria'] = self::getCriteriaMeta($registration->opportunity);
            }

            $this->json($data);
            return;
        }

        $this->json(['permission-denied'], 403);
    }

     public function POST_publishOpinions(): void
    {
        $app = App::i();
        if($app->user->is('guest')) {
            $app->redirect($app->getBaseUrl());
        }

        $opportunityId = $this->postData['id'] ?? null;
        $opportunity = $opportunityId ? $app->repo('Opportunity')->find($opportunityId) : null;

        if (!$opportunity) {
            $this->json(['error' => i::__('Oportunidade não encontrada.')], 404);
            return;
        }

        if(!$opportunity->canUser('@control', $app->user)) {
            $this->json([
                'error' => i::__('Você não tem permissão para publicar os pareceres desta oportunidade.'),
            ], 403);
            return;
        }

        if ($opportunity->publishedOpinions) {
            $this->json(['success' => true]);
            return;
        }

        if (!Module::areAllEvaluationsSent($opportunity)) {
            $this->json([
                'error' => i::__('É necessário aguardar o envio de todas as avaliações antes de publicar os pareceres.'),
            ], 409);
            return;
        }

        try {
            $opportunity->setMetadata('publishedOpinions', true);
            $opportunity->save(true);
        } catch (\Exception $e) {
            $app->log->error("Não foi possível publicar os pareceres da oportunidade {$opportunity->id}: {$e->getMessage()}");

            $this->json([
                'error' => i::__('Não foi possível publicar os pareceres. Tente novamente.'),
            ], 500);

            return;
        }

        $sealIds = $opportunity->owner->getRelatedSealIds();
        $this->notificateUsers($opportunity->id, true, $sealIds);

        $this->json(['success' => true]);
    }

    public function POST_registrationStates(): void
    {
        $this->selectedOpinions(false);
    }

    public function POST_publishSelectedOpinions(): void
    {
        $this->selectedOpinions(true);
    }

    private function selectedOpinions(bool $publish): void
    {
        $this->requireAuthentication();
        $app = App::i();
        $opportunity = $app->repo(Opportunity::class)->find($this->postData['id'] ?? 0);
        if (!$opportunity) {
            $this->json(['error' => i::__('Oportunidade não encontrada.')], 404);
            return;
        }
        if (!$opportunity->canUser('@control', $app->user)) {
            $this->json(['error' => i::__('Você não tem permissão para publicar os pareceres desta oportunidade.')], 403);
            return;
        }
        if (!Module::isEvaluationMethodValid($opportunity)) {
            $this->json(['error' => i::__('Este método de avaliação não permite a publicação de pareceres.')], 409);
            return;
        }
        $ids = $this->postData['registrationIds'] ?? [];
        if (!is_array($ids) || !$ids || count($ids) > 500) {
            $this->json(['error' => i::__('Selecione entre 1 e 500 inscrições.')], 400);
            return;
        }
        foreach ($ids as $id) {
            if (!is_scalar($id) || !ctype_digit((string) $id) || (int) $id < 1) {
                $this->json(['error' => i::__('Seleção de inscrições inválida.')], 400);
                return;
            }
        }
        $ids = array_values(array_unique(array_map('intval', $ids)));
        $service = new OpinionPublication();
        try {
            if ($publish) {
                $published = $service->publish($opportunity, $ids);
                try {
                    self::notifyRegistrations($published, $opportunity->owner->getRelatedSealIds());
                } catch (\Throwable $exception) {
                    $app->log->error("Pareceres publicados, mas houve falha nas notificações da oportunidade {$opportunity->id}: {$exception->getMessage()}");
                }
                $response = ['success' => true, 'publishedIds' => $published->map(function ($registration) {
                    return $registration->id;
                })->getValues()];
            } else {
                $registrations = $app->repo(Registration::class)->findBy(['id' => $ids, 'opportunity' => $opportunity]);
                if (count($registrations) !== count($ids)) {
                    throw new \InvalidArgumentException('A seleção contém inscrições inexistentes ou de outra oportunidade.');
                }
                $response = ['states' => $service->states($opportunity, $registrations)];
            }
        } catch (\InvalidArgumentException $exception) {
            $this->json(['error' => $exception->getMessage()], 400);
            return;
        } catch (\DomainException $exception) {
            $this->json(['error' => $exception->getMessage()], 409);
            return;
        } catch (\Throwable $exception) {
            $app->log->error("Falha na publicação de pareceres da oportunidade {$opportunity->id}: {$exception->getMessage()}");
            $this->json(['error' => i::__('Não foi possível processar os pareceres. Tente novamente.')], 500);
            return;
        }
        $this->json($response);
    }

     /**
     * @throws WorkflowRequest
     * @throws PermissionDenied
     */
    public static function notificateUsers(int $opportunityId, bool $verifyPublishingOpinions = true,array $sealIds = []): bool
    {
        $app = App::i();
        $opportunity = $app->repo('Opportunity')->find($opportunityId);
        if($verifyPublishingOpinions && $opportunity->publishedOpinions === false) {
            return false;
        }

        set_time_limit(500);

        $criteria = new Criteria();
        $criteria->where($criteria->expr()->eq('opportunity', $opportunity));
        $criteria->andWhere($criteria->expr()->gt('status', '0'));

        $registrations = $app->repo('Registration')->matching($criteria)->filter(function (Registration $registration) {
            // Individual publications have already notified these proponents.
            return !$registration->getMetadata('opinionPublication');
        });

        return self::notifyRegistrations($registrations, $sealIds);
    }

    private static function notifyRegistrations(Collection $registrations, array $sealIds): bool
    {
        $app = App::i();
        if ($registrations->isEmpty()) {
            return true;
        }

        if (in_array(2, $sealIds)) {
            try {
                self::sendToMailQueue($registrations);
                $app->log->debug("Processo de envio de emails enviado para a fila.");
            } catch (\Throwable $exception) {
                $app->log->error(
                    "Não foi possível enviar os emails de publicação de pareceres da oportunidade " .
                    "para a fila: {$exception->getMessage()}"
                );
            }
        }

        $count = count($registrations);
        $failed = 0;
        $succeed = 0;
        foreach ($registrations as $i => $registration) {
            try {
                self::creteAppNotification($registration);
                $succeed++;
                $app->log->debug("Notificação ".($i+1)."/$count enviada para o usuário {$registration->owner->user->id} ({$registration->owner->name})");
            } catch (\Exception $e) {
                $failed++;
                $app->log->error("Notificação ".($i+1)."/$count não enviada ao usuário {$registration->owner->user->id} ({$registration->owner->name})");
            }
        }

        $app->log->debug("Notificações enviadas!\nTotal: $count\nFalhas: $failed\nSucesso: $succeed");

        return true;
    }

      /**
     * @throws WorkflowRequest
     * @throws PermissionDenied
     */
    private static function creteAppNotification(Registration $registration): void
    {
        $notification = new Notification();
        $notification->user = $registration->owner->user;
        $notification->message = sprintf(
            "Sua inscrição <a style='font-weight:bold;' href='/inscricao/{$registration->id}'>%s</a>" .
            " da oportunidade <a style='font-weight:bold;' href='/oportunidade/{$registration->opportunity->id}'>%s</a> está com os pareceres publicados.",
            $registration->number,
            $registration->opportunity->name
        );
        $notification->save(true);
    }

    private static function sendToMailQueue(Collection $registrations): void
    {
        $app = App::i();
        $registration = $registrations->first();
        if (!$registration) {
            return;
        }

        $registrationsData = [];
        foreach ($registrations as $registration) {
            $registrationsData[] = [
                'number' => $registration->number,
                'url' => $registration->getSingleUrl(),
                'agent' => [
                    'name' => $registration->owner->name,
                    'email' => $registration->owner->user->email,
                ],
            ];
        }

        $data = [
            'registrations' => $registrationsData,
            'opportunity' => [
                'name' => $registration->opportunity->name,
                'url' =>  $registration->opportunity->getSingleUrl(),
            ],
        ];

        // Observar padrão usado na documentação para uso do mapa cultural
        $queueService = new AmqpQueueService();
        $queueService->sendMessage(
            $app->config['rabbitmq']['exchange_default'],
            '',
            $data,
            $app->config['rabbitmq']['queues']['queue_opinion_management']
        );
    }

    public static function getCriteriaMeta(Opportunity $opportunity): array
    {
        $app = App::i();
        $criteria = $app->repo(EvaluationMethodConfigurationMeta::class)->findOneBy([
            'key' => 'criteria',
            'owner' => $opportunity->evaluationMethodConfiguration,
        ]);
        $criteria = json_decode($criteria->value, true) ?? [];
        $finalCriteria = [];
        array_walk($criteria, function ($criterion) use (&$finalCriteria){
            $finalCriteria[$criterion['id']] = $criterion;
        });

        return $finalCriteria;
    }

    private function getEvaluationsAvg(Registration $registration)
    {
        $app = App::i();

        $evaluations = $app->repo(RegistrationEvaluation::class)->findBy(['registration' => $registration]);

        if (empty($evaluations)) {
            $this->json(['not-found'], 404);
        }

        $evaluationsAvg = 0;

        foreach ($evaluations as $evaluation) {
            $evaluationsAvg += (float) $evaluation->result;
        }

        $evaluationsAvg /= count($evaluations); // Necessário utilizar a média das avaliações para aplicar as políticas afirmativas

        return $evaluationsAvg;
    }

}
