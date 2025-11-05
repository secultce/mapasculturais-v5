<?php
use MapasCulturais\App;
$action = preg_replace("#^(\w+/)#", "", $this->template);

$this->bodyProperties['ng-app'] = "entity.app";
$this->bodyProperties['ng-controller'] = "EntityController";

$this->jsObject['angularAppDependencies'][] = 'entity.module.opportunity';

$this->addEntityToJs($entity);

$this->addOpportunityToJs($entity->opportunity);

$this->addOpportunitySelectFieldsToJs($entity->opportunity);

$this->addRegistrationToJs($entity);

$this->includeAngularEntityAssets($entity);
$this->includeEditableEntityAssets();

$avaliable_evaluationFields = $entity->opportunity->avaliableEvaluationFields ?? [];
$app->view->jsObject['avaliableEvaluationFields'] = $avaliable_evaluationFields;

$can_see = function($field) use ($entity, $avaliable_evaluationFields){  

    /** O Gestor pode ver todos os campos */
    if($entity->opportunity->canUser("@control")){
        return true;
    }

    if($entity->canUser("viewUserEvaluation")  && !isset($avaliable_evaluationFields[$field])){
        return false;
    }

    return true;
};

$_params = [
    'entity' => $entity,
    'action' => $action,
    'opportunity' => $entity->opportunity
];

?>
<?php $this->part('editable-entity', array('entity'=>$entity, 'action'=>$action));  ?>

<article class="main-content registration" ng-controller="OpportunityController">
    <?php $this->part('singles/registration--header', $_params); ?>
      

    <div class="tabs-content">
         <?php $this->applyTemplateHook('content-diligence','begin'); ?>
           
        <?php $this->applyTemplateHook('content-diligence','end'); ?>

    <article id="registration-content-all" class="aba-content">

        <?php $this->applyTemplateHook('form','begin'); ?>

        <?php $this->part('singles/registration-single--header', $_params) ?>

        <?php if($can_see('category')): ?>
       
        <?php $this->part('singles/registration-single--categories', $_params) ?>   

        <?php endif ?>
        
        <?php if($can_see('agentsSummary')): ?>

        <?php $this->part('singles/registration-single--agents', $_params) ?>

        <?php endif ?>

        <?php if($can_see('spaceSummary')): ?>

        <?php $this->part('singles/registration-single--spaces', $_params) ?>
        
        <?php endif ?>

        <?php if(App::i()->repo($entity->getClassName())->find($entity->id)->canUser('evaluate')){
            $canEvaluate= App::i()->repo($entity->getClassName())->find($entity->id)->canUser('evaluate');
            $this->part('singles/bonus-single',['canEvaluate'=>$canEvaluate]);
        } ?>
               
        <?php if ($entity->opportunity->canUser("@control")): ?>
            <?php
                $registrationEvaluations = $app->repo('RegistrationEvaluation')->findBy(['registration' => $entity->id]);
                $b2FieldIds = []; 
                $b2Fields   = [];   

                array_walk($registrationEvaluations, function($evaluation) use (&$b2FieldIds, &$b2Fields){
                    array_walk($evaluation->evaluationData, function($value, $key) use (&$b2FieldIds, &$b2Fields){
                        if (str_starts_with($key, 'b2_')) {
                            $id = (int) substr($key, 3);
                            $b2FieldIds[] = $id;
                            $b2Fields[$id] = ['value' => $value];
                        }
                    });
                });

                $b2FieldIds = array_unique($b2FieldIds);

                if (!empty($b2FieldIds)) {
                    $fieldConfigs = $app->repo('RegistrationFieldConfiguration')
                                        ->findBy(['id' => $b2FieldIds]);
                    $b2Fields = array_map(function($fieldConfig) use ($b2Fields){
                        $id = $fieldConfig->id;
                        if(!isset($b2Fields[$id])){
                            return null;
                        }
                        return [
                            'id'    => $id,
                            'title' => $fieldConfig->title,
                            'value' => $b2Fields[$id]['value']
                        ];
                    }, $fieldConfigs);

                    $b2Fields = array_filter($b2Fields);
                }

            ?>
            <?php $this->part('singles/registration/fields-for-bonus', [
                'b2Fields' => array_values($b2Fields)
            ]); ?>
        <?php endif; ?>

        <?php $this->part('singles/registration-single--fields', $_params) ?>

        <?php $this->applyTemplateHook('form','end'); ?>
    </article>
    </div>
    <?php $this->part('singles/registration--valuers-list', $_params) ?>
</article>
<article id="sidebars">
    <?php $this->part('singles/registration--sidebar--left', $_params) ?>
    <?php $this->part('singles/registration--sidebar--right', $_params) ?>
</article>
