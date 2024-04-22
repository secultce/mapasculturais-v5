<?php
namespace Diligence\Repositories;

use MapasCulturais\App;
use DateTime;
use Diligence\Entities\Diligence as DiligenceEntity;
use Doctrine\ORM\EntityRepository;
use MapasCulturais\Entity;

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

    public function findId($diligence): object
    {
        $app = App::i();  
        return $app->em->getRepository('Diligence\Entities\Diligence')->find($diligence);
       
    }

    /**
     * Retorna resposta e diligencia
     *
     * @param [int] $registration
     */
    static public function getDiligenceAnswer($registration)
    {
        // $this->requireAuthentication();
        $app = App::i();
        //Verificando se tem resposta para se relacionar a diligencia
        $dql = "SELECT ad, d
        FROM  Diligence\Entities\AnswerDiligence ad 
        LEFT JOIN  ad.diligence d 
        WHERE d.registration = :reg";
        $registrations = self::queryDiligente($app, $dql, $registration);
       
        //Se não tiver resposta de alguma diligencia então envia somente a diligencia
        if(!empty($registrations)){
            return $registrations;
        }else{
            $dql = "SELECT d
            FROM  Diligence\Entities\Diligence d
            WHERE d.registration = :reg";
            return self::queryDiligente($app, $dql, $registration);            
        }        
    }
    /**
     * Função que gera a execulta o resultado Doctrine DQL
     *
     * @param [object] $app
     * @param [string] $dql
     * @param [int] $registration
     */
    protected static function queryDiligente($app, $dql, $registration)
    {
        $query = $app->em->createQuery($dql)->setParameters(['reg' => $registration]);

        return $query->getResult();
    }

    static function getAuthorizedProject($registration): array
    {
        $app = App::i();  
        $reg = $app->repo('Registration')->find($registration);
        $optionAuthorized = $reg->getMetadata('option_authorized');
        $valueAuthorized = $reg->getMetadata('value_project_diligence');
        return [
            'optionAuthorized' => $optionAuthorized,
            'valueAuthorized' =>  $valueAuthorized
        ];
    }

}