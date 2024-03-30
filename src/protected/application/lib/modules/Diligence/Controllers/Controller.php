<?php
namespace Diligence\Controllers;

require dirname(__DIR__).'/Entities/Diligence.php';
require dirname(__DIR__).'/Repositories/Diligence.php';

use DateTime;
use \MapasCulturais\App;
use MapasCulturais\Entities\Notification;
use Diligence\Repositories\Diligence as DiligenceRepo;
use Diligence\Entities\Diligence as EntityDiligence;
// use MapasCulturais\Entities\EntityRevision as Revision;
// use \MapasCulturais\Entities\EntityRevisionData;
// use MapasCulturais\Traits;
// use Recourse\Entities\Recourse as EntityRecourse;


class Controller extends \MapasCulturais\Controller{

    public function GET_index() {
        $app = App::i();
        $diligence = new EntityDiligence();
        dump($diligence);
    }
    public function POST_save()
    {      
        
        // $this->requireAuthentication();
        $app = App::i();  
        $regs = DiligenceRepo::getRegistrationAgentOpenAndAgent(
            $this->data['registration'],
            $this->data['openAgent'],
            $this->data['agent']
        );
        $openAgent = $regs['openAgent'];
        $agent = $regs['agent'];
        $userDestination = [
            'name' => $agent->name,
            'email' => $agent->user->email, 
            'number' => $regs['reg']->id,
            'days' => $regs['reg']->opportunity->getMetadata('diligence_days')
        ];
        dump($userDestination);
        EntityDiligence::sendQueue($userDestination);
        // //Se tiver registro com a inscrição passada na requisição
        // $diligenceRepository = DiligenceRepo::findBy($this->data['registration']);
        // if(count($diligenceRepository) > 0) {
        //     self::updateContent($diligenceRepository, $this->data['description'], $regs['reg'], $this->data['status']);
        // }
        // //Instanciando para gravar no banco de dados
        // $diligence = new EntityDiligence;
        // $diligence->registration    = $regs['reg'];
        // $diligence->openAgent       = $openAgent;
        // $diligence->agent           = $agent;
        // $diligence->createTimestamp =  new DateTime();
        // $diligence->description     = $this->data['description'];
        // $diligence->status          = $this->data['status'];  
       
        // $app->em->persist($diligence);
        // $app->em->flush();
        // $app->disableAccessControl();
        // $save = $diligence->save(true);
        // $app->enableAccessControl();
        // self::returnJson($save);  
    }

     /**
     * Busca o conteúdo de uma diligencia salva ou enviada
     *
     * @return void
     */
    public function GET_getcontent() : string
    {
        //ID é o número da inscrição
        if(isset($this->data['id'])){
            //Repositorio da Diligencia
            $diligence = DiligenceRepo::findBy($this->data['id']);
            $this->json(['data' =>$diligence[0], 'status' => 200], 200);
        }
        //Validação caso nao tenha a inscrição na URL
        $this->json(['message' => 'Falta a inscrição', 'status' => 'error'], 400);
    }
    /**
     * Metodo para alterar o valor do conteudo da mensagem da Diligencia
     *
     * @param [object] $diligences
     * @param [string] $description
     * @param [object] $registration
     * @param [int] $status
     * @return void
     */
    public function updateContent($diligences, $description, $registration, $status = 0) : void
    {
        // $this->requireAuthentication();
        $app = App::i();
        $save = null;
       
        // foreach ($diligences as $diligence) {
        //     $diligence->description      = $description;
        //     $diligence->registration    = $registration;
        //     $diligence->createTimestamp =  new DateTime();
        //     $diligence->description     = $this->data['description'];
        //     $diligence->status          = $status;
        //     $app->em->persist($diligence);
        //     $app->em->flush();
        //     $app->disableAccessControl();
        //     $save = $diligence->save();
        //     $app->enableAccessControl();
        // }        
        if($status == 3){
            self::sendNotification();
        }
        // self::returnJson($save);
    }

    public function returnJson($instance)
    {
        if(is_null($instance)){
            // EntityDiligence::sendQueue($userDestination);
            $this->json(['message' => 'success', 'status' => 200], 200);
        }else{
            $this->json(['message' => 'Error: ', 'status' => 400], 400);
        }    
    }

    public function sendNotification()
    {
        $app = App::i();
        $url = $app->createUrl('inscricao', $this->data['registration']);
        $numberRegis = '<a href="'.$url.'">'.$this->data['registration'].'</a>';
        $message = 'Um parecerista abriu uma diligência para você responder na inscrição de número: '.$numberRegis;
       
        $regs = DiligenceRepo::getRegistrationAgentOpenAndAgent(
            $this->data['registration'],
            $this->data['openAgent'],
            $this->data['agent']
        );
        $notification = new Notification;
        $notification->message = $message;
        $notification->user = $regs['agent']->user;
        $app->disableAccessControl();
        $notification->save(true);
        $app->enableAccessControl();
    }
}