<?php

namespace Diligence\Entities;

use DateTime;
use \MapasCulturais\i;
use \MapasCulturais\App;
use MapasCulturais\Entity;
use Diligence\Controllers\Tado as ControllerTado;
use Doctrine\ORM\Mapping as ORM;

use MapasCulturais\ApiOutputs\Json;
use Diligence\Controllers\Controller;
use Diligence\Service\DiligenceInterface;
use Diligence\Entities\Diligence as EntityDiligence;
use Diligence\Repositories\Diligence as DiligenceRepo;
use MapasCulturais\Traits\EntityRevision;

/**
 * Tado 
 * 
 * @ORM\Table(name="tado")
 * @ORM\Entity
 * @ORM\entity(repositoryClass="MapasCulturais\Repository")
 */

class Tado extends \MapasCulturais\Entity
{
  use EntityRevision;
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
   * @var string
   *
   * @ORM\Column(name="number", type="string", length=24, nullable=true)
   */
  protected $number;

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
   * @ORM\Column(name="period_to", type="datetime", nullable=false)
   */
  protected $periodTo;

  /**
   * @var \MapasCulturais\Entities\Agent
   *
   * @ORM\ManyToOne(targetEntity="MapasCulturais\Entities\Agent", fetch="LAZY")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="agent_id", referencedColumnName="id", onDelete="CASCADE")
   * })
   */
  protected $agent;

  /**
   * @var string
   *
   * @ORM\Column(name="object", type="string", length=255, nullable=false)
   */
  protected $object;

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
   * @var string
   *
   * @ORM\Column(name="conclusion", type="text", nullable=true)
   */
  protected $conclusion;

  /**
   * @var \MapasCulturais\Entities\Agent
   *
   * @ORM\ManyToOne(targetEntity="MapasCulturais\Entities\Agent", fetch="LAZY")
   * @ORM\JoinColumns({
   *   @ORM\JoinColumn(name="agent_signature", referencedColumnName="id", onDelete="CASCADE")
   * })
   */
  protected $agentSignature;

  /**
   * @var integer
   *
   * @ORM\Column(name="status", type="smallint", nullable=false)
   */
  protected $status = self::STATUS_DRAFT;

    /**
     * @var string
     *
     * @ORM\Column(name="name_manager", type="string", length=255, nullable=true)
     */
    protected $nameManager;


    /**
     * @var string
     *
     * @ORM\Column(name="cpf_manager", type="string", length=255, nullable=true)
     */
    protected $cpfManager;

    public function validateForm(ControllerTado $request): array
  {
    $return = [];
    foreach ($request->data as $key => $value) {
      if ($key == "object" && $value == "") {
        array_push($return, 'O Objeto é obrigatório');
      }
      if ($key == "conclusion" && $value == "") {
        array_push($return, 'A Conclusão é obrigatória');
      }
      if (
        $request->data['status'] == 1 && 
        ($key == "datePeriodInitial" && $value == "") || $key == "datePeriodEnd" && $value == ""  
      ) {
        array_push($return, 'O Período Final da Vigência é Obrigatório');
      }
      
    }
    return $return;
  }
}
