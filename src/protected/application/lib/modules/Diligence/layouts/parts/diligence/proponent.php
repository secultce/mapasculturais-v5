<?php

use Carbon\Carbon;
use MapasCulturais\App;
use Diligence\Entities\AnswerDiligence;
use Diligence\Repositories\Diligence as DiligenceRepo;

$diligence = App::i()->repo('Diligence\Entities\AnswerDiligence')->find(27);

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
            'diligenceDays' => $context['diligenceDays'],
            'placeHolder' => $context['placeHolder'],
            'sendEvaluation' => $sendEvaluation,
            'isProponent' => $context['isProponent'],
            'diligenceAndAnswers' => $diligenceAndAnswers
        ]);
        ?>
<?php 
    $showText = false;
    dump($diligenceAndAnswers);
    if(!is_null($diligenceAndAnswers))
    {
        foreach ($diligenceAndAnswers as $key => $resultsDraft) {
           
            if ($resultsDraft instanceof AnswerDiligence && !is_null($resultsDraft) && $resultsDraft->status == 0) :
                $dateDraft = Carbon::parse($resultsDraft->createTimestamp)->diffForHumans();
                $descriptionDraft = true;
                $showText = true;
                $type = "";
                // DiligenceRepo::getDraft($resultsDraft->answer, $resultsDraft->id, 'proponente', ucfirst($dateDraft));
        ?>
    
                <div id="draft-description-diligence" class="div-draft-description-diligence">
                    <div style="display: flex;  justify-content: space-between;">
                    <span style="font-size: medium; color: #000">Resposta em rascunho. <br /></span>
                    <a class="btn btn-primary" onclick='editDescription(<?= json_encode($resultsDraft->answer); ?>,<?= $resultsDraft->id; ?>, "proponent")'>
                            Editar Resposta
                        </a>
                    </div>
                    <p style="color: #3E3E3E;font-size: 10x; margin-top: 14px;"><?= $resultsDraft->answer; ?> </p>
                    <p style="font-size: x-small; font-size: 12px; font-weight: 700; margin-top: 8px"><?= ucfirst($dateDraft); ?> </p>
                    <p>
                      
                    </p>
                </div>
        <?php
            endif;
        };
        foreach ($diligenceAndAnswers as $key => $resultsAnswer) {
            if(is_null($resultsAnswer))
            {
                $showText = true;
            }
        }
    }
   
    ?>
        <div class="flex-container" id="btn-actions-proponent">
            
            <?php 
            dump($showText);
                $showText ? $this->part('diligence/description', ['placeHolder' => $placeHolder]) : null;
                $this->part('diligence/btn-actions-proponent', ['entity' => $context['entity'], 'showText' => $showText, 'diligence' => $diligence]); 
            ?>
        </div>
        <!-- FIM PROPONENTE -->
    </div>
</div>