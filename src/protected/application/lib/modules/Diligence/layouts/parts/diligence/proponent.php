<?php
$app->view->enqueueScript('app', 'diligence', 'js/diligence/proponent.js');
$placeHolder = "Digite aqui a sua resposta";


$this->applyTemplateHook('tabs', 'before');
$this->part('diligence/ul-buttons', ['entity' => $entity]);
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
            'term' => $term,
            'placeHolder' => $placeHolder
        ]);
        ?>

        <div class="flex-container" id="btn-actions-proponent">
            <?php $this->part('diligence/btn-actions-proponent', ['entity' => $entity]); ?>
        </div>
        <!-- FIM PROPONENTE -->
    </div>
</div>