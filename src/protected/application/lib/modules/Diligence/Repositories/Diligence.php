<?php
namespace Diligence\Repositories;

use Diligence\Entities\Tado;
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
     */
    public static function getDiligenceAnswer(int $registration, bool $sentDiligence = false, bool $sentAnswer = false): ?array
    {
        $registrationAnswer = $registration;
        $app = App::i();

        //Verificando se tem resposta para se relacionar a diligencia
        $dql = "SELECT ad, d
        FROM Diligence\Entities\Diligence d
            LEFT JOIN Diligence\Entities\AnswerDiligence ad
                WITH ad.diligence = d AND ad.registration = :regAnswer AND ad.status >= :statusAnswer
        WHERE d.registration = :reg
            AND d.status >= :statusDiligence
        ORDER BY d.sendDiligence DESC, ad.createTimestamp DESC";


        $query = $app->em->createQuery($dql)
            ->setParameters([
                'reg' => $registration,
                'regAnswer' =>  $registrationAnswer,
                'statusAnswer' => $sentAnswer ? 1 : 0,
                'statusDiligence' => $sentDiligence ? 1 : 0,
            ]);
        $diligenceAndAnswers = $query->getResult();

        if (empty($diligenceAndAnswers)) {
            return null;
        }
        return $diligenceAndAnswers;
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

    public static function getTado($registratrion): ?Tado
    {
        $app = App::i();
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

    /**
     * Função que busca se tem registro do status do PC e retorna o valor para preenchimento
     * do select na view
     */
    public function getSituacionPC(Registration $registration) : string
    {
        $app = App::i();
        $entity = $app->repo('Registration')->find($registration->id);
        //Se não tiver metadata retorna falso
        return !is_null($entity->getMetadata('situacion_diligence')) ? $entity->getMetadata('situacion_diligence') : 'all';
    }

    public static function getFinancialReportsAccountability($registration_id)
    {
        $app = App::i();

        $result = $app->repo('RegistrationFile')->findBy([
            'owner' => $registration_id,
            'group' => 'financial-report-accountability'
        ]);

        return $result;
    }

    /**
     * Verifica se quem está logado tem controle na opp. e se é o fiscal de uma diligência
     * @param $registration
     * @return DiligenceEntity|null
     */
    public static function getIsAuditor($registration)
    {
        $app = App::i();
        $auditorDiligence = $app->repo(DiligenceEntity::class)->findOneBy(['registration' => $registration], ['id' => 'desc']);

        $isAdmin = $auditorDiligence->registration->opportunity->canUser("@control", $app->user);
        if($auditorDiligence->openAgent->userId !== $app->user->id && !$isAdmin) {
            $app->setCookie("denied-auditor", 'Esse monitoramento já está sendo acompanhado por outro Fiscal', time()+3600);;
            $app->redirect($app->createUrl('oportunidade', $auditorDiligence->registration->opportunity->id));
        }
        return $auditorDiligence;
    }

}
