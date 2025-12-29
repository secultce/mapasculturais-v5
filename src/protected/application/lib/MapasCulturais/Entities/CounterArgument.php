<?php

namespace MapasCulturais\Entities;

use Doctrine\ORM\Mapping as ORM;
use MapasCulturais\Traits;

/**
 * CounterArgument
 * 
 * @ORM\Table(name="counter_argument")
 * @ORM\Entity
 * @ORM\entity(repositoryClass="MapasCulturais\Repositories\CounterArgument")
 */
class CounterArgument extends \MapasCulturais\Entity
{
    use Traits\EntityFiles;

    const STATUS_SEND = self::STATUS_ENABLED;

    const STATUSES = [
        self::STATUS_SEND => 'Enviado',
    ];

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="counter_argument_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", nullable=false)
     */
    protected $text;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="smallint", nullable=false)
     */
    protected $status = self::STATUS_SEND;

    /**
     * @var \MapasCulturais\Entities\Registration
     *
     * @ORM\OneToOne(targetEntity="MapasCulturais\Entities\Registration", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="registration_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    protected $registration;

    /**
     * @var \MapasCulturais\Entities\CounterArgumentFile[] Files
     *
     * @ORM\OneToMany(targetEntity="MapasCulturais\Entities\CounterArgumentFile", fetch="EXTRA_LAZY", mappedBy="owner", cascade="remove", orphanRemoval=true)
     * @ORM\JoinColumn(name="id", referencedColumnName="object_id", onDelete="CASCADE")
     */
    protected $__files;

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
