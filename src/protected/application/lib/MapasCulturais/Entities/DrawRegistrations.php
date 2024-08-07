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
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Draw", inversedBy="drawRegistrations", cascade="persist")
     * @ORM\JoinColumn(name="draw_id", referencedColumnName="id", nullable=false)
     */
    protected $draw;

    /**
     * @ORM\ManyToOne(targetEntity=\MapasCulturais\Entities\Registration::class)
     * @ORM\JoinColumn(name="registration_id", referencedColumnName="id", nullable=false)
     */
    protected $registration;

    /**
     * @ORM\Column(type="integer")
     */
    protected $rank;

    public function jsonSerialize(): array
    {
        $serialized = parent::jsonSerialize();
        unset($serialized['draw']);
        $serialized['registration'] = [
            'singleUrl' => $this->registration->singleUrl,
            'number' => $this->registration->number,
            'owner' => [
                'name' => $this->registration->owner->name,
                'id' => $this->registration->owner->id
            ],
        ];
        return $serialized;
    }
}
