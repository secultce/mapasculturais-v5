<?php

use MapasCulturais\i;
use Diligence\Entities\Diligence as EntityDiligence;
use Diligence\Entities\AnswerDiligence;
use Carbon\Carbon;

$descriptionDraft = true;
if (!$sendEvaluation) :
?>
    <p id="paragraph_loading_content">
        <label for="">
            Carregando ... <img id="img-loading-content" />
        </label>
        <br />
        <br />
    </p>
    <label>
        <strong>
            <?php
            i::_e('Diligências enviadas:');
            ?>
        </strong>
    </label>
    <div class="div-diligence" id="div-diligence">
        <p id="paragraph_info_status_diligence"></p>
    </div>
    <div style="margin-top: 30px; width: 100%;" id="div-content-all-diligence-send">
        <div style="width: 100%;  display: flex;justify-content: space-between;flex-wrap: wrap; ">
            <div class="item-col"></div>
            <div class="item-col" style="padding: 8px;">
                <p style="font-size: smaller;">
                    <?php
                    EntityDiligence::infoTerm($entity, $diligenceRepository, $diligenceDays);
                    ?>
                </p>
            </div>
            <div class="item-col"></div>
        </div>
        <div id="answer_diligence" class="answer_diligence">
            <label for="" style="font-size: 14px; font-weight: 700;  font-family: inherit;  line-height: 19.07px;">
                Resposta do Proponente:
            </label>
            <p id="paragraph_content_send_answer" class="paragraph_content_send_answer"></p>
        </div>
    </div>
    <?php if(!is_null($diligenceAndAnswers)) : ?>
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
            
            if ($resultsDiligence instanceof EntityDiligence && !is_null($resultsDiligence) && $resultsDiligence->status == 3) { ?>
                <div style="display: flex; justify-content: space-between;" class="div-accordion-diligence">
                    <label style="font-size: 14px">Diligência </label>
                    <label style="color: #085E55; font-size: 14px" class="title-hide-show-accordion">Visualizar <i class="fas fa-angle-down arrow"></i></label>
                </div>
                <div class="content">
                    <p>
                        <?php
                        echo $resultsDiligence->description;
                        ?>
                    </p>
                    <p class="paragraph-createTimestamp paragraph_createTimestamp_answer">
                        <?php echo $dtSend; ?>
                    </p>
                </div>
            <?php
            }
            if ($resultsDiligence instanceof AnswerDiligence && !is_null($resultsDiligence) && $resultsDiligence->status == 3) {
                $dtAnswer       = Carbon::parse($resultsDiligence->createTimestamp);
                $dtSendAnswer   = $dtAnswer->isoFormat('LLL');
            ?>
                <div style="display: flex; justify-content: space-between;" class="div-accordion-diligence">
                    <label style="font-size: 14px">
                        <strong>Resposta Recebida</strong>
                    </label>
                    <label style="color: #085E55; font-size: 14px" class="title-hide-show-accordion">Visualizar <i class="fas fa-angle-down arrow"></i></label>
                </div>
                <div class="content" style="background-color: #F3F3F3;">
                    <p>
                        <?php
                        echo $resultsDiligence->answer;
                        ?>
                    </p>
                    <p class="paragraph-createTimestamp paragraph_createTimestamp_answer">
                        <?php echo $dtSendAnswer;   ?>
                    </p>
                </div>
            <?php
            }
            if (is_null($resultsDiligence))
            {
                $descriptionDraft = false;
                echo ' <div style="display: flex; justify-content: space-between;" class="div-accordion-diligence">
                    <label style="font-size: 14px">Aguardando Resposta.</label>
                    <label style="color: #085E55; font-size: 14px" class="title-hide-show-accordion">Visualizar <i class="fas fa-angle-down arrow"></i></label>
                </div><div class="content"><p>Aguardando resposta</p></div>';
                
            }
            if ($resultsDiligence instanceof AnswerDiligence && !is_null($resultsDiligence) && $resultsDiligence->status == 0)
            {
                $descriptionDraft = false;
                echo ' <div style="display: flex; justify-content: space-between;" class="div-accordion-diligence">
                <label style="font-size: 14px">Aguardando Resposta.</label>
                <label style="color: #085E55; font-size: 14px" class="title-hide-show-accordion">Visualizar <i class="fas fa-angle-down arrow"></i></label>
            </div><div class="content"><p>Aguardando resposta</p></div>';            
        }
        endforeach; ?>
        </div>
   
    <?php 
    endif;//endif id=accordion
    ?>
    
<?php endif; ?>