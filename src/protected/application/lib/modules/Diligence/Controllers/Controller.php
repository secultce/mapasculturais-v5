<?php
namespace Diligence\Controllers;

use \MapasCulturais\App;
use Diligence\Entities\AnswerDiligence;
use Diligence\Service\NotificationInterface;
use Diligence\Entities\NotificationDiligence;
use MapasCulturais\Entities\RegistrationMeta;
use Diligence\Entities\Diligence as EntityDiligence;
use Diligence\Repositories\Diligence as DiligenceRepo;

class Controller extends \MapasCulturais\Controller implements NotificationInterface {

    use \Diligence\Traits\DiligenceSingle;
    use \MapasCulturais\Traits\ControllerUploads;

    const NOT_DILIGENCE     = 'sem_diligencia';
    const ONLY_DILIGENCE    = 'diligencia_aberta';
    const WITH_ANSWER       = 'resposta_rascunho';
    const ANSWER_SEND       = 'resposta_enviada';

    /**
     * Salva uma diligencia
     *
     * @return void
     */
    public function POST_save() : void
    {      
        // $this->requireAuthentication();
        $answer = new EntityDiligence();
        $entity = $answer->create($this);
        self::returnJson($entity, $this);
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
            $diligence = DiligenceRepo::getDiligenceAnswer($this->data['id']);
            $content = 0;
            foreach ($diligence as $key => $value) {
            
                //Verificando se existe diligencia
                if($value instanceof \Diligence\Entities\Diligence && $value->status >= 0)
                {
                    $content = 1;
                }
                if($value instanceof \Diligence\Entities\AnswerDiligence && $value->status == 0)
                {
                    $content = 2;
                }
                if($value instanceof \Diligence\Entities\AnswerDiligence && $value->status == 3)
                {
                    $content = 3;
                }
                
            }
        
            switch ($content) {
                case 0:
                    $this->json(['message' => self::NOT_DILIGENCE, 'data' =>$diligence, 'status' => 200], 200);
                    break;
                case 1:
                    $this->json(['message' => self::ONLY_DILIGENCE, 'data' =>$diligence, 'status' => 200], 200);
                    break; 
                case 2:
                    $this->json(['message' => self::WITH_ANSWER, 'data' =>$diligence, 'status' => 200], 200);
                    break; 
                case 3:
                    $this->json(['message' => self::ANSWER_SEND, 'data' =>$diligence, 'status' => 200], 200);
                    break; 
                                  
                default:
                    # code...
                    break;
            }
           
        }
        //Passando para o hook o conteúdo da instancia diligencia
        App::i()->applyHook('controller(diligence).getContent', [&$diligence]);
        //Validação caso nao tenha a inscrição na URL
        $this->json(['message' => 'Falta a inscrição', 'status' => 'error'], 400);
    }
    
    /**
     * Metodo da interface para notificação
     *
     * @return void
     */
    public function notification()
    {
        $this->requireAuthentication();
        App::i()->applyHook('controller(diligence).notification:before');
        //Notificação no Mapa Cultural
        $notification = new NotificationDiligence();
        $notification->create($this);        

        $userDestination = $notification->userDestination($this);
        App::i()->applyHook('controller(diligence).notification:after');
        //Enviando para fila RabbitMQ
        EntityDiligence::sendQueue($userDestination, 'proponente');
    }

    public function POST_sendNotification()
    {
        self::notification();
    }
    /**
     * Rsposta do proponente
     *
     * @return void
     */
    public function POST_answer() : void
    {
        if($this->data['answer'] == ''){
            $this->errorJson(['message' => 'O Campo de resposta deve está preenchido'], 400);
        }
        $this->requireAuthentication();
        $answer = new AnswerDiligence();
        $entity = $answer->create($this);
        self::returnJson($entity, $this);
    }
    
    /**
     * Altera o status da diligencia, retornando para rascunho
     *
     * @return void
     */
    public function PUT_cancelsend() : void
    {
        $this->requireAuthentication();
        $cancel = new EntityDiligence();
        $cancel->cancel($this);        
    }

    public function POST_notifiAnswer()
    {
        $app = App::i();
        $dili = $app->repo('\Diligence\Entities\Diligence')->findBy(['registration' => $this->data['registration']]);
        $userDestination = [];
        foreach ($dili as $diligence) {
          $userDestination = [
            'registration' => $this->data['registration'],
            'comission' => $diligence->openAgent->user->email,
            'owner' => $diligence->registration->opportunity->owner->user->email
        ];
        }       
        EntityDiligence::sendQueue($userDestination, 'resposta');
    }

    /**
     * Altera o status da resposta, retornando para rascunho
     *
     * @return void
     */
    public function PUT_cancelsendAnswer()
    {
        $this->requireAuthentication();
        $cancel = new AnswerDiligence();
        $cancel->cancel($this);
    }

    public function POST_valueProject()
    {   
        // $this->requireAuthentication();
        $app = App::i();

        $request = array_keys($this->postData);
        $regMeta =[];
        $idEntity = $this->postData['entity'];
        $reg = App::i()->repo('Registration')->find($idEntity);
        $createMetadata = null;
        $regMeta = $app->repo('RegistrationMeta')->findBy([
            'owner' => $idEntity
        ]);
        foreach ($this->postData as $keyRequest => $meta) {
           
            // dump($keyRequest, $meta,$this->postData['entity']);
            // dump($this->data[$meta]);
           
            //'key' => $key, 'value'=>  $meta, 
            // dump($regMeta);
            if(empty($regMeta)){
                $createMeta = self::authorizedProject($reg, $keyRequest, $meta);
                $entity = self::saveEntity($createMeta);
            }

            foreach ($regMeta as $key => $value) {
             
                //option_authorized
                //Se já existe dados cadastrados, então substitui por um valor novo
                if($value->key == $keyRequest)
                {
                    $value->value = $meta;
                    $entity = self::saveEntity($value);
                }
               
                // $entity = self::saveEntity($value);
                // if($value->key == 'value_project_diligence') {
                //     $value->value = $meta;
                    
                // }
                // $entity = self::saveEntity($value);
                // self::returnJson($entity, $this);
            }
           
          
            $createMetadata = $app->repo('RegistrationMeta')->findBy([
               'key' => $keyRequest, 'owner' => $idEntity
            ]);
            dump($createMetadata);
            if(empty($createMetadata)) {
                $createMeta = self::authorizedProject($reg, $keyRequest, $meta);
                $entity = self::saveEntity($createMeta);
            }
        }
     
        self::returnJson($entity, $this);

    }

    protected function authorizedProject($entity, $key, $value): object
    {
        $metaData = new RegistrationMeta();
        $metaData->key = $key;
        $metaData->value = $value;
        $metaData->owner = $entity;
        return $metaData;
    }

    public function GET_getAuthorizedProject()
    {
        $authorized = DiligenceRepo::getAuthorizedProject($this->data['id']);
        $this->json([
            'optionAuthorized' => $authorized['optionAuthorized'] , 
            'valueAuthorized' => $authorized['valueAuthorized']
        ]);
    }

    /**
     * Excluir arquivos da diligência
     *
     * @return boolean
     */
    public function GET_deleteFile() : bool
    {
        $app = App::i();
        $conn = $app->em->getConnection();
        //Verificando se existe na rota esse indice
        if(isset($this->data[1]))
        {
            $entity = $app->repo('Registration')->find($this->data[1]);
            //Verificando se o dono da inscrição é o mesmo usuario logado
            if($entity->getOwnerUser() == $app->getUser())
            {
                $query = $conn->executeQuery('DELETE FROM file WHERE id = '.$this->data['id']);
                $result = $query->execute();
                if($result)
                {
                    self::returnJson(null, $this);
                }
            }           
        }        
        $this->errorJson(['message' => 'Erro Inexperado', 'status' => 400], 400);
    }
}