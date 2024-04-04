<?php

use MapasCulturais\i;
use Diligence\Entities\Diligence as EntityDiligence;

$this->jsObject['userEvaluate'] = EntityDiligence::isEvaluate($entity, $app->user);

$this->jsObject['isProponent'] = EntityDiligence::isProponent($diligenceRepository);
?>
<script>
    $(document).ready(function() {
        var urlAtual = window.location.href;
        // Declaração da variável fora de qualquer função para torná-la global
        var idDiligence;
        // Sentença de string que você deseja verificar na URL
        var sentencaDesejada = "#/tab=diligence-diligence";

        if (urlAtual.includes(sentencaDesejada)) {
            console.log("A URL contém a sentença desejada.");
            $("#registration-content-all").hide();
        } else {
            console.log("A URL não contém a sentença desejada.");
            $("#diligence-diligence").hide();

        }

        $("#btn-save-diligence").hide();
        $("#btn-save-diligence-proponente").hide()
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

                if (MapasCulturais.isProponent) {
                    $("#btn-save-diligence-proponente").hide()
                } else {
                    $("#btn-save-diligence").hide()
                }
            } else {
                if (MapasCulturais.isProponent) {
                    $("#btn-save-diligence-proponente").show()
                } else {
                    $("#btn-save-diligence").show()
                }
            }
        });
    });
</script>
<?php $this->applyTemplateHook('tabs', 'before'); ?>
<ul class="abas clearfix">
    <li class="active">
        <a href="#diligence-principal" rel="noopener noreferrer" onclick="showRegistration()" id="tab-main-content-diligence-principal">Ficha</a>
    </li>
    <li class="" id="li-tab-diligence-diligence">
        <a href="#diligence-diligence" rel="noopener noreferrer" onclick="hideRegistration()" id="tab-main-content-diligence-diligence">Diligência</a>
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
            <div style="padding: 20px;justify-content: space-between;flex-wrap: wrap; margin-top: 10px; background:#F3F3F3">
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
            function hideRegistration() {
                $("#registration-content-all").hide();
            }

            function showRegistration() {
                $("#registration-content-all").show();
            }
            function showSaveContent(status)
            {
                $("#label-save-content-diligence").show()
                setTimeout(() => {
                    $("#label-save-content-diligence").hide()
                    if(status == 3){
                        MapasCulturais.createUrl('inscricao', MapasCulturais.entity.id);
                    }
                }, 2000);
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
                            showSaveContent(status)
                        }
                    },
                    error: function(err) {
                        console.log({
                            err
                        })
                    }
                });
            }

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

            function getContentDiligence() {
                $.ajax({
                    type: "GET",
                    url: MapasCulturais.createUrl('diligence', 'getcontent/' + MapasCulturais.entity.id),
                    dataType: "json",
                    success: function(res) {
                        console.log({
                            res
                        })
                        switch (res.message) {
                            case 'resposta_rascunho':
                                idDiligence = res.data[0].diligence.id;
                                break;
                            case 'sem_diligencia':
                                idDiligence = 0;
                                break;
                            case 'diligencia_aberta':
                                idDiligence = res.data[0].id;
                                break;
                                    
                            default:
                                break;
                        }
                        
                        //Sem diligência ou diligencia aberta - Ação para o proponente
                        // if (
                        //     (res.message == 'sem_diligencia' || res.message == 'diligencia_aberta') &&
                        //     MapasCulturais.userEvaluate == false) {
                        //     $("#li-tab-diligence-diligence > a").remove();
                        //     $("#li-tab-diligence-diligence").append('<label>Diligência</label>');
                        //     $("#li-tab-diligence-diligence > label").addClass('cursor-disabled');
                        // }
                        //Sem diligência ou diligencia aberta - Ação para o avaliador
                        if (
                            (res.message == 'sem_diligencia' || res.message == 'diligencia_aberta') &&
                            MapasCulturais.userEvaluate == true) {
                            $("#btn-actions-proponent").hide();
                            $("#descriptionDiligence").val(res.data[0].description);
                            $("#btn-save-diligence").show();
                            $("#paragraph_info_status_diligence").html('A sua Diligência ainda não foi enviada');
                        }
                        
                        //Formatando página para o parecerista
                        if (
                            res.data[0].status == 3
                        ) {
                            $("#descriptionDiligence").hide();
                            $("#paragraph_info_status_diligence").hide();
                            $("#paragraph_content_send_diligence").html(res.data[0].description);
                            $("#div-content-all-diligence-send").show();
                            $("#paragraph_createTimestamp").html(moment(res.data[0].sendDiligence.date).format("LLL"));
                            $("#div-info-send").show();
                            $("#btn-save-diligence").hide();
                            $("#btn-send-diligence").hide();
                        }
                        //Para proponente
                        if (
                            res.message == 'resposta_enviada' &&
                            res.data[1].status == 3 && res.data[0].status == 3
                        ) {
                            $("#li-tab-diligence-diligence").append('');
                            console.log(res.data[1].answer);
                            $("#descriptionDiligence").hide();
                            $("#btn-send-diligence-proponente").hide();
                            $("#paragraph_content_send_answer").html(res.data[1].answer);
                            $("#paragraph_createTimestamp").html(moment(res.data[0].sendDiligence.date).format("LLL"));
                        }
                        console.log((res.message == 'diligencia_aberta' || res.message == 'resposta_rascunho'))
                        console.log(MapasCulturais.userEvaluate == false)
                        console.log(res.data[0].status == 0);
                        if (
                            (res.message == 'diligencia_aberta' || res.message == 'resposta_rascunho') &&
                            MapasCulturais.userEvaluate == false &&
                            res.data[0].status == 0
                        ) {
                            let description = '';
                            if(res.message == 'diligencia_aberta'){
                                description = res.data[0].description;
                            }else{
                                description = res.data[0].diligence.description;
                            }
                            const anchor = '<a href="#diligence-diligence" rel="noopener noreferrer" onclick="hideRegistration()"' +
                                'id="tab-main-content-diligence-diligence">Diligência</a>';
                            $("#li-tab-diligence-diligence > label").removeClass('cursor-disabled');
                            $("#li-tab-diligence-diligence > label").remove();
                            $("#li-tab-diligence-diligence").append(anchor);
                            $("#div-info-send").hide();
                            $("#btn-save-diligence").hide();
                            $("#btn-send-diligence").hide();
                            $("#paragraph_content_send_diligence").html(description);
                            $("#descriptionDiligence").show();
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