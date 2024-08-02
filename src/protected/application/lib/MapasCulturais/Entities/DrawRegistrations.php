<?php
namespace MapasCulturais\Entities;

use Doctrine\ORM\Mapping as ORM;
use MapasCulturais\Entity;

/**
 * @ORM\Entity
 * @ORM\Table(name="draw_registrations")
 */
class DrawRegistrations extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Draw", inversedBy="drawRegistrations")
     * @ORM\JoinColumn(name="ranking_id", referencedColumnName="id", nullable=false)
     */
    private $draw;

    /**
     * @ORM\ManyToOne(targetEntity=\MapasCulturais\Entities\Registration::class)
     * @ORM\JoinColumn(name="registration_id", referencedColumnName="id", nullable=false)
     */
    private $registration;

    /**
     * @ORM\Column(type="integer")
     */
    private $rank;
}
