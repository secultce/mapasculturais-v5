<?php
namespace Diligence\Entities;

use Doctrine\ORM\Mapping as ORM;
use \MapasCulturais\App;
use \MapasCulturais\i;
use DateTime;
use Diligence\Controllers\Controller;
use MapasCulturais\Entity;
//Para uso do RabbitMQ
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Exchange\AMQPExchangeType;

use Diligence\Repositories\Diligence as DiligenceRepo;
use Diligence\Entities\Diligence as EntityDiligence;
use Diligence\Service\DiligenceInterface;
use MapasCulturais\ApiOutputs\Json;


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
    const STATUS_CLOSE = 3; // Para diligência que foi enviada para o proponente
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
     * Envia para a fila do RabbitMQ
     *
     * @param [array] $userDestination
     * @return void
     */
    static public function sendQueue($userDestination, $routeKey)
    {

        $exchange = 'router';
        $queue = 'msgs';
        $connection = new AMQPStreamConnection('rabbitmq', '5672', 'mqadmin', 'Admin123XX_', '/');
       
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
     * Metodo que retorna o total de dias para resposta do proponente para o parecerista e a data limite de resposta
     * para o proponente
     *
     * @param [datetime] $date
     * @param [object] $entity
     * @return array
     */
    static public function verifyTerm($date, $entity): array
    {
        if(isset($date) && count($date) > 0 && isset($date[0]->sendDiligence)){
            $days = $entity->opportunity->getMetadata('diligence_days');
            $daysAdd = '+'.$days.' day';
            $term = $date[0]->sendDiligence->modify($daysAdd);
            //Verificando se a data e hora atual é menor que o prazo
            if(new DateTime() <= $term )
            {
                return [
                    'term' => $term,
                    'verify' => true
                ];
            }
        }
        
        return [
            'term' => null,
            'verify' => false
        ];
    }

    /**
     * Verifica se quem está logado é o mesmo agente que foi aberto a diligência
     *
     * @param [object] $entity
     * @param [object] $diligenceAgentId
     * @param [array] $term
     * @return void
     */
    static public function infoTerm($entity, $diligenceAgentId, $term ): void
    {
        $app = App::i();
       
        if(isset($diligenceAgentId[0]) && count($diligenceAgentId) > 0){
            if(
                ($app->user->profile->id == $diligenceAgentId[0]->agent->id) && !is_null($term['term'])
            ){               
                i::_e('Vocẽ tem até ' .
                $term['term']->format('d/m/Y H:i') .
                ' para responder a diligência.');
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

    public function create($class)
    {
        App::i()->applyHook('entity(diligence).createDiligence:before');
        //Buscando informações do agente e da inscrição
        $regs = DiligenceRepo::getRegistrationAgentOpenAndAgent(
            $class->data['registration'],
            $class->data['openAgent'],
            $class->data['agent']
        );
       
        //Se tiver registro de diligência
        $diligenceRepository = DiligenceRepo::findBy('Diligence\Entities\Diligence', ['registration' => $class->data['registration']]);
      
        if(count($diligenceRepository) > 0) {
            return self::updateContent($diligenceRepository, $class->data['description'], $regs['reg'], $class->data['status']);
        }
        //Instanciando para gravar no banco de dados
        $diligence = new EntityDiligence;
        $diligence->registration    = $regs['reg'];
        $diligence->openAgent       = $regs['openAgent'];
        $diligence->agent           = $regs['agent'];
        $diligence->createTimestamp =  new DateTime();
        $diligence->description     = $class->data['description'];   
        $diligence->status          = $class->data['status'];
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
    protected function updateContent($diligences, $description, $registration, $status = 0)
    {
        $save = null;
        foreach ($diligences as $diligence) {
            $diligence->description     = $description;
            $diligence->registration    = $registration;
            $diligence->createTimestamp =  new DateTime();
            $diligence->status          = $status;
            //Se for para enviar a diligência, então salva o momento do envio
            if($status == 3){
                $diligence->sendDiligence =  new DateTime();
            }

            $save = self::saveEntity($diligence);
        }
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

    static public function evaluationSend($entity)
    {
        if($entity->opportunity->isUserEvaluationsSent())
        {
            return true;
        }

        return false;
    }

}