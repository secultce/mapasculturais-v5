<?php
namespace CounterReason\Repositories;
ini_set('error_reporting', E_ALL & ~E_NOTICE);
use Doctrine\ORM\EntityRepository;
use CounterReason\Entities\CounterReason;
use MapasCulturais\App;
use MapasCulturais\Entities\Registration;

class CounterReasonRepository extends \MapasCulturais\Repository
{
    static public function getCounterReason(Registration $registration, $app)
    {
        return $app->repo(CounterReason::class)->findOneBy(['registration' => $registration]);
    }
}
