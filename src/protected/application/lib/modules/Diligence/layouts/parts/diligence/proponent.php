<?php
$app->view->enqueueScript('app', 'diligence', 'js/diligence/proponent.js');
$placeHolder = "Digite aqui a sua resposta";


$this->applyTemplateHook('tabs', 'before');
$this->part('diligence/ul-buttons', ['entity' => $context['entity'], 'sendEvaluation' => $sendEvaluation]);
?>

<?php $this->applyTemplateHook('tabs', 'after'); ?>
<div class="tabs-content">
    <div id="diligence-principal"></div>
    <div id="diligence-diligence">
        <!-- PARA O PROPONENTE -->
        <?php
        $this->part('diligence/body-diligence-common', [
            'entity' => $context['entity'],
            'diligenceRepository' => $context['diligenceRepository'], 
            'term' => $context['term'],
            'placeHolder' => $context['placeHolder'],
            'sendEvaluation' => $sendEvaluation
        ]);
        ?>

        <div class="flex-container" id="btn-actions-proponent">
            <?php $this->part('diligence/btn-actions-proponent', ['entity' => $context['entity']]); ?>
        </div>
        <!-- FIM PROPONENTE -->
    </div>
</div>