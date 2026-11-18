<?php

declare(strict_types=1);

namespace OpinionManagement\Services;

use Doctrine\DBAL\Connection;
use Doctrine\Common\Collections\ArrayCollection;
use MapasCulturais\App;
use MapasCulturais\Entities\Opportunity;
use MapasCulturais\Entities\Registration;
use MapasCulturais\Entities\RegistrationEvaluation;

class OpinionPublication
{
    public static function isPublished(Registration $registration): bool
    {
        return (bool) $registration->opportunity->publishedOpinions
            || (bool) $registration->getMetadata('opinionPublication');
    }

    /** @param Registration[] $registrations */
    public function states(Opportunity $opportunity, array $registrations): array
    {
        if (!$registrations) {
            return [];
        }

        $ids = array_map(function (Registration $registration) { return $registration->id; }, $registrations);
        $valuerUserIds = EvaluationReviewers::activeUserIds($opportunity);
        // The evaluations view includes assigned reviewers who have not started yet.
        $rows = $valuerUserIds ? App::i()->em->getConnection()->fetchAllAssociative(
            'SELECT registration_id, COUNT(*) AS total,
                SUM(CASE WHEN evaluation_status = :sent THEN 1 ELSE 0 END) AS sent
             FROM evaluations WHERE opportunity_id = :opportunity AND registration_id IN (:ids)
                AND valuer_user_id IN (:valuerUsers)
             GROUP BY registration_id',
            ['sent' => RegistrationEvaluation::STATUS_SENT, 'opportunity' => $opportunity->id, 'ids' => $ids, 'valuerUsers' => $valuerUserIds],
            ['ids' => Connection::PARAM_INT_ARRAY, 'valuerUsers' => Connection::PARAM_INT_ARRAY]
        ) : [];
        $counts = array_column($rows, null, 'registration_id');
        $states = [];
        foreach ($registrations as $registration) {
            $count = $counts[$registration->id] ?? ['total' => 0, 'sent' => 0];
            $published = self::isPublished($registration);
            $ready = $registration->status > 0 && (int) $count['total'] > 0
                && (int) $count['total'] === (int) $count['sent'];
            $states[$registration->id] = [
                'published' => $published,
                'ready' => !$published && $ready,
                'number' => $registration->number,
            ];
        }

        return $states;
    }

    /**
     * Validate the entire selection before saving. The opportunity lock serializes
     * repeated requests so an already published registration is not notified again.
     * @return ArrayCollection Newly published registrations
     */
    public function publish(Opportunity $opportunity, array $ids): ArrayCollection
    {
        $app = App::i();
        $connection = $app->em->getConnection();
        $connection->beginTransaction();
        try {
            $connection->executeQuery('SELECT id FROM opportunity WHERE id = ? FOR UPDATE', [$opportunity->id]);
            $app->em->refresh($opportunity);
            $registrations = $app->repo(Registration::class)->findBy(['id' => $ids, 'opportunity' => $opportunity]);
            if (count($registrations) !== count($ids)) {
                throw new \InvalidArgumentException('A seleção contém inscrições inexistentes ou de outra oportunidade.');
            }
            $states = $this->states($opportunity, $registrations);
            $pending = [];
            foreach ($states as $state) {
                if (!$state['published'] && !$state['ready']) {
                    $pending[] = $state['number'];
                }
            }
            if ($pending) {
                throw new \DomainException('Aguarde o envio de todas as avaliações das inscrições: ' . implode(', ', $pending) . '.');
            }
            $published = new ArrayCollection();
            foreach ($registrations as $registration) {
                if ($states[$registration->id]['published']) {
                    continue;
                }
                $registration->setMetadata('opinionPublication', json_encode([
                    'publishedAt' => (new \DateTimeImmutable())->format(DATE_ATOM),
                    'publishedBy' => $app->user->id,
                ]));
                // Save only metadata: submitted registration fields remain untouched.
                $registration->getMetadata('opinionPublication', true)->save(true);
                $published->add($registration);
            }
            $app->em->flush();
            $connection->commit();

            return $published;
        } catch (\Throwable $exception) {
            $connection->rollBack();
            throw $exception;
        }
    }
}
