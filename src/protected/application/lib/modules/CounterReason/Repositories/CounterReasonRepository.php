<?php
namespace CounterReason\Repositories;

use CounterReason\Entities\CounterReason;
use MapasCulturais\Entities\Registration;

class CounterReasonRepository extends \MapasCulturais\Repository
{
    static public function getCounterReason(Registration $registration, $app)
    {
        return $app->repo(CounterReason::class)->findOneBy(['registration' => $registration]);
    }
}
