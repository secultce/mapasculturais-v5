<?php
namespace CounterReason\Repositories;

use CounterReason\Entities\CounterReason;
use MapasCulturais\Entities\Agent;
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
}
