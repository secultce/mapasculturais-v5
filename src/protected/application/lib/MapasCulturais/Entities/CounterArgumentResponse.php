<?php

namespace MapasCulturais\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * CounterArgumentResponse
 * 
 * @ORM\Table(name="counter_argument_response")
 * @ORM\Entity
 * @ORM\entity(repositoryClass="MapasCulturais\Repositories\CounterArgument")
 */
class CounterArgumentResponse extends \MapasCulturais\Entity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="counter_argument_response_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", nullable=false)
     */
    protected $text;

    /**
     * @var boolean
     *
     * @ORM\Column(name="published", type="boolean", nullable=false)
     */
    protected $published = false;

    /**
     * @var \MapasCulturais\Entities\Agent
     *
     * @ORM\ManyToOne(targetEntity="MapasCulturais\Entities\Agent", fetch="LAZY")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="agent_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    protected $owner;

    /**
     * @var \MapasCulturais\Entities\CounterArgument
     *
     * @ORM\OneToOne(targetEntity="MapasCulturais\Entities\CounterArgument", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="counter_argument_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    protected $counterArgument;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_timestamp", type="datetime", nullable=false)
     */
    protected $createTimestamp;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_timestamp", type="datetime", nullable=true)
     */
    protected $updateTimestamp;
}
