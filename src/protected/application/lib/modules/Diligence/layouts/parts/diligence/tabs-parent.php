<?php

use MapasCulturais\i;
use Diligence\Entities\Diligence as EntityDiligence;



$this->jsObject['isProponent'] = EntityDiligence::isProponent($context['diligenceRepository'], $context['entity']);

$app->view->enqueueScript('app', 'diligence', 'js/diligence/diligence.js');
//Verificação se as avaliações já foram enviadas pelo avaliador logado
// 

?>
<?php 
    $this->applyTemplateHook('tabs', 'before');
    $this->part('diligence/ul-buttons', ['entity' => $context['entity'], 'sendEvaluation' => $sendEvaluation]);
?>

<div class="tabs-content">
    <div id="diligence-principal">

    </div>
    <div id="diligence-diligence">
        <?php 
            $this->part('diligence/body-diligence-common', [
                    'entity' => $context['entity'],
                    'diligenceRepository' => $context['diligenceRepository'], 
                    'term' => $context['term'],
                    'placeHolder' => $context['placeHolder'],
                    'sendEvaluation' => $sendEvaluation
                ]); 
        ?>
        <div id="div-info-send" class="div-info-send">
            <p>
                <?php i::_e('Sua diligência já foi enviada') ?>
            </p>
        </div>

        <div class="div-btn-send-diligence flex-container">
            <div class="flex-items" id="btn-actions-diligence">
                <?php 
                if(!$sendEvaluation):
                    $this->part('diligence/btn-actions-diligence'); 
                endif;
                ?>
            </div>
           
        </div>
    </div>
</div>
<?php $this->applyTemplateHook('tabs', 'after'); ?>

<?php //endif; ?>