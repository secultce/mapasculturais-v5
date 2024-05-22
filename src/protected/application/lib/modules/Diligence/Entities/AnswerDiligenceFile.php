<?php
namespace Diligence\Entities;

use Doctrine\ORM\Mapping as ORM;
use MapasCulturais\Entities\File;
/**
 * @ORM\Entity
 * @ORM\entity(repositoryClass="MapasCulturais\Repository")
 */
class AnswerDiligenceFile extends File{

    /**
     * @var \Diligence\Entities\AnswerDiligence
     *
     * @ORM\ManyToOne(targetEntity="Diligence\Entities\AnswerDiligence", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    protected $owner;

    /**
     * @var \Diligence\Entities\AnswerDiligenceFile
     *
     * @ORM\ManyToOne(targetEntity="Diligence\Entities\AnswerDiligence", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    protected $parent;

    // public function getUrl(): string
    // {
    //     return \MapasCulturais\App::i()->createUrl('recursos', 'arquivo', [$this->id]);
    // }
}