<?php
namespace MapasCulturais\Entities;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use MapasCulturais\Entity;

/**
 * @ORM\Entity
 * @ORM\Table(name="draw")
 */
class Draw extends Entity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=\MapasCulturais\Entities\Opportunity::class)
     * @ORM\JoinColumn(name="opportunity_id", referencedColumnName="id", nullable=false)
     */
    private $opportunity;

    /**
     * @ORM\Column(type="text")
     */
    private $category;

    /**
     * @ORM\Column(type="timestamp")
     */
    private $createTimestamp;

    /**
     * @ORM\ManyToOne(targetEntity=\MapasCulturais\Entities\User::class)
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="boolean")
     */
    private $published;

    /**
     * @ORM\OneToMany(targetEntity="DrawRegistrations", mappedBy="draw")
     */
    private $drawRegistrations;

    public function __construct()
    {
        $this->drawRegistrations = new ArrayCollection();
        parent::__construct();
    }
}
