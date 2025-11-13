<?php
namespace CounterReason\Repositories;

use DateTime;
use MapasCulturais\Entities\Agent;
use CounterReason\Entities\CounterReason;
use MapasCulturais\Entities\Registration;

class CounterReasonRepository extends \MapasCulturais\Repository
{
    static public function getCounterReason(Registration $registration, $app)
    {
        return $app->repo(CounterReason::class)->findOneBy(['registration' => $registration]);
    }

    static public function getCounterReasonByAgent(Agent $agent, $app): array
    {
        return $app->repo(CounterReason::class)->findBy(['agent' => $agent]);
    }

    /**
     * Verifica o período para habilitar o botão de submeter a contrarrazão
     * @param mixed $entity
     * @return bool
     */
    static public function validatePeriodCounterReason($entity): bool
    {
        $strToInitial = $entity->opportunity->getMetadata('counterReason_date_initial') . ' ' . $entity->opportunity->getMetadata('counterReason_time_initial');
        $initialOfPeriod = \DateTime::createFromFormat('Y-m-d H:i', $strToInitial);
        $strToEnd = $entity->opportunity->getMetadata('counterReason_date_end') . ' ' . $entity->opportunity->getMetadata('counterReason_time_end');
        $endOfPeriod = \DateTime::createFromFormat('Y-m-d H:i', $strToEnd);
        $now = new DateTime();
        if ($now >= $initialOfPeriod &&  $now <= $endOfPeriod)
            return true;

        return false;
    }
}