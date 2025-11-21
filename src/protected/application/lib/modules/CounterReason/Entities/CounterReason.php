<?php
namespace CounterReason\Entities;
use Doctrine\ORM\Mapping as ORM;
/**
 * CounterReason
 *
 * @ORM\Table(name="counter_reason")
 * @ORM\Entity
 * @ORM\entity(repositoryClass="MapasCulturais\Repository")
 */
class CounterReason extends \MapasCulturais\Entity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="counter_reason_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $text;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true, name="send")
     */
    protected $send;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    protected $status = self::STATUS_ENABLED;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $reply;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true, name="date_reply")
     */
    protected $dateReply;

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
     * @var \MapasCulturais\Entities\Opportunity
     *
     * @ORM\ManyToOne(targetEntity="MapasCulturais\Entities\Opportunity")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="opportunity_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    protected $opportunity;

    /**
     * @var \MapasCulturais\Entities\Agent
     *
     * @ORM\ManyToOne(targetEntity="MapasCulturais\Entities\Agent")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="agent_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    protected $agent;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="MapasCulturais\Entities\Agent")
     * @ORM\JoinColumns({
     *    @ORM\JoinColumn(name="reply_agent_id", referencedColumnName="id", nullable=true)
     * })
     */
    protected $replyAgent = null;

    /**
     * @var bool
     *
     * @ORM\Column(name="reply_publish", type="boolean", nullable=true)
     */
    protected $replyPublish = false;


    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true, name="create_timestamp")
     */
    protected $createTimestamp;

}
