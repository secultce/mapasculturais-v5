<?php
namespace CounterReason\Entities;

use Doctrine\ORM\Mapping as ORM;
use CounterReason\Entities\File;
/**
 * @ORM\Entity
 * @ORM\entity(repositoryClass="MapasCulturais\Repository")
 *
 */

class CounterReasonFile extends File{

    /**
     * @var \CounterReason\Entities\CounterReason
     *
     * @ORM\ManyToOne(targetEntity="CounterReason\Entities\CounterReason", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    protected $owner;

    /**
     * @var \CounterReason\Entities\CounterReasonFile
     *
     * @ORM\ManyToOne(targetEntity="CounterReason\Entities\CounterReasonFile", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    protected $parent;
}