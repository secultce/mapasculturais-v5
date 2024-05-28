<?php
namespace Diligence\Entities;

use Doctrine\ORM\Mapping as ORM;
use \MapasCulturais\App;
use \MapasCulturais\i;
use DateTime;
use Diligence\Controllers\Controller;
use MapasCulturais\Entity;

use Diligence\Repositories\Diligence as DiligenceRepo;
use Diligence\Entities\Diligence as EntityDiligence;
use Diligence\Service\DiligenceInterface;
use MapasCulturais\ApiOutputs\Json;


/**
 * Tado 
 * 
 * @ORM\Table(name="tado")
 * @ORM\Entity
 * @ORM\entity(repositoryClass="MapasCulturais\Repository")
 */

class Tado extends \MapasCulturais\Entity {
   /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="tado_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;

     /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_timestamp", type="datetime", nullable=false)
     */
    protected $createTimestamp;

     /**
     * @var \DateTime
     *
     * @ORM\Column(name="period_from", type="datetime", nullable=false)
     */
    protected $periodFrom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="rperiod_to", type="datetime", nullable=false)
     */
    protected $periodTo;
}