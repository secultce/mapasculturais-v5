<?php

declare(strict_types=1);

namespace OpinionManagement\Controllers;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use EvaluationMethodTechnical\Plugin as EvaluationTechnicalPlugin;
use MapasCulturais\App;
use MapasCulturais\Entities\EvaluationMethodConfigurationMeta;
use MapasCulturais\Entities\Notification;
use MapasCulturais\Entities\Opportunity;
use MapasCulturais\Entities\Registration;
use MapasCulturais\Entities\RegistrationEvaluation;
use MapasCulturais\Exceptions\PermissionDenied;
use MapasCulturais\Exceptions\WorkflowRequest;
use OpinionManagement\Helpers\EvaluationList;
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
        $registration = $app->repo(Registration::class)->find($this->data['id']);
        if ($registration->canUser('view')) {
            $evaluationsAvg = self::getEvaluationsAvg($registration);
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

        $opportunity = $app->repo('Opportunity')->find($this->postData['id']);

        if(!$opportunity->canUser('@control', $app->user)) {
            $this->json(['permission-denied'], 403);
            return;
        }

        try {
            $opportunity->setMetadata('publishedOpinions', true);
            $opportunity->save(true);
        } catch (\Exception $e) {
            $this->json(['error' => new \PDOException('Cannot save this data', 0, $e)], 500);
            return;
        }

        $this->notificateUsers($opportunity->id);

        $this->json(['success' => true]);
    }

     /**
     * @throws WorkflowRequest
     * @throws PermissionDenied
     */
    public static function notificateUsers(int $opportunityId, bool $verifyPublishingOpinions = true): bool
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

        $registrations = $app->repo('Registration')->matching($criteria);

        self::sendToMailQueue($registrations);

        $app->log->debug("Processo de envio de emails enviado para a fila.");

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

    public static function getEvaluationsAvg(Registration $registration): float
    {
        $app = App::i();

        $evaluations = $app->repo(RegistrationEvaluation::class)->findBy(['registration' => $registration]);
        $evaluationsAvg = 0;
        foreach ($evaluations as $evaluation) {
            $evaluationsAvg += (float) $evaluation->result;
        }
        $evaluationsAvg /= count($evaluations); // Necessário utilizar a média das avaliações para aplicar as políticas afirmativas

        return $evaluationsAvg;
    }

}
