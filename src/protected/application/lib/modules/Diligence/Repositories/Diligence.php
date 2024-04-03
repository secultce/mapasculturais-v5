<?php
namespace Diligence\Repositories;

use MapasCulturais\App;
use DateTime;
use Diligence\Entities\Diligence as DiligenceEntity;


class Diligence{

    static public function findBy($className = 'Diligence\Entities\Diligence', $array): array
    {
        $app = App::i();  
        $entity = $app->em->getRepository($className)->findBy($array);
       
        if(count($entity) > 0){
            return $entity;
        }
        return $entity;
    }
    
    static public function getRegistrationAgentOpenAndAgent($number, $agentOpen, $agent): array
    {
        $app = App::i();  
        $reg = $app->repo('Registration')->find($number);
        $openAgent = $app->repo('Agent')->find($agentOpen);
        $agent = $app->repo('Agent')->find($agent);

        return ['reg' => $reg, 'openAgent' => $openAgent, 'agent' => $agent];
    }

    public function findId($diligence)
    {
        $app = App::i();  
        return $app->em->getRepository('Diligence\Entities\Diligence')->find($diligence);
       
    }

    static public function getDiligenceAnswer($registration)
    {
        // $this->requireAuthentication();
        $app = App::i();

        $dql = "SELECT d, ad
                FROM Diligence\Entities\Diligence d
                JOIN Diligence\Entities\AnswerDiligence ad WITH ad.diligence = d.id
                WHERE d.registration = :reg";
        $query = $app->em->createQuery($dql)->setParameters(['reg' => $registration]);

        $registrations = $query->getResult();

        $opportunities = array_map(function($d){
            return $d->diligence;
        }, $registrations);

        return $registrations;

    }

}