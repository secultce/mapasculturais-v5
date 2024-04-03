<?php

use MapasCulturais\i;
use Diligence\Entities\Diligence as EntityDiligence;

$this->jsObject['userEvaluate'] = $entity->canUser('evaluate', $app->user);
$this->jsObject['isProponent'] = EntityDiligence::isProponent($diligenceRepository);
?>
<script>
    $(document).ready(function() {

        $("#btn-save-diligence").hide();
        $("#label-save-content-diligence").hide();
        $("#div-info-send").hide();
        $("#div-content-all-diligence-send").hide();
        $("#descriptionDiligence").show();
        if ($(this).val() > 0) {
            $("#btn-save-diligence").show();
        }

        getContentDiligence();
        $("#descriptionDiligence").on("keyup", function() {
            var texto = $(this).val(); // Obtém o valor do textarea
            if (texto.slice(-1) == '') {
                $("#btn-save-diligence").hide()
            } else {
                $("#btn-save-diligence").show()
            }
        });
    });
</script>
<?php $this->applyTemplateHook('tabs', 'before'); ?>
<ul class="abas clearfix">
    <li class="active">
        <a href="#diligence-principal" rel="noopener noreferrer" onclick="showRegistration()" 
        id="tab-main-content-diligence-principal">Ficha</a>
    </li>
    <li class="" id="li-tab-diligence-diligence">
        <a href="#diligence-diligence" rel="noopener noreferrer" onclick="hideRegistration()" 
        id="tab-main-content-diligence-diligence">Diligência</a>
    </li>
</ul>
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
            <div style="width: 100%; display: flex;justify-content: space-between;flex-wrap: wrap; ">
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
            <div id="div-info-send" class="div-info-send">
                <p>
                    <?php i::_e('Sua diligência já foi enviada') ?>
                </p>
            </div>
        </div>
        <div>
            <textarea name="description" id="descriptionDiligence" cols="30" rows="10" 
            placeholder="<?= $placeHolder; ?>" class="diligence-context-open"></textarea>
        </div>
        <div class="div-btn-send-diligence flex-container">
            <div class="flex-items" id="btn-actions-diligence">
               <?php $this->part('diligence/btn-actions-diligence'); ?>
            </div>
             <!-- PARA O PROPONENTE -->
                <div  class="flex-items" id="btn-actions-proponent">
                    <?php $this->part('diligence/btn-actions-proponent', ['entity' => $entity]); ?>
                </div>
                <!-- FIM PROPONENTE -->
        </div>
        <script>
            function hideRegistration() {
                $("#registration-content-all").hide();
            }

            function showRegistration() {
                $("#registration-content-all").show();
            }

            function saveDiligence(status) {
                if (status == 3) {
                    Swal.fire({
                        title: "Confirmar o envio da diligência?",
                        text: "Essa ação não pode ser desfeita. Por isso, revise sua diligência com cuidado.",
                        showDenyButton: true,
                        showCancelButton: false,
                        denyButtonText: `Não, enviar depois`,
                        confirmButtonText: "Enviar agora",
                    }).then((result) => {
                        /* Read more about isConfirmed, isDenied below */
                        if (result.isConfirmed) {
                            sendAjaxDiligence(status)
                        }
                    });
                } else {
                    sendAjaxDiligence(status)
                }

            }

            function sendAjaxDiligence(status) {
                $.ajax({
                    type: "POST",
                    url: MapasCulturais.createUrl('diligence', 'save'),
                    data: {
                        registration: MapasCulturais.entity.id,
                        openAgent: MapasCulturais.userProfile.id,
                        agent: MapasCulturais.entity.ownerId,
                        createTimestamp: moment().format("YYYY-MM-DD"),
                        description: $("#descriptionDiligence").val(),
                        status: status,
                    },
                    dataType: "json",
                    success: function(res) {
                        console.log('sendAjax', res)
                        if (res.status == 200) {
                            $("#label-save-content-diligence").show()
                            setTimeout(() => {
                                $("#label-save-content-diligence").hide()
                                MapasCulturais.createUrl('inscricao', MapasCulturais.entity.id)
                            }, 2000);
                        }
                    },
                    error: function(err) {
                        console.log({
                            err
                        })
                    }
                });
            }

            function saveAnswerProponente(status)
            {
                $.ajax({
                    type: "POST",
                    url: MapasCulturais.createUrl('diligence', 'answer'),
                    data: "data",
                    dataType: "dataType",
                    success: function (response) {
                        
                    }
                });
            }

            function getContentDiligence() {
                $.ajax({
                    type: "GET",
                    url: MapasCulturais.createUrl('diligence', 'getcontent/' + MapasCulturais.entity.id),
                    dataType: "json",
                    success: function(res) {

                        if (res.data == null && MapasCulturais.userEvaluate) {
                            $("#btn-actions-proponent").hide();
                        }

                        if ( (res.data.status == 0 || res.data == null)
                             && MapasCulturais.userEvaluate) {
                            $("#btn-actions-proponent").hide();
                            $("#descriptionDiligence").val(res.data.description);
                            $("#btn-save-diligence").show();
                            $("#paragraph_info_status_diligence").html('A sua Diligência ainda não foi enviada');
                        }
                        if(res.data.status == 0 && res.data.sendDiligence == null){
                            if(MapasCulturais.userEvaluate == false) {
                                $("#li-tab-diligence-diligence > a").remove();
                                $("#li-tab-diligence-diligence").append('<label>Diligência</label>');
                                $("#li-tab-diligence-diligence > label").addClass('cursor-disabled');
                            }
                        }

                        if (res.data !== null && res.data.status == 3 && 
                                (res.data.sendDiligence == !null || res.data.sendDiligence.date !== "")
                            ){
                            console.log({res})
                            $("#paragraph_info_status_diligence").html('Sua diligência já foi enviada');
                            $("#paragraph_info_status_diligence").hide();
                            $("#paragraph_content_send_diligence").html(res.data.description);
                            $("#paragraph_createTimestamp").html(moment(res.data.createTimestamp.date).format("LLL"));
                            $("#div-diligence").hide();
                            $("#descriptionDiligence").hide();
                            $("#div-info-send").show();
                            if (res.data.agent.id == MapasCulturais.userProfile.id) {
                                $("#descriptionDiligence").show();
                                $("#div-info-send").hide();
                                $("#descriptionDiligence").val('')
                            }
                            if(!MapasCulturais.isProponent) {                                
                                $("#btn-actions-proponent").hide();
                            }
                            $("#btn-save-diligence").hide();
                            $("#btn-send-diligence").hide();
                            $("#div-content-all-diligence-send").show();
                        }
                    }
                });
            }

            function sendFileDiligence() {
                console.log('sendFileDiligence')
            }
        </script>
    </div>
</div>
<?php $this->applyTemplateHook('tabs', 'after'); ?>