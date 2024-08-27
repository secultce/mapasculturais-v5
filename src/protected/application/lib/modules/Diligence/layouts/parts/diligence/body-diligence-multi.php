<?php

use MapasCulturais\i;
use Diligence\Entities\Diligence as EntityDiligence;
use Diligence\Entities\AnswerDiligence;
use Carbon\Carbon;
use Diligence\Entities\Tado;
use Diligence\Repositories\Diligence as DiligenceRepo;

$this->applyTemplateHook('body-diligence-multi', 'before');
$this->applyTemplateHook('body-diligence-multi-div', 'begin');
?>
    <div>
        <hr>
    </div>
    <div class="import-financial-report" style="margin-top: 15px">

        <?php
        $financialReportsAccountability = DiligenceRepo::getFinancialReportsAccountability($entity->id);
        $generatedTado = DiligenceRepo::getTado($entity);

        $delBtn = '
                    <div class="delete-financial-report-btn">
                        <a delete-financial-report data-file-id="%s" class="icon icon-close hltip" title="Excluir arquivo">
                        </a>
                    </div>
                ';
        $showDelBtn = !$generatedTado || $generatedTado->status !== Tado::STATUS_ENABLED ? $delBtn : '';

        if ($financialReportsAccountability) {
            foreach ($financialReportsAccountability as $financialReportAccountability) {
                $file_id = $financialReportAccountability->id;
                echo '
                            <div class="financial-report-wrapper" id="financial-report-wrapper">
                                <i class="fas fa-download" style="margin-right: 10px;"></i>
                                <a href="/arquivos/privateFile/' . $file_id . '" target="_blank" rel="noopener noreferrer">
                                    relatorio_financeiro.pdf
                                </a>
                                ' . sprintf($showDelBtn, $file_id) . '
                            </div>
                        ';
            }
        }
        ?>
    </div>

