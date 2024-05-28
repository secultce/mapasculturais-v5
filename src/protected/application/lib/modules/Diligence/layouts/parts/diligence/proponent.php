<?php

use Carbon\Carbon;
use MapasCulturais\App;
use Diligence\Entities\AnswerDiligence;
use Diligence\Entities\Diligence;

$diligence = App::i()->repo('Diligence\Entities\AnswerDiligence')->find(27);

$app->view->enqueueScript('app', 'diligence', 'js/diligence/proponent.js');
$placeHolder = "Digite aqui a sua resposta";

Carbon::setLocale('pt_BR');

$this->applyTemplateHook('tabs', 'before');
$this->part('diligence/ul-buttons', ['entity' => $context['entity'], 'sendEvaluation' => $sendEvaluation]);
?>

<?php $this->applyTemplateHook('tabs', 'after'); ?>
<div class="tabs-content">
    <div id="diligence-principal"></div>
    <div id="diligence-diligence">
        <!-- PARA O PROPONENTE -->
        <?php
        $diligenceType = $context['entity']->opportunity->use_multiple_diligence == 'Sim' ? 'multi' : 'common';
        $this->part("diligence/body-proponent-$diligenceType", [
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
        // dump($diligenceAndAnswers);
        if (!is_null($diligenceAndAnswers)) {
            $diligenceAndAnswerLast = [];
            foreach ($diligenceAndAnswers as $key => $resultsDraft) {
                if ($key < 2) {
                    array_push($diligenceAndAnswerLast, $resultsDraft);
                }
            };
            $diligence_days = AnswerDiligence::vertifyWorkingDays($diligenceAndAnswerLast[0]->sendDiligence, $context['entity']->opportunity->getMetadata('diligence_days'));

            if (!is_null($diligenceAndAnswerLast[1]) && $diligenceAndAnswerLast[1]->status == 0) {
                $dateDraft = Carbon::parse($diligenceAndAnswerLast[1]->createTimestamp)->diffForHumans();
                if(new DateTime() <= $diligence_days){
                    $titleButton = 'Editar Resposta';
                }else{
                    $titleButton = 'expirou';                    
                }
                $this->part('diligence/edit-description', [
                    'titleDraft' => 'Resposta em rascunho.',
                    'titleButton' => $titleButton,
                    'resultsDraft' => $diligenceAndAnswerLast[1]->answer,
                    'id' => $diligenceAndAnswerLast[1]->id,
                    'type' => "proponent",
                    'dateDraft' => ucfirst($dateDraft)
                ]);
                $showText = true;
            }
            
            //Quando tem diligencia mas ainda não tem resposta
            if (is_null($diligenceAndAnswerLast[1]) && $diligenceAndAnswerLast[0]->status == 3) {
                $showText = true;
                new DateTime() <= $diligence_days ? $showText = true : $showText = false;
                $this->part('diligence/info-term',[
                    'entity' => $context['entity'],
                    'diligenceRepository' => $context['diligenceRepository'],
                    'diligenceDays' => $diligence_days
                ]);
            }
            if (!is_null($diligenceAndAnswerLast[1]) && $diligenceAndAnswerLast[0]->status == 3) {
                // dump($diligenceAndAnswerLast[0]->sendDiligence);
               
                new DateTime() <= $diligence_days ? $showText = true : $showText = false;
                $this->part('diligence/info-term',[
                    'entity' => $context['entity'],
                    'diligenceRepository' => $context['diligenceRepository'],
                    'diligenceDays' => $diligence_days
                ]);
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