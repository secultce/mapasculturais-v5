<?php

namespace MapasCulturais\Entities;

use Doctrine\ORM\Mapping as ORM;
use MapasCulturais\App;
use MapasCulturais\Traits;

/**
 * RegistrationMeta
 *
 * @ORM\Table(name="registration_file_configuration")
 * @ORM\Entity
 * @ORM\entity(repositoryClass="MapasCulturais\Repository")
 * @ORM\HasLifecycleCallbacks
 */
class RegistrationFileConfiguration extends \MapasCulturais\Entity
{
    use Traits\EntityRevision,
        Traits\EntityFiles;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="registration_file_configuration_id_seq", allocationSize=1, initialValue=1)
     */
    protected $id;

    /**
     * @var \MapasCulturais\Entities\Opportunity
     *
     * @ORM\ManyToOne(targetEntity="MapasCulturais\Entities\Opportunity")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="opportunity_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    protected $owner;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @var boolean
     *
     * @ORM\Column(name="required", type="boolean", nullable=false)
     */
    protected $required = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="categories", type="array", nullable=true)
     */
    protected $categories = [];
    
    /**
     * @var integer
     *
     * @ORM\Column(name="display_order", type="smallint", nullable=false)
     */
    protected $displayOrder = 255;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="conditional", type="boolean", nullable=false)
     */
    protected $conditional ;

     /**
     * @var string
     *
     * @ORM\Column(name="conditional_field", type="string",length=255 ,nullable=false)
     */
    protected $conditionalField;

     /**
     * @var string
     *
     * @ORM\Column(name="conditional_value", type="string",length=255 ,nullable=false)
     */
    protected $conditionalValue;

    /**
     * @var \MapasCulturais\Entities\AgentFile[] Files
     *
     * @ORM\OneToMany(targetEntity="MapasCulturais\Entities\RegistrationFileConfigurationFile", mappedBy="owner", cascade="remove", orphanRemoval=true)
     * @ORM\JoinColumn(name="id", referencedColumnName="object_id", onDelete="CASCADE")
    */
    protected $__files;

    static function getValidations() {
        return [
            'owner' => [ 
                'required' => \MapasCulturais\i::__("A oportunidade é obrigatória.")
            ],
            'title' => [ 
                'required' => \MapasCulturais\i::__("O título do anexo é obrigatório.")
            ]
        ];
    }

    public function getFileGroupName(){
        return 'rfc_' . $this->id;
    }

    public function setOwnerId($id){
        $this->owner = App::i()->repo('Opportunity')->find($id);
    }
    
    public function setCategories($value) {
        if(!$value){
            $value = [];
        } else if (!is_array($value)){
            $value = explode("\n", $value);
        }
        $this->categories = $value;
    }

    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'ownerId' => $this->owner->id,
            'title' => $this->title,
            'description' => $this->description,
            'required' => $this->required,
            'template' => $this->getFile('registrationFileTemplate'),
            'groupName' => $this->fileGroupName,
            'categories' => $this->categories,
            'displayOrder' => $this->displayOrder,
            'conditional' => $this->conditional ? true : false,
            'conditionalField' => $this->conditionalField,
            'conditionalValue' => $this->conditionalValue
        ];
    }

    protected function _canUser($user){
        return $this->owner->canUser('modifyRegistrationFields', $user);
    }

    protected function canUserModify($user){
        return $this->_canUser($user);
    }

    protected function canUserRemove($user){
        return $this->_canUser($user);
    }

    protected function canUserCreate($user){
        return $this->_canUser($user);
    }

    //============================================================= //
    // The following lines ara used by MapasCulturais hook system.
    // Please do not change them.
    // ============================================================ //

    /** @ORM\PrePersist */
    public function prePersist($args = null){ parent::prePersist($args); }
    /** @ORM\PostPersist */
    public function postPersist($args = null){ parent::postPersist($args); }

    /** @ORM\PreRemove */
    public function preRemove($args = null){ parent::preRemove($args); }
    /** @ORM\PostRemove */
    public function postRemove($args = null){ parent::postRemove($args); }

    /** @ORM\PreUpdate */
    public function preUpdate($args = null){ parent::preUpdate($args); }
    /** @ORM\PostUpdate */
    public function postUpdate($args = null){ parent::postUpdate($args); }
}
