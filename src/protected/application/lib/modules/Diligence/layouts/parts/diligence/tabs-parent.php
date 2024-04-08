<?php

use MapasCulturais\i;
use Diligence\Entities\Diligence as EntityDiligence;



$this->jsObject['isProponent'] = EntityDiligence::isProponent($diligenceRepository, $entity);

$app->view->enqueueScript('app', 'diligence', 'js/diligence/diligence.js');
?>
<script>
    $(document).ready(function() {
               
    });

</script>

<?php 


    $this->applyTemplateHook('tabs', 'before');
    $this->part('diligence/ul-buttons');
?>

<div class="tabs-content">
    <div id="diligence-principal">

    </div>
    <div id="diligence-diligence">
        <label>
            <strong>
                <?php
                i::_e('Diligência ao proponente')
                ?>
            </strong>
        </label>
        <div class="div-diligence" id="div-diligence">
            <p id="paragraph_info_status_diligence"></p>
        </div>
        <div style="margin-top: 30px; width: 100%;" id="div-content-all-diligence-send">
            <label class="label-diligence-send">Diligência:</label>
            <p id="paragraph_content_send_diligence"></p>
            <p id="paragraph_createTimestamp" class="paragraph-createTimestamp"></p>

            <div style="width: 100%;  display: flex;justify-content: space-between;flex-wrap: wrap; ">
                <div class="item-col"></div>
                <div class="item-col" style="padding: 8px;">
                    <p>
                        <?php
                        EntityDiligence::infoTerm($entity, $diligenceRepository, $term);
                        ?>
                    </p>
                </div>
                <div class="item-col"></div>
            </div>
            <div id="answer_diligence" style="padding: 20px;justify-content: space-between;flex-wrap: wrap; margin-top: 10px; background:#F3F3F3">
                <label for="" style="font-size: 14px; font-weight: 700;  font-family: inherit;  line-height: 19.07px;">
                    Resposta do Proponente:
                </label>
                <p id="paragraph_content_send_answer" style="color: #3E3E3E;  font-weight: 400;  font-size: 14px;  line-height: 19.07px; margin-top:10px"></p>
            </div>
            <div id="div-info-send" class="div-info-send">
                <p>
                    <?php i::_e('Sua diligência já foi enviada') ?>
                </p>
            </div>
        </div>
        <div>
            <textarea name="description" id="descriptionDiligence" cols="30" rows="10" placeholder="<?= $placeHolder; ?>" class="diligence-context-open"></textarea>
        </div>

        <div class="div-btn-send-diligence flex-container">
            <div class="flex-items" id="btn-actions-diligence">
                <?php $this->part('diligence/btn-actions-diligence'); ?>
            </div>
            <!-- PARA O PROPONENTE -->
            <div class="flex-items" id="btn-actions-proponent">
                <?php $this->part('diligence/btn-actions-proponent', ['entity' => $entity]); ?>
            </div>
            <!-- FIM PROPONENTE -->
        </div>
       
        <script>

          

           
            

            function saveAnswerProponente(status) {
                $.ajax({
                    type: "POST",
                    url: MapasCulturais.createUrl('diligence', 'answer'),
                    data: {
                        diligence: idDiligence,
                        answer: $("#descriptionDiligence").val(),
                        status: status
                    },
                    dataType: "json",
                    success: function(response) {
                        showSaveContent(status);
                        console.log({
                            response
                        })
                    }
                });
            }

            // function getContentDiligence() {
            //     $.ajax({
            //         type: "GET",
            //         url: MapasCulturais.createUrl('diligence', 'getcontent/' + MapasCulturais.entity.id),
            //         dataType: "json",
            //         success: function(res) {
            //             // console.log({
            //             //     res
            //             // })
            //             return res
            //             // switch (res.message) {
            //             //     case 'sem_diligencia':
            //             //         idDiligence = 0;
            //             //         break;
            //             //     case 'diligencia_aberta':
            //             //         idDiligence = res.data[0].id;
            //             //         break;
            //             //     case 'resposta_rascunho':
            //             //         idDiligence = res.data[0].diligence.id;
            //             //         break;
            //             //     default:
            //             //         break;
            //             // }

            //             //Sem diligência ou diligencia aberta - Ação para o proponente
            //             // if (
            //             //     (res.message == 'sem_diligencia' || res.message == 'diligencia_aberta') &&
            //             //     MapasCulturais.userEvaluate == false) {
            //             //     $("#li-tab-diligence-diligence > a").remove();
            //             //     $("#li-tab-diligence-diligence").append('<label>Diligência</label>');
            //             //     $("#li-tab-diligence-diligence > label").addClass('cursor-disabled');
            //             // }
            //             //Sem diligência ou diligencia aberta - Ação para o avaliador
            //             console.log((res.message == 'diligencia_aberta' || res.message == 'resposta_rascunho'))
            //             console.log(MapasCulturais.userEvaluate == true)
            //             console.log(res.data[0].status == 0);
            //             // if (
            //             //     (res.message == 'sem_diligencia' || res.message == 'diligencia_aberta') &&
            //             //     MapasCulturais.userEvaluate == true) {
            //             //     $("#btn-actions-proponent").hide();
            //             //     $("#descriptionDiligence").val(res.data[0].description);
            //             //     $("#btn-save-diligence").show();
            //             //     $("#paragraph_info_status_diligence").html('A sua Diligência ainda não foi enviada');
            //             // }

            //             //Formatando página para o parecerista
            //             if (
            //                 res.data[0].status == 3
            //             ) {
            //                 $("#descriptionDiligence").hide();
            //                 $("#paragraph_info_status_diligence").hide();
            //                 $("#paragraph_content_send_diligence").html(res.data[0].description);
            //                 $("#div-content-all-diligence-send").show();
            //                 $("#paragraph_createTimestamp").html(moment(res.data[0].sendDiligence.date).format("LLL"));
            //                 $("#div-info-send").show();
            //                 $("#btn-save-diligence").hide();
            //                 $("#btn-send-diligence").hide();
            //             }
            //             //Para proponente
            //             // if (
            //             //     res.message == 'resposta_enviada' &&
            //             //     res.data[1].status == 3 && res.data[0].status == 3
            //             // ) {
            //             //     $("#li-tab-diligence-diligence").append('');
            //             //     console.log(res.data[1].answer);
            //             //     $("#descriptionDiligence").hide();
            //             //     $("#btn-send-diligence-proponente").hide();
            //             //     $("#paragraph_content_send_answer").html(res.data[1].answer);
            //             //     $("#paragraph_createTimestamp").html(moment(res.data[0].sendDiligence.date).format("LLL"));
            //             // }
                      
            //             // if (
            //             //     (res.message == 'diligencia_aberta' || res.message == 'resposta_rascunho') &&
            //             //     MapasCulturais.userEvaluate == false &&
            //             //     res.data[0].status == 0
            //             // ) {
            //             //     let description = '';
            //             //     if (res.message == 'diligencia_aberta') {
            //             //         description = res.data[0].description;
            //             //     } else {
            //             //         description = res.data[0].diligence.description;
            //             //     }
            //             //     const anchor = '<a href="#diligence-diligence" rel="noopener noreferrer" onclick="hideRegistration()"' +
            //             //         'id="tab-main-content-diligence-diligence">Diligência</a>';
            //             //     $("#li-tab-diligence-diligence > label").removeClass('cursor-disabled');
            //             //     $("#li-tab-diligence-diligence > label").remove();
            //             //     $("#li-tab-diligence-diligence").append(anchor);
            //             //     $("#div-info-send").hide();
            //             //     $("#btn-save-diligence").hide();
            //             //     $("#btn-send-diligence").hide();
            //             //     $("#paragraph_content_send_diligence").html(description);
            //             //     $("#descriptionDiligence").show();
            //             //     $("#div-content-all-diligence-send").show();
            //             // }
            //         }
            //     });
            // }

            function sendFileDiligence() {
                console.log('sendFileDiligence')
            }
        </script>
    </div>
</div>
<?php $this->applyTemplateHook('tabs', 'after'); ?>