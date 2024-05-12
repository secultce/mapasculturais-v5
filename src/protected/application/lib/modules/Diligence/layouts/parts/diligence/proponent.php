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
        if (!is_null($diligenceAndAnswers)) {
            $diligenceAndAnswerLast = [];
            foreach ($diligenceAndAnswers as $key => $resultsDraft) {
                if ($key < 2) {
                    array_push($diligenceAndAnswerLast, $resultsDraft);
                }
            };
            if (!is_null($diligenceAndAnswerLast[1]) && $diligenceAndAnswerLast[1]->status == 0) {
                $dateDraft = Carbon::parse($diligenceAndAnswerLast[0]->createTimestamp)->diffForHumans();
                $this->part('diligence/edit-description', [
                    'titleDraft' => 'Resposta em rascunho.',
                    'titleButton' => 'Editar Resposta',
                    'resultsDraft' => $diligenceAndAnswerLast[1]->answer,
                    'id' => $diligenceAndAnswerLast[1]->id,
                    'type' => "proponent",
                    'dateDraft' => ucfirst($dateDraft)
                ]);
                $showText = true;
            }
            if (is_null($diligenceAndAnswerLast[1]) && $diligenceAndAnswerLast[0]->status == 3) {
                $showText = true;
            }
        }
        ?>
        <div class="flex-container" id="btn-actions-proponent">
            <?php
            if ($showText || is_null($diligenceAndAnswers)) {
                $this->part('diligence/description', ['placeHolder' => $placeHolder]);
                $this->part('diligence/message-success-draft');
                $this->part('diligence/btn-actions-proponent', ['entity' => $context['entity'], 'showText' => $showText, 'diligence' => $diligence]);
            }
            ?>
        </div>
        <!-- FIM PROPONENTE -->
    </div>
</div>