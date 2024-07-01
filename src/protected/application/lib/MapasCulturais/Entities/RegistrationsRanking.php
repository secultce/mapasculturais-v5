<?php

namespace MapasCulturais\Entities;

use MapasCulturais\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * RegistrationsRanking
 *
 * @ORM\Table(name="registrations_ranking", indexes={
 *     @ORM\Index(name="registrations_ranking_opportunity_category_idx", columns={"opportunity_id", "category"}),
 *     @ORM\Index(name="registrations_ranking_category_idx", columns={"category"}),
 * }, uniqueConstraints={
 *     @ORM\UniqueConstraint(name="opportunity_category_rank", columns={"opportunity_id", "category", "rank"})
 * })
 * @ORM\Entity(repositoryClass="MapasCulturais\Repositories\RegistrationsRanking", readOnly=true)
 * @ORM\HasLifecycleCallbacks
 */
class RegistrationsRanking extends Entity
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var Registration
     *
     * @ORM\OneToOne(targetEntity="MapasCulturais\Entities\Registration")
     * @ORM\JoinColumn(name="registration_id", referencedColumnName="id", nullable=false, onDelete="cascade")
     */
    protected $registration;

    /**
     * @var Opportunity
     *
     * @ORM\OneToOne(targetEntity="MapasCulturais\Entities\Opportunity")
     * @ORM\JoinColumn(name="opportunity_id", referencedColumnName="id", nullable=false, onDelete="cascade")
     */
    protected $opportunity;

    /**
     * @var int
     *
     * @ORM\Column(name="rank", type="integer", nullable=false)
     */
    protected $rank;

    /**
     * @var string
     *
     * @ORM\Column(name="category", type="text", nullable=false)
     */
    protected $category;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_timestamp", type="datetime", nullable=false)
     */
    protected $createTimestamp;

    /**
     * @var Agent
     *
     * @ORM\ManyToOne(targetEntity="MapasCulturais\Entities\Agent")
     * @ORM\JoinColumn(name="agent_id", referencedColumnName="id", nullable=false)
     */
    protected $owner;

    /**
     * @throws \Exception
     */
    public function __construct(Registration $registration, Opportunity $opportunity, int $rank, string $category, Agent $owner)
    {
        parent::__construct();

        // Valida se a categoria da inscrição é igual a que será registrada
        if($registration->category !== $category)
            throw new \Exception('Invalid category to registration');

        $this->registration = $registration;
        $this->opportunity = $opportunity;
        $this->rank = $rank;
        $this->category = $category;
        $this->createTimestamp = new \DateTime();
        $this->owner = $owner;
    }
}
