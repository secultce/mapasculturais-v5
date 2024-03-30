<?php
namespace Diligence\Repositories;

use MapasCulturais\App;
use DateTime;
use Diligence\Entities\Diligence as DiligenceEntity;


class Diligence{

    static public function findBy($id): array
    {
        $app = App::i();  
        $diligence = $app->em->getRepository('Diligence\Entities\Diligence')->findBy(['registration' => $id]);
       
        if($diligence > 0){
            return $diligence;
        }
        return $diligence;
    }
    
    static public function getRegistrationAgentOpenAndAgent($number, $agentOpen, $agent): array
    {
        $app = App::i();  
        $reg = $app->repo('Registration')->find($number);
        $openAgent = $app->repo('Agent')->find($agentOpen);
        $agent = $app->repo('Agent')->find($agent);

        return ['reg' => $reg, 'openAgent' => $openAgent, 'agent' => $agent];
    }

}