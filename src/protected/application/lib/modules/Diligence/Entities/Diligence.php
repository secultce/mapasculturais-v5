<?php
namespace Diligence\Entities;

use Doctrine\ORM\Mapping as ORM;
use \MapasCulturais\App;
use \MapasCulturais\i;
use DateTime;
use MapasCulturais\Entity;
//Para uso do RabbitMQ
require_once dirname(__DIR__).'/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Exchange\AMQPExchangeType;

/**
 * Diligence 
 * 
 * @ORM\Table(name="diligence")
 * @ORM\Entity
 * @ORM\entity(repositoryClass="MapasCulturais\Repository")
 */

class Diligence extends \MapasCulturais\Entity 
{
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
     * Envia para a fila do RabbitMQ
     *
     * @param [array] $userDestination
     * @return void
     */
    static public function sendQueue($userDestination)
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
        $channel->basic_publish($message, $exchange, 'proponente');

        $channel->close();
        $connection->close();

    }

    static public function verifyTerm($date, $entity): array
    {

        if(isset($date) && count($date) > 0){
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
     * @param [int] $diligenceAgentId
     * @param [date] $term
     * @return void
     */
    static public function infoTerm($entity, $diligenceAgentId, $term ): void
    {
        $app = App::i();
        if(isset($diligenceAgentId[0]) && count($diligenceAgentId) > 0){
            if($app->user->profile->id == $diligenceAgentId[0]->agent->id){
                i::_e('Vocẽ tem até ' .
                $term['term']->format('d/m/Y H:i') .
                ' para responder a diligência.');
            }else{
                i::_e('O Proponente tem apenas ' .
                $entity->opportunity->getMetadata('diligence_days') .
                ' dias para responder essa diligência.');
            }
        }
       
    }

    /**
     * Verifica se o agente logado é o que deve responder a diligência
     *
     * @param [array] $diligenceAgentId
     * @return boolean
     */
    static public function isProponent($diligenceAgentId) : bool
    {
        $app = App::i();
        if(isset($diligenceAgentId[0]) && count($diligenceAgentId) > 0){
            if($app->user->profile->id == $diligenceAgentId[0]->agent->id){
                return true;
            }
        }
        return false;
    }

}