<?php

namespace Diligence\Entities;

use Doctrine\ORM\Mapping as ORM;
use MapasCulturais\App;
use MapasCulturais\Entities\Registration;
use MapasCulturais\Traits;

/**
 * Opinion 
 * 
 * @ORM\Table(name="accountability_opinion")
 * @ORM\Entity
 * @ORM\entity(repositoryClass="MapasCulturais\Repository")
 */
class Opinion extends \MapasCulturais\Entity
{
    use Traits\EntityRevision;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="accountability_opinion_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="opinion", type="text", nullable=false)
     */
    protected $opinion;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="smallint", nullable=false)
     */
    protected $status = self::STATUS_DRAFT;

    /**
     * @var \MapasCulturais\Entities\Registration
     *
     * @ORM\ManyToOne(targetEntity="MapasCulturais\Entities\Registration")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="registration_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    protected $registration;

    /**
     * @var \MapasCulturais\Entities\Agent
     *
     * @ORM\ManyToOne(targetEntity="MapasCulturais\Entities\Agent", fetch="LAZY")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="agent_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    protected $owner;

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

    public function create(string $opinion, Registration $registration, bool $publish)
    {
        $this->opinion = $opinion;
        $this->registration = $registration;
        $this->owner = App::i()->getUser()->profile;
        $this->createTimestamp = new \DateTime();

        if ($publish) $this->setStatus(self::STATUS_ENABLED);

        $this->save(true);
    }

    public function update(string $opinion, bool $publish)
    {
        $this->opinion = $opinion;
        $this->updateTimestamp = new \DateTime();

        if ($publish) $this->setStatus(self::STATUS_ENABLED);

        $this->save(true);
    }

    public function setStatus(int $status)
    {
        $this->status = $status;
    }
}
