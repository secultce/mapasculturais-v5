<?php

use MapasCulturais\i;
use MapasCulturais\App;
use Diligence\Entities\Diligence as EntityDiligence;
use Diligence\Repositories\Diligence as RepoDiligence;
use Diligence\Entities\AnswerDiligence;
use Carbon\Carbon;


$diligenceAndAnswers = RepoDiligence::getDiligenceAnswer($entity->id);

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
            i::_e('Diligencia Enviadas:');
            dump($diligenceAndAnswers);
            ?>
        </strong>
    </label>
    <div class="div-diligence" id="div-diligence">
        <p id="paragraph_info_status_diligence"></p>
    </div>
    <div style="margin-top: 30px; width: 100%;" id="div-content-all-diligence-send">
        <!-- <label class="label-diligence-send">Diligência:</label> -->
        <!-- <p id="paragraph_content_send_diligence"></p>
    <p id="paragraph_createTimestamp" class="paragraph-createTimestamp"></p> -->
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
    <div id="accordion" class="head">
        <?php

        foreach ($diligenceAndAnswers as $key => $results) :
            Carbon::setLocale('pt_BR');
            $dt = null;
            $dtSend = "";
            // dump($results->sendDiligence == null);
            if ($results !== null) {
                $dt = Carbon::parse($results->sendDiligence);
                $dtSend = $dt->isoFormat('LLL');
                $dtAndwer = Carbon::parse($results->sendDiligence);
                $dtSendAnswer = $dtAndwer->isoFormat('LLL');
            }
            // if (is_null($results)) {
            //     echo ' <div style="display: flex; justify-content: space-between;" id="div-accordion-diligence" class="div-accordion-diligence">
            //     <label style="font-size: 14px">Aguardando Resposta</label>
            //     <label style="color: #085E55; font-size: 14px" class="title-hide-show-accordion">Visualizar <i class="fas fa-angle-down arrow"></i></label>
            // </div><div class="content"><p>Aguardando resposta</p></div>';
            // }
            if ($results instanceof EntityDiligence && !is_null($results) && $results->status == 3) {
        ?>
                <div style="display: flex; justify-content: space-between;" id="div-accordion-diligence" class="div-accordion-diligence">
                    <label style="font-size: 14px">Diligência </label>
                    <label style="color: #085E55; font-size: 14px" class="title-hide-show-accordion">Visualizar <i class="fas fa-angle-down arrow"></i></label>
                </div>
                <div class="content">
                    <p>
                        <?php
                        echo $results->description;

                        ?>
                    </p>
                    <p id="paragraph_createTimestamp_answer" class="paragraph-createTimestamp">
                        <?php echo $dtSend;   ?>
                    </p>
                </div>
            <?php
            }



            if ($results instanceof AnswerDiligence && !is_null($results) && $results->status == 3) {
            ?>
                <div style="display: flex; justify-content: space-between;" id="div-accordion-diligence" class="div-accordion-diligence">
                    <label style="font-size: 14px">
                        <strong>Resposta Recebida</strong>
                    </label>
                    <label style="color: #085E55; font-size: 14px" class="title-hide-show-accordion">Visualizar <i class="fas fa-angle-down arrow"></i></label>
                </div>
                <div class="content" style="background-color: #F3F3F3;">
                    <p>
                        <?php
                        echo $results->answer;

                        ?>
                    </p>
                    <p id="paragraph_createTimestamp_answer" class="paragraph-createTimestamp">
                        <?php echo $dtSendAnswer;   ?>
                    </p>
                </div>
        <?php
            }


        endforeach; ?>
    </div>
    <?php foreach ($diligenceAndAnswers as $key => $results) {

        if ($results instanceof EntityDiligence && !is_null($results) && $results->status == 0) :
            $dateDraft = Carbon::parse($results->createTimestamp)->diffForHumans();

    ?>

            <div id="draft-description-diligence" class="div-draft-description-diligence">
                <span style="font-size: small; color: #085E55">Diligência em rascunho. <br /></span>
                <p style="padding: 5px;"><?= $results->description; ?> </p>
                <p style="font-size: x-small;"><?= ucfirst($dateDraft); ?> </p>
                <p>
                    <a class="edit-draft-descrption0-diligence" 
                        onclick='editDescriptionDiligence(<?php echo json_encode($results->description); ?>,<?= $results->id; ?>)'
                    >
                        Editar
                    </a>
                </p>
            </div>
    <?php
        endif;
    };
    ?>

    <div>
        <?php if (!is_null($results)) : ?>
            <textarea name="description" id="descriptionDiligence" cols="30" rows="10" placeholder="<?= $placeHolder; ?>" class="diligence-context-open"></textarea>
            <input type="text" id="id-input-diligence">
        <?php endif; ?>
    </div>
    <label class="diligence-label-save" id="label-save-content-diligence">
        <i class="fas fa-check-circle mr-10"></i>
        Suas alterações foram salvas
    </label>

<?php endif; ?>