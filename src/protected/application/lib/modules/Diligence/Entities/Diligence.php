<?php

namespace Diligence\Entities;

use DateTime;
use MapasCulturais\i;
use MapasCulturais\App;
use MapasCulturais\Entity;
use Doctrine\ORM\Mapping as ORM;
use MapasCulturais\ApiOutputs\Json;
//Para uso do RabbitMQ
use PhpAmqpLib\Message\AMQPMessage;
use Diligence\Controllers\Controller;
use Diligence\Service\DiligenceInterface;
use MapasCulturais\Entities\Registration;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use MapasCulturais\Entities\RegistrationMeta;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use Diligence\Entities\Diligence as EntityDiligence;
use Diligence\Repositories\Diligence as DiligenceRepo;

/**
 * Diligence 
 * 
 * @ORM\Table(name="diligence")
 * @ORM\Entity
 * @ORM\entity(repositoryClass="MapasCulturais\Repository")
 */

class Diligence extends \MapasCulturais\Entity implements DiligenceInterface
{
    use \Diligence\Traits\DiligenceSingle;

    const STATUS_OPEN = 2; // Para diligencias que está em aberto
    const STATUS_SEND = 3; // Para diligência que foi enviada para o proponente
    const STATUS_ANSWERED = 4; // Para diligências que foi respondido pelo proponente

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="diligence_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;

