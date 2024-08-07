<?php
namespace MapasCulturais\Entities;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use MapasCulturais\Entity;
use MapasCulturais\Repositories\Draw as DrawRepository;

/**
 * @ORM\Entity(repositoryClass=DrawRepository::class)
 * @ORM\Table(name="draw")
 */
class Draw extends Entity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity=\MapasCulturais\Entities\Opportunity::class)
     * @ORM\JoinColumn(name="opportunity_id", referencedColumnName="id", nullable=false)
     */
    protected $opportunity;

    /**
     * @ORM\Column(type="text")
     */
    protected $category;

    /**
     * @ORM\Column(type="datetime", name="create_timestamp")
     */
    protected $createTimestamp;

    /**
     * @ORM\ManyToOne(targetEntity=\MapasCulturais\Entities\User::class)
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    protected $user;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $published;

    /**
     * @ORM\OneToMany(targetEntity="DrawRegistrations", mappedBy="draw", cascade="persist")
     */
    protected $drawRegistrations;

    public function jsonSerialize(): array
    {
        $serialized = parent::jsonSerialize();
        $serialized['opportunity'] = [
            'id' => $this->opportunity->id,
            'name' => $this->opportunity->name,
            'singleUrl' => $this->opportunity->singleUrl,
        ];
        $serialized['drawRegistrations'] = $this->drawRegistrations->toArray();
        $serialized['user'] = $this->user->profile->name;
        return $serialized;
    }
}
