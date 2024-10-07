<?php

use Carbon\Carbon;
use Diligence\Entities\AnswerDiligence;
use Diligence\Entities\Diligence;
use MapasCulturais\Entity;
use MapasCulturais\i;
use Diligence\Entities\Diligence as EntityDiligence;
use Diligence\Repositories\Diligence as DiligenceRepo;

/**
 * @var array<string, mixed> $context
 * @var bool $sendEvaluation
 * @var (Diligence|AnswerDiligence)[]|null $diligenceAndAnswers
 */

$files = DiligenceRepo::getFilesDiligence($context['entity']->id);
Carbon::setLocale('pt_BR');

$this->jsObject['isProponent'] = EntityDiligence::isProponent($context['diligenceRepository'], $context['entity']);
$showText = false;
$placeHolder = "Digite aqui a sua diligência";

//Tado
$tado = DiligenceRepo::getTado($context['entity']);

?>
<?php
$this->applyTemplateHook('tabs', 'before');
$this->part('diligence/ul-buttons', ['entity' => $context['entity'], 'sendEvaluation' => $sendEvaluation]);
?>

<div class="tabs-content">
    <div id="diligence-principal"></div>
    <div id="diligence-diligence">
        <p id="paragraph_loading_content">
            <label for="">
                Carregando ... <img id="img-loading-content" />
            </label>
            <br />
            <br />
        </p>
        <?php
        if (!is_null($diligenceAndAnswers)) {
            $diligenceAndAnswerLast = [];
            foreach ($diligenceAndAnswers as $key => $resultsDraft) {
                //Array somente com os dois ultimos registros
                if ($key < 2) {
                    array_push($diligenceAndAnswerLast, $resultsDraft);
                }
            };
            if ($diligenceAndAnswerLast[0]->status === 0) {
                $dateDraft = Carbon::parse($diligenceAndAnswerLast[0]->createTimestamp)->diffForHumans();
                // @todo: Talvez essa não seja a melhor lógica, alterar depois
                if ($tado !== null && $tado->status !== 0) {
                    $titleButton = 'expirou';
                }
                $this->part('diligence/edit-description', [
                    'titleDraft' => 'Diligência em rascunho.',
                    'titleButton' => $titleButton ?? 'Editar Diligência',
                    'titleTrash' => 'Excluir Rascunho',
                    'resultsDraft' => $diligenceAndAnswerLast[0]->description,
                    'id' => $diligenceAndAnswerLast[0]->id,
                    'type' => "diligence",
                    'dateDraft' => ucfirst($dateDraft),
                ]);
                $showText = true;
            }

            if (!is_null($diligenceAndAnswerLast[1]) && $diligenceAndAnswerLast[1]->status == 3) {
                $showText = true;
            }
        }
        ?>
        <?php
            $diligenceType = $context['entity']->opportunity->use_multiple_diligence == 'Sim' ? 'multi' : 'common';
            $this->part("diligence/body-diligence-$diligenceType", [
                'entity' => $context['entity'],
                'diligenceRepository' => $context['diligenceRepository'],
                'diligenceDays' => $context['diligenceDays'],
                'placeHolder' => $context['placeHolder'],
                'sendEvaluation' => $sendEvaluation,
                'isProponent' => $context['isProponent'],
                'diligenceAndAnswers' => $diligenceAndAnswers,
            ]);
        ?>
        <div id="div-info-send" class="div-info-send">
            <p>
                <?php i::_e('Sua diligência já foi enviada') ?>
            </p>
        </div>
        <div class="div-btn-send-diligence flex-container">
            <?php
                //Se tiver TADO finalizado não tem mais interação
                if(
                    is_null($tado) 
                    || $tado->status == 0
                    || $diligenceAndAnswers[0] === null
                    || $diligenceAndAnswers[0]->status !== Entity::STATUS_TRASH
                ) :
            ?>
            <div class="d-none" id="btn-actions-diligence">
                <?php
                if ($showText || is_null($diligenceAndAnswers)) {
                    $this->part('diligence/description', ['placeHolder' => $placeHolder]);
                    $this->part('diligence/message-success-draft');
                    $this->part('diligence/btn-actions-diligence', [
                        'entity' => $context['entity'],
                        'sendEvaluation' => $sendEvaluation
                    ]);
                }
                ?>
            </div>
            <?php endif; ?>
            <div class="import-diligence" style="width: 100%">
                <?php
                foreach ($files as $file) {
                    $id = $file["id"];
                    echo  '<article id="file-diligence-up-' . $id . '" class="objeto" style="margin-top: 20px;">
                    <span>Arquivo</span>
                    <h1><a href="/arquivos/privateFile/' . $id . '" 
                    class="attachment-title ng-binding ng-scope" target="_blank" rel="noopener noreferrer" 
                    >' . $file["name"] . '</a></h1> 
                    <div class="botoes footer-btn-delete-file-diligence">
                       
                    </div>
                </article>';
                } ?>
            </div>
        </div>
    </div>
</div>
<?php $this->applyTemplateHook('tabs', 'after'); ?>