    /**
     * @var \MapasCulturais\Entities\Registration
     *
     * @ORM\ManyToOne(targetEntity="MapasCulturais\Entities\Registration")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="registration_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    protected $registration;

    /**
     * Agente que abrirá a diligência
     * @var \MapasCulturais\Entities\Agent
     *
     * @ORM\ManyToOne(targetEntity="MapasCulturais\Entities\Agent")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="open_agent_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    protected $openAgent;


    /**
     * Agente que receberá a diligência
     * @var \MapasCulturais\Entities\Agent
     *
     * @ORM\ManyToOne(targetEntity="MapasCulturais\Entities\Agent")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="agent_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    protected $agent;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_timestamp", type="datetime", nullable=false)
     */
    protected $createTimestamp;


    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=false)
     */
    protected $description;


    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    protected $status = Entity::STATUS_DRAFT;
   
    /**
     * @var integer
     *
     * @ORM\Column(name="situation", type="integer", nullable=false)
     */
    protected $situation = self::STATUS_OPEN;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="send_diligence", type="datetime", nullable=false)
     */
    protected $sendDiligence;

    /**
     * @var \Diligence\Entities\AnswerDiligence
     *
     * @ORM\OneToOne(targetEntity="Diligence\Entities\AnswerDiligence", mappedBy="diligence")
     */
    protected $answer;

    /**
     * @var \Diligence\Entities\DiligenceFile[] Files
     *
     * @ORM\OneToMany(targetEntity="Diligence\Entities\DiligenceFile", mappedBy="owner", cascade="remove", orphanRemoval=true)
     * @ORM\JoinColumn(name="id", referencedColumnName="object_id", onDelete="CASCADE")
    */
    protected $files;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="text", nullable=false)
     */
    protected $subject;

    /**
     * Envia para a fila do RabbitMQ
     *
     * @param [array] $userDestination
     * @return void
     */
    static public function sendQueue($userDestination, $routeKey)
    {
        $exchange = 'router';
        $queue = 'msgs';
        $connection = new AMQPStreamConnection($_ENV['RABBITMQ_HOST'], $_ENV['RABBITMQ_PORT'], $_ENV['RABBITMQ_USER'], $_ENV['RABBITMQ_PASSWORD'], $_ENV['RABBITMQ_VHOST']);
       
        $channel = $connection->channel();
        $channel->queue_declare($queue, false, true, false, false);
    
        $channel->exchange_declare($exchange, AMQPExchangeType::DIRECT, false, true, false);
        $channel->queue_bind($queue, $exchange);

       
        $messageBody = json_encode($userDestination);
        $message = new AMQPMessage($messageBody, array('content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
        $channel->basic_publish($message, $exchange, $routeKey);

        $channel->close();
        $connection->close();

    }

    /**
     * Verifica se quem está logado é o mesmo agente que foi aberto a diligência
     *
     * @param [object]  $entity
     * @param [object]  $diligenceAgentId
     * @param [date]    $diligenceDays
     * @return void
     */
    static public function infoTerm($entity, $diligenceAgentId, $diligenceDays ): void
    {
        $app = App::i();

        if(isset($diligenceAgentId[0]) && count($diligenceAgentId) > 0){
            if(
                ($app->user->profile->id == $diligenceAgentId[0]->agent->id) && 
                new DateTime() <= $diligenceDays
            ){
                $simpleMsg = "Você tem até {$diligenceDays->format('d/m/Y H:i')} para responder essa diligência";
                $multiMsg = "Uma interação de diligência foi aberta e você tem até {$diligenceDays->format('d/m/Y H:i')} para responder";

                $entity->opportunity->use_multiple_diligence == 'Sim' ? i::_e($multiMsg) : i::_e($simpleMsg);
            }else{
                if($diligenceAgentId[0]->sendDiligence <= new DateTime() && !$entity->canUser('evaluate')){
                    i::_e('Desculpe, mas o prazo para responder está encerrado.');
                }else{
                    i::_e('O Proponente tem apenas ' .
                    $entity->opportunity->getMetadata('diligence_days') .
                    ' dias para responder essa diligência.');
                }
            }
        }
       
    }

    /**
     * Verifica se o agente logado é o que deve responder a diligência
     *
     * @param [array] $diligenceAgentId
     * @return boolean
     */
    static public function isProponent($diligenceAgentId, $entity) : bool
    {
        $app = App::i();
        //Em caso de não ter diligencia aberta, então verifica o dono da inscrição com o usuario logado
        if(empty($diligenceAgentId)){
            if($entity->getOwnerUser() == $app->user)
            {
                return true;
            }
        }

        if(isset($diligenceAgentId[0]) && count($diligenceAgentId) > 0){
            if($app->user->profile->id == $diligenceAgentId[0]->agent->id){
                return true;
            }
        }
        return false;
    }

    static public function isEvaluate($entity, $user)
    {
        $evaluation = $entity->getUserEvaluation($user);
      
        if(!is_null($evaluation) && $evaluation->user->id == $user->id)
        {
            return true;
        }
        return false;
    }

    public function createOrUpdate($class)
    {
        App::i()->applyHook('entity(diligence).createDiligence:before');
        //Buscando informações do agente e da inscrição
        $newDiligenceData = DiligenceRepo::getRegistrationAgentOpenAndAgent(
            $class->data['registration'],
            $class->data['openAgent'],
            $class->data['agent']
        );

        if(isset($class->data['idDiligence']) && $class->data['idDiligence'] > 0){
             //Se tiver registro de diligência
            $diligenceRepository = App::i()->repo('Diligence\Entities\Diligence')->find($class->data['idDiligence']);
            return self::updateContent(
                $diligenceRepository,
                $class->data['description'], 
                $newDiligenceData['reg'],
                $class->data['status'],
                json_encode($class->data['subject'])
            );
        }
      

        //Instanciando para gravar no banco de dados
        $diligence = new EntityDiligence;
        $diligence->registration    = $newDiligenceData['reg'];
        $diligence->openAgent       = $newDiligenceData['openAgent'];
        $diligence->agent           = $newDiligenceData['agent'];
        $diligence->createTimestamp = new DateTime();
        $diligence->description     = $class->data['description'];
        $diligence->status          = $class->data['status'];
        $diligence->subject         = json_encode($class->data['subject']);
        //Considerando que será um envio
        if($class->data['status'] == "3"){
            $diligence->sendDiligence = new DateTime();
        }
        App::i()->applyHook('entity(diligence).createDiligence:after', [&$diligence]);
        return self::saveEntity($diligence);
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
    protected function updateContent($diligences, $description, $registration, $status = 0, $subject)
    {
        $save = null;
        $diligences->description     = $description;
        $diligences->registration    = $registration;
        $diligences->createTimestamp =  new DateTime();
        $diligences->status          = $status;
        $diligences->subject         = $subject;
        //Se for para enviar a diligência, então salva o momento do envio
        if($status == 3){
           $diligences->sendDiligence =  new DateTime();
        }

        $save = self::saveEntity($diligences);
        return $save;
    }

    public function cancel(Controller $class) : Json
    {
        $app =  App::i();
        $dili = $app->repo('\Diligence\Entities\Diligence')->findBy( ['registration' => $class->data['registration']]);
        $save = null;
        foreach ($dili as $diligence) {
            $diligence->status  = 0;
            self::saveEntity($diligence);     
        }
      
        if($save == null){
            return $class->json(['message' => 'success', 'status' => 200], 200);
        }
        return $class->json(['message' => 'error', 'status' => 400], 400);
    }

    static public function evaluationSend($entity) : bool
    {
        if($entity->opportunity->isUserEvaluationsSent())
        {
            return true;
        }
        return false;
    }

    public function getStatusLabel(): ?string
    {
        switch ($this->status):
            case 0:
            case 2:
                return \MapasCulturais\i::_e('Rascunho');
            case 3:
                return \MapasCulturais\i::_e('Enviado ao proponente');
            case 4:
                return \MapasCulturais\i::_e('Respondido');
            default:
                throw new \Exception('Invalid status');
        endswitch;
    }

    public function jsonSerialize()
    {
        $preSerialized = parent::jsonSerialize();
        $serialized = $preSerialized;
        $serialized['registration'] = $preSerialized['registration']->id;
        $serialized['openAgent'] = [
            'id' => $preSerialized['openAgent']->id,
            'name' => $preSerialized['openAgent']->name,
            'singleUrl' => $preSerialized['openAgent']->singleUrl,
        ];
        $serialized['agent'] = [
            'id' => $preSerialized['agent']->id,
            'name' => $preSerialized['agent']->name,
            'singleUrl' => $preSerialized['agent']->singleUrl,
        ];
        // Verifica se existe uma resposta. Caso não, atribui 'null'
        $serialized['answer'] = $preSerialized['answer'] ? [
            'id' => $preSerialized['answer']->id,
            'answer' => $preSerialized['answer']->answer,
            'createTimestamp' => $preSerialized['answer']->createTimestamp,
            'status' => $preSerialized['answer']->status,
        ] : null;
        return  $serialized;
    }

    /**
     * Função para retornar o dado do campo de assunto
     * @return mixed
     */
    public function getSubject()
    {
        //Se string para array
        $subject  = json_decode($this->subject, true);
        if(!is_null($subject))
        {
            $retSubject = [];
            //Tratando os termos para o usuário
            foreach ($subject as $item) {

                if($item == "subject_exec_physical")
                {
                    array_push($retSubject, "Execução Física do Objeto");
                }
                if($item == "subject_report_finance")
                {
                    array_push($retSubject, "Relatório Financeiro");
                }
            }
            //Tratando a forma de escrita
            return implode(', ', $retSubject). '.';
        };
    }

    /**
     * Retornando o assunto para usar como array
     * @return mixed
     */
    public function subjectToArray()
    {
        return json_decode($this->subject);
    }

    /**
     * Metodo que retorna a confirmação do assunto marcado
     * @param $subjectReplace
     * @return string[]
     */
    public function getCheckSubject($subjectReplace)
    {
        $checkPhysical = '';
        $checkFinance = '';
        if($subjectReplace[0] == 'subject_exec_physical'){
            $checkPhysical = 'checked';
        }
        if($subjectReplace[0] == 'subject_report_finance')
        {
            $checkFinance = 'checked';
        }elseif (isset($subjectReplace[1]) && $subjectReplace[1] == 'subject_report_finance')
        {
            $checkFinance = 'checked';
        }
        return ['checkPhysical' => $checkPhysical, 'checkFinance' => $checkFinance];
    }
    //Cria um objeto Matadata para salvar na inscrição
    static public function createSituacionMetadata($request, Registration $registration) : RegistrationMeta
    {
        $metaData = new RegistrationMeta();
        $metaData->key = 'situacion_diligence';
        $metaData->value = $request->data['situacion'];
        $metaData->owner = $registration;
        return $metaData;
    }


}
