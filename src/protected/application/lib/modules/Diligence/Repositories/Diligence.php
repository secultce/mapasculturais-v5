<?php
namespace Diligence\Repositories;

use MapasCulturais\App;
use Diligence\Entities\Diligence as DiligenceEntity;
use MapasCulturais\Entities\Registration;

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
        $registrationAnswer = $registration;
        $app = App::i();
        //Verificando se tem resposta para se relacionar a diligencia
        $dql = "SELECT ad, d
        FROM  Diligence\Entities\Diligence d 
        LEFT JOIN  Diligence\Entities\AnswerDiligence ad WITH ad.diligence = d AND ad.registration = :regAnswer
        WHERE d.registration = :reg ORDER BY d.sendDiligence DESC , ad.createTimestamp DESC" ;

        $registrations = self::queryDiligente($app, $dql, $registration, $registrationAnswer);
        //Se não tiver resposta de alguma diligencia então envia somente a diligencia
        if(!empty($registrations)){
            return $registrations;
        }else{
            $dql = "SELECT d
            FROM  Diligence\Entities\Diligence d
            WHERE d.registration = :reg";
            return self::queryDiligente($app, $dql, $registration, $registrationAnswer);            
        }
    }
    /**
     * Função que gera a execulta o resultado Doctrine DQL
     *
     * @param [object] $app
     * @param [string] $dql
     * @param [int] $registration
     */
    protected static function queryDiligente($app, $dql, $registration, $registrationAnswer)
    {
        try {
            $query = $app->em->createQuery($dql)->setParameters(['reg' => $registration, 'regAnswer' =>  $registrationAnswer]);
            return $query->getResult();
        } catch (\Throwable $th) {
           return null;
        }
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

    static public function getFilesDiligence($diligence): array
    {
        $app = App::i();
        $params = [
            "object_type" => "Diligence\Entities\Diligence",
            "object_id" => $diligence,
            "grp" => "answer-diligence"
        ];

        $query = "SELECT * FROM file WHERE object_type = :object_type and object_id = :object_id and grp = :grp";
        $conn = $app->em->getConnection();
        $result = $conn->fetchAllAssociative($query, $params);
        return $result;
    }

    public function getTado($registratrion)
    {
        $app = App::i();  
        //Buscando o tado gerado
        $tado = $app->repo('Diligence\Entities\Tado')->findOneBy([
            'registration' => $registratrion
        ]);
        return $tado;
    }

    /**
     * Buscando a ultima diligência relacionado a inscrição Desejada
     *
     * @param [int|string] $registration
     * @return DiligenceEntity
     */
    public function getIdLastDiligence($registration) : DiligenceEntity
    {
        $app = App::i();
        $lastDiligence = $app->repo(DiligenceEntity::class)->findOneBy(['registration' => $registration], ['id' => 'desc']);
        return $lastDiligence;
    }

    //Verifica se tem acesso ao relatório financeiro da PC
    public function verifyAcessReport(Registration $registration): bool
    {
        $app = App::i();
        
        $user = $app->user;
        $hasAccess = false;
        //Se o usuário logado tem permissão de avaliador da inscrição
        if($registration->canUser('evaluate', $user)){
            $hasAccess = true;
        //se o usuario logado é o mesmo dono da inscrição
        }elseif( $user->id == $registration->owner->user->id ){          
            $hasAccess = true;
        //Verifica se faz parte do grupo de admin e se é o usuário logado
        }else{
            foreach ($registration->opportunity->agentRelations as $managers) {
                if(
                    $managers->group == "group-admin" && 
                    isset($managers->agent->id) && 
                    $managers->agent->id == $user->profile->id
                ){
                    $hasAccess = true;
                }
            }
        }
        // Se o usuário não tem permissão, redireciona com mensagem de erro
        if(!$hasAccess){
            $_SESSION['error'] = "Ops! Você não tem permissão para acessar esse relatório financeiro";
            $app->redirect($app->baseUrl.'panel', 403);
        }
        
        return $hasAccess;
    }

}