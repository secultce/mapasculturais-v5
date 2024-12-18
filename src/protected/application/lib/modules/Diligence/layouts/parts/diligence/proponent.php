<?php

/**
 * @var App $app
 * @var array<string,mixed> $context
 * @var (Diligence|AnswerDiligence)[] $diligenceAndAnswers
 * @var bool $sendEvaluation
 */

use Carbon\Carbon;
use MapasCulturais\App;
use Diligence\Entities\AnswerDiligence;
use Diligence\Entities\Diligence;
use Diligence\Repositories\Diligence as RepoDiligence;

$app->view->enqueueScript('app', 'diligence', 'js/diligence/proponent.js');
$placeHolder = "Digite aqui a sua resposta";

Carbon::setLocale('pt_BR');
$tado = RepoDiligence::getTado($context['entity']);
$this->applyTemplateHook('tabs', 'before');
$this->part('diligence/ul-buttons', ['entity' => $context['entity'], 'sendEvaluation' => $sendEvaluation]);
?>

<?php $this->applyTemplateHook('tabs', 'after'); ?>
    <div id="diligence-diligence" class="aba-content">
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
        if (!is_null($diligenceAndAnswers)) {
            $diligenceAndAnswerLast = [];
            foreach ($diligenceAndAnswers as $key => $resultsDraft) {
                if ($key < 2) {
                    $diligenceAndAnswerLast[] = $resultsDraft;
                }
            };
            $diligence_days = AnswerDiligence::setNumberDaysAnswerDiligence(
                $diligenceAndAnswerLast[0]->sendDiligence,
                $context['entity']->opportunity->getMetadata('diligence_days'),
                $context['entity']->opportunity->getMetadata('type_day_response_diligence')
            );

            if (!is_null($diligenceAndAnswerLast[1]) && $diligenceAndAnswerLast[1]->status == AnswerDiligence::STATUS_DRAFT) {
                $dateDraft = Carbon::parse($diligenceAndAnswerLast[1]->createTimestamp)->diffForHumans();

                if (($tado === null || $tado->status === 0) && new DateTime() <= $diligence_days) {
                    $titleButton = 'Editar Resposta';
                } else {
                    $titleButton = 'expirou';
                }

                $this->part('diligence/edit-description', [
                    'titleDraft' => 'Resposta em rascunho.',
                    'titleTrash' => '',
                    'titleButton' => $titleButton,
                    'resultsDraft' => $diligenceAndAnswerLast[1]->answer,
                    'id' => $diligenceAndAnswerLast[1]->id,
                    'type' => "proponent",
                    'dateDraft' => ucfirst($dateDraft)
                ]);
                $showText = true;
            }
            
            //Quando tem diligencia, mas ainda não tem resposta
            if (is_null($diligenceAndAnswerLast[1]) && $diligenceAndAnswerLast[0]->status == Diligence::STATUS_SEND) {
                $showText = true;
                if (new DateTime() > $diligence_days) {
                    $showText = false;
                }
                if (is_null($tado) || $tado->status == 0) :
                    $this->part('diligence/info-term',[
                        'entity' => $context['entity'],
                        'diligenceRepository' => $context['diligenceRepository'],
                        'diligenceDays' => $diligence_days,
                    ]);
                endif;
            }
            if (!is_null($diligenceAndAnswerLast[1]) && $diligenceAndAnswerLast[0]->status == Diligence::STATUS_SEND) {
               
                new DateTime() <= $diligence_days ? $showText = true : $showText = false;
                if (is_null($tado) || $tado->status == 0) :
                    $this->part('diligence/info-term', [
                        'entity' => $context['entity'],
                        'diligenceRepository' => $context['diligenceRepository'],
                        'diligenceDays' => $diligence_days
                    ]);
                endif;
            }
        }
        //Se tiver TADO finalizado não tem mais interação
        if (is_null($tado) || $tado->status == 0) : ?>
        <div class="flex-container" id="btn-actions-proponent">
            <?php
            if ($showText || is_null($diligenceAndAnswers)) {
                $this->part('diligence/description', ['placeHolder' => $placeHolder]);
                $this->part('diligence/message-success-draft');
                $this->part('diligence/btn-actions-proponent', ['entity' => $context['entity'], 'showText' => $showText]);
            }
            ?>
        </div>
        <?php endif; ?>
        <!-- FIM PROPONENTE -->
    </div>

