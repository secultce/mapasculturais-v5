<?php

namespace MapasCulturais\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\entity(repositoryClass="MapasCulturais\Repository")
 */
class CounterArgumentFile extends File
{
    /**
     * @var \MapasCulturais\Entities\CounterArgument
     *
     * @ORM\ManyToOne(targetEntity="MapasCulturais\Entities\CounterArgument")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $owner;

    /**
     * @var \MapasCulturais\Entities\CounterArgumentFile
     *
     * @ORM\ManyToOne(targetEntity="MapasCulturais\Entities\CounterArgumentFile", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    protected $parent;
}
