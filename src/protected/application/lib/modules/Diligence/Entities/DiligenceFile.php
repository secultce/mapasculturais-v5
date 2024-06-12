<?php

namespace Diligence\Entities;

use Doctrine\ORM\Mapping as ORM;
use MapasCulturais\Entities\File;

/**
 * @ORM\Entity
 * @ORM\entity(repositoryClass="MapasCulturais\Repository")
 */
class DiligenceFile extends File {

    /**
     * @var \Diligence\Entities\Diligence
     *
     * @ORM\ManyToOne(targetEntity="Diligence\Entities\Diligence")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    protected $owner;

    /**
     * @var \Diligence\Entities\DiligenceFile
     *
     * @ORM\ManyToOne(targetEntity="Diligence\Entities\DiligenceFile", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    protected $parent;
}