<?php
if ($diligenceAndAnswers) :
    if ($diligenceAndAnswers[0]->status == EntityDiligence::STATUS_SEND) : ?>
        <div>

            <h5>
                <?php i::_e('Diligências enviadas'); ?>
            </h5>
            <div style="margin-top: 25px;">
                <div style="font-size: 14px; padding: 10px; margin-bottom: 10px;">
                    <label>
                        <b>
                            Diligência (atual):
                        </b>
                    </label>
                    <label for="">
                        <strong>Assunto(s): </strong>
                        <?php
                            //Condição para mostrar na página o assunto
                            if($diligenceAndAnswers) {
                                echo $diligenceAndAnswers[0]->getSubject();
                            }
                        ?>
                    </label>
                    <p style="margin: 10px 0px;">
                        <?php echo $diligenceAndAnswers[0]->description; ?>
                    </p>
                    <span style="font-size: 12px; font-weight: 700; color: #404040;">
                        <?php echo Carbon::parse($diligenceAndAnswers[0]->sendDiligence)->isoFormat('LLL'); ?>
                    </span>
                </div>
                <?php if (!is_null($diligenceAndAnswers[1]) && $diligenceAndAnswers[1]->status == AnswerDiligence::STATUS_SEND) : ?>
                    <div style="font-size: 14px; background-color: #F5F5F5; padding: 10px;">
                        <label>
                            <b>Resposta recebida:</b>
                        </label>
                        <p style="margin: 10px 0px;">
                            <?php echo $diligenceAndAnswers[1]->answer; ?>
                        </p>
                        <?php
                        $files = DiligenceRepo::getFilesDiligence($diligenceAndAnswers[1]->diligence->id);

                        foreach ($files as $file) {
                            echo '
                                <p style="margin-bottom: 10px;">
                                    <a href="/arquivos/privateFile/' . $file["id"] . '" target="_blank" rel="noopener noreferrer">
                                        ' . $file["name"] . '
                                    </a>
                                </p>
                            ';
                        }
                        ?>
                        <span style="font-size: 12px; font-weight: 700; color: #404040;">
                            <?php echo Carbon::parse($diligenceAndAnswers[1]->createTimestamp)->isoFormat('LLL'); ?>
                        </span>
                    </div>
                <?php else : ?>
                    <div style="text-align: center; border: 1px solid #ccc; border-radius: 5px; padding: 25px;">
                        <p>Sua diligência foi enviada.</p>
                        <p>
                            <b>Aguarde a resposta.</b>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
    <?php
    if (!is_null($diligenceAndAnswers) && count($diligenceAndAnswers) > 2) : ?>
        <div style="display: flex; justify-content: center; margin-top: 10px; margin-bottom: 10px; color: green;">
            <p style="color: #085E55; font-weight: 700; font-size: 14px;">
                Mensagens mais antigas
            </p>
            <p>
                <br>
            </p>
        </div>

        <div id="accordion" class="head">
            <?php
            foreach ($diligenceAndAnswers as $key => $resultsDiligence) :
                Carbon::setLocale('pt_BR');
                $dt = null;
                $dtSend = "";

                if ($resultsDiligence !== null) {
                    $dt             = Carbon::parse($resultsDiligence->sendDiligence);
                    $dtSend         = $dt->isoFormat('LLL');
                }

                if ($key > 1) :
                    if ($resultsDiligence instanceof EntityDiligence && !is_null($resultsDiligence) && $resultsDiligence->status == EntityDiligence::STATUS_SEND) : ?>
                        <div style="display: flex; justify-content: space-between;" class="div-accordion-diligence">
                            <label style="font-size: 14px">
                                <b>Diligência:</b>
                            </label>
                            <label style="color: #085E55; font-size: 14px" class="title-hide-show-accordion">Visualizar <i class="fas fa-angle-down arrow"></i></label>
                        </div>
                        <div class="content">
                            <p>
                                <label for="">
                                    <strong>Assunto(s): </strong>
                                    <?php echo $resultsDiligence->getSubject(); ?>
                                </label>
                            </p>
                            <p>
                                <?php echo $resultsDiligence->description; ?>
                            </p>
                            <p class="paragraph-createTimestamp paragraph_createTimestamp_answer">
                                <?php echo $dtSend; ?>
                            </p>
                        </div>
                    <?php
                    endif;

                    if ($resultsDiligence instanceof AnswerDiligence && !is_null($resultsDiligence) && $resultsDiligence->status == AnswerDiligence::STATUS_SEND) :
                        $dtAnswer = Carbon::parse($resultsDiligence->createTimestamp);
                        $dtSendAnswer = $dtAnswer->isoFormat('LLL');
                    ?>
                        <div style="display: flex; justify-content: space-between;" class="div-accordion-diligence">
                            <label style="font-size: 14px">
                                <b>Resposta recebida:</b>
                            </label>
                            <label style="color: #085E55; font-size: 14px" class="title-hide-show-accordion">
                                Visualizar <i class="fas fa-angle-down arrow"></i>
                            </label>
                        </div>
                        <div class="content" style="font-size: 14px; background-color: #F5F5F5; padding: 10px;">
                            <p style="margin: 10px 0px;">
                                <?php echo $resultsDiligence->answer; ?>
                            </p>
                            <?php
                                //Passando a diligencia da resposta para verificar retornar arquivo se houver
                                $this->part('diligence/body-diligence-files',[
                                        'entityAnswer' => $resultsDiligence
                                ])
                            ?>
                            <span style="font-size: 12px; font-weight: 700; color: #404040;">
                                <?php echo $dtSendAnswer; ?>
                            </span>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<div class="div-diligence" id="div-diligence">
    <p id="paragraph_info_status_diligence"></p>
    <?php
    //Array para marcar como confirmado a opção
    isset($diligenceAndAnswers[0]) ?  $subjectReplace = $diligenceAndAnswers[0]->subjectToArray() : $subjectReplace = [];
    isset($diligenceAndAnswers[0]) ? $checked = $diligenceAndAnswers[0]->getCheckSubject($subjectReplace) : $checked = ['checkPhysical' => "checked", 'checkFinance' => ""];

    $generatedTado = DiligenceRepo::getTado($entity);

    if (!is_null($diligenceAndAnswers[1]) || isset($diligenceAndAnswers[1]->status)) {
        if(!is_null($generatedTado) && $generatedTado->status == EntityDiligence::STATUS_DRAFT)
        {
            $this->part(
                'diligence/body-diligence-subject',
                [
                    'checkPhysical' => $checked['checkPhysical'],
                    'checkFinance' => $checked['checkFinance'],
                ]
            );
        }else{
            //Se não tiver tado, mas se tiver resposta
            $this->part(
                'diligence/body-diligence-subject',
                [
                    'checkPhysical' => $checked['checkPhysical'],
                    'checkFinance' => $checked['checkFinance'],
                ]
            );
        }

    } else {
        //Mostrará assunto quando não tiver diligencia ou quando editar rascunho
        $this->part(
            'diligence/body-diligence-subject',
            [
                'checkPhysical' => $checked['checkPhysical'],
                'checkFinance' => $checked['checkFinance'],
            ]
        );
    }
    ?>
</div>

<?php
$this->applyTemplateHook('body-diligence-multi-div', 'end');
$this->applyTemplateHook('body-diligence-multi', 'after'); ?>