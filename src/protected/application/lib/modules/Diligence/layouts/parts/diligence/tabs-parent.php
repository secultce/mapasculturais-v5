<?php

use Carbon\Carbon;
use MapasCulturais\i;
use Diligence\Entities\Diligence as EntityDiligence;
use Diligence\Repositories\Diligence as DiligenceRepo;

$files = DiligenceRepo::getFilesDiligence($context['entity']->id);

$this->jsObject['isProponent'] = EntityDiligence::isProponent($context['diligenceRepository'], $context['entity']);
$showText = false;
$placeHolder = "Digite aqui a sua diligência";
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
                    'isProponent' => $context['isProponent'],
                    'diligenceAndAnswers' => $diligenceAndAnswers
                ]); 
        ?>
        <?php 
        dump($diligenceAndAnswers);
    if(!is_null($diligenceAndAnswers))
    {
        $diligenceAndAnswerLast = [];
        foreach ($diligenceAndAnswers as $key => $resultsDraft) {
            //Array somente com os dois ultimos registros
            if($key < 2) {
                array_push($diligenceAndAnswerLast,$resultsDraft );
            }
            if ($resultsDraft instanceof EntityDiligence && !is_null($resultsDraft) && $resultsDraft->status == 0) :
               
                $descriptionDraft = true;
                
             
               
            endif;
        };

        
        if($diligenceAndAnswerLast[0]->status == 0)
        {
            $dateDraft = Carbon::parse($diligenceAndAnswerLast[0]->createTimestamp)->diffForHumans();
            $this->part('diligence/edit-description',[
                'titleDraft' => 'Diligência em rascunho.',
                'titleButton' => 'Editar Diligência',
                'resultsDraft' => $diligenceAndAnswerLast[0]->description,
                'id' => $diligenceAndAnswerLast[0]->id,
                'type' => "proponent",
                'dateDraft' => ucfirst($dateDraft)
            ]);
        }

        if(!is_null($diligenceAndAnswerLast[1]) && $diligenceAndAnswerLast[1]->status == 3)
        {
            $showText = true;
        }
       
        // dump($diligenceAndAnswerLast);
    }
    ?>
        <div id="div-info-send" class="div-info-send">
            <p>
                <?php i::_e('Sua diligência já foi enviada') ?>
            </p>
        </div>
<?php
// dump($showText);



?>
        <div class="div-btn-send-diligence flex-container">
            <div class="" id="btn-actions-diligence">
                <?php 
                // dump($showText);
                    $showText ? $this->part('diligence/description', ['placeHolder' => $placeHolder]) : null;
                    $this->part('diligence/btn-actions-diligence', [
                        'entity' => $context['entity'],
                        'sendEvaluation' => $sendEvaluation
                    ]);
                    $this->part('diligence/message-success-draft');
              
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
