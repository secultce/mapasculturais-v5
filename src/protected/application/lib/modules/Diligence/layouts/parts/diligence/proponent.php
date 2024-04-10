<?php
$app->view->enqueueScript('app', 'diligence', 'js/diligence/proponent.js');

$this->applyTemplateHook('tabs', 'before');
$this->part('diligence/ul-buttons');
?>

<?php $this->applyTemplateHook('tabs', 'after'); ?>
<div class="tabs-content">
    <div id="diligence-principal"></div>
    <div id="diligence-diligence">
    <!-- PARA O PROPONENTE -->
        <?php 
            $this->part('diligence/body-diligence-common', [
                    'entity' => $entity,
                    'diligenceRepository' => $diligenceRepository, 
                    'term' => $term ,
                    'placeHolder' => $placeHolder
                    ]); 
        ?>   
    <div class="flex-items" id="btn-actions-proponent">
        <?php $this->part('diligence/btn-actions-proponent', ['entity' => $entity]); ?>
    </div>
    <!-- FIM PROPONENTE -->
    </div>
</div>