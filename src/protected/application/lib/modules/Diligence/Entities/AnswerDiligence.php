<?php
namespace Diligence\Entities;

use Doctrine\ORM\Mapping as ORM;
use \MapasCulturais\App;
use \MapasCulturais\i;
use DateTime;
use MapasCulturais\Entity;
//Para uso do RabbitMQ

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Exchange\AMQPExchangeType;

/**
 * AnswerDiligence 
 * 
 * @ORM\Table(name="answer_diligence")
 * @ORM\Entity
 * @ORM\entity(repositoryClass="MapasCulturais\Repository")
 */

class AnswerDiligence extends \MapasCulturais\Entity {

    const STATUS_OPEN = 2; // Para diligencias que está em aberto
    const STATUS_SEND = 3; // Para diligência que foi enviada para o proponente
    const STATUS_ANSWERED = 4; // Para diligências que foi respondido pelo proponente

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="answer_diligence_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;

     /**
     * @var \Diligence\Entities\Diligence
     *
     * @ORM\ManyToOne(targetEntity="Diligence\Entities\Diligence")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="diligence_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    protected $diligence;

     /**
     * @var string
     *
     * @ORM\Column(name="answer", type="text", nullable=false)
     */
    protected $answer;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_timestamp", type="datetime", nullable=false)
     */
    protected $createTimestamp;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    protected $status = Entity::STATUS_DRAFT;

}