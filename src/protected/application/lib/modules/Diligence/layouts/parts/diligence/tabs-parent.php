<?php

use MapasCulturais\i;
use Diligence\Entities\Diligence as EntityDiligence;

use Diligence\Repositories\Diligence as DiligenceRepo;
$files = DiligenceRepo::getFilesDiligence($context['entity']->id);

$this->jsObject['isProponent'] = EntityDiligence::isProponent($context['diligenceRepository'], $context['entity']);

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
                    'diligenceDays' => $context['diligenceDays'],
                    'placeHolder' => $context['placeHolder'],
                    'diligenceAndAnswers' => $diligenceAndAnswers,
                    'sendEvaluation' => $sendEvaluation,
                    'isProponent' => $context['isProponent']
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
               
                    $this->part('diligence/btn-actions-diligence', [
                        'entity' => $context['entity'],
                        'sendEvaluation' => $sendEvaluation
                    ]); 
              
                ?>
               
            </div>
            <div class="import-diligence" style="width: 100%">
                <?php
                foreach($files as $file){
                    $id = $file["id"];
                echo  '<article id="file-diligence-up-'.$id.'" class="objeto" style="margin-top: 20px;">
                    <span>Arquivo</span>
                    <h1><a href="/arquivos/privateFile/'.$id.'" 
                    class="attachment-title ng-binding ng-scope" target="_blank" rel="noopener noreferrer" 
                    >'.$file["name"].'</a></h1> 
                    <div class="botoes footer-btn-delete-file-diligence">
                        <a data-href="/diligence/deleteFile/'.$id.'/registration/'.$context['entity']->id.'"
                        data-target="#file-diligence-up-'.$id.'"
                        data-configm-message="Remover este arquivo?"
                        class="btn btn-small btn-danger delete hltip js-remove-item">Excluir</a>
                    </div>
                </article>';
                } ?>
            </div>
        </div>
    </div>
</div>
<?php $this->applyTemplateHook('tabs', 'after'); ?>

<?php //endif; ?>
