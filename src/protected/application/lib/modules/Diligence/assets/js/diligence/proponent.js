$(document).ready(function () {
    EntityDiligence.showAccordion('#accordion');
    //Remove o botão de abrir diligencias
    EntityDiligence.removeBtnOpenDiligence();

    //id da diligencia se houver
    let idDiligence = 0;
    $("#id-input-diligence").val(idDiligence);

    //Ocuta os arquivo comuns
    EntityDiligence.hideCommon();

    //Bloqueando aba para proponente
    $("#li-tab-diligence-diligence > a").remove();
    $("#tab-diligence-diligence").remove();

    $("#li-tab-diligence-diligence").append('<label>Diligência</label>');
    $("#li-tab-diligence-diligence > label").addClass('cursor-disabled');

    let entityDiligence = EntityDiligence.showContentDiligence();
    entityDiligence
    .then((res) => {
        const draftStatus = 0;
        const diligences = res.data;
        const diligenceSent = diligences?.filter(diligence => {
            return diligence?.status > draftStatus;
        });

        if (res.message === 'sem_diligencia' && MapasCulturais.isEvaluator === false) {
            //Se tiver diligencia
            if (res.data?.length && diligenceSent.length) {
                const ahref ='<a href="#diligence-diligence" rel="noopener noreferrer" onclick="hideRegistration()" id="tab-main-content-diligence-diligence">Diligência</a>';

                $("#li-tab-diligence-diligence > label").removeClass('cursor-disabled');
                $("#li-tab-diligence-diligence > label").remove();
                $("#li-tab-diligence-diligence").append(ahref);

                res.data.forEach((element, index) => {
                    $("#descriptionDiligence").hide();
                    $("#div-btn-actions-proponent").hide();
                    $("#attachment-info").hide();
                    //Id da diligencia
                    MapasCulturais.idDiligence = element?.id;
                    $("#paragraph_loading_content").hide();
                });
            }
        }

        if(res.message !== 'sem_diligencia' &&  MapasCulturais.isEvaluator === false) {
            hideAnswerDraft();
            let idsDiligences = [];
            res.data.forEach((answer, index) => {
                if (answer?.id === undefined) {
                    EntityDiligence.showAnswerDraft(null);
                    $("#descriptionDiligence").show();
                } else {
                    idsDiligences.push(answer?.id);
                }
                if (answer?.status === 0) {
                    $("#descriptionDiligence").hide();
                }
            })

            MapasCulturais.idDiligence = Math.max.apply(null, idsDiligences);
            const ahref ='<a href="#diligence-diligence" rel="noopener noreferrer" onclick="hideRegistration()" id="tab-diligence-diligence">Diligência</a>';
                $("#li-tab-diligence-diligence > label").removeClass('cursor-disabled');
                $("#li-tab-diligence-diligence > label").remove();
                $("#li-tab-diligence-diligence").append(ahref);
            res.data.forEach((answer, index) => {
                const limitDate = EntityDiligence.validateLimiteDate(MapasCulturais.diligence_days);

                if (limitDate) {
                    EntityDiligence.showAnswerDraft(answer);
                    $("#descriptionDiligence").hide();
                    $("#div-btn-actions-proponent").hide();
                } else {
                    MapasCulturais.idDiligence = answer?.diligence?.id;
                    EntityDiligence.showAnswerDraft(answer);
                    $("#descriptionDiligence").show();
                    $("#div-btn-actions-proponent").show();
                }
            });
            if (res.data[0].answer != null) {
                $("#descriptionDiligence").hide();
                $("#div-btn-actions-proponent").hide();
                $("#attachment-info").hide();
                if (res.data[0].answer.status == 3) $("#div-content-all-diligence-send").hide();
            } else {
                $("#div-content-all-diligence-send").show();
                EntityDiligence.showAnswerDraft(null);
                $("#descriptionDiligence").show();
            }
        }

        $("#upload-file-diligence").submit(() => {
            const numberSavedFiles = MapasCulturais.countFileUpload + 1;
            const useMultiDiligence = MapasCulturais.entity.object.opportunity.use_multiple_diligence;

            if (useMultiDiligence == 'Sim' || numberSavedFiles <= 2) {
                MapasCulturais.countFileUpload++;

                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                setTimeout(() => {
                    $('#info-title-limit-file-diligence').html(`
                        Limite de arquivo excedido
                        <button class="btn-reload-diligence" onClick="window.location.reload();" title="Recarregar página">
                            <i class="fa fa-redo-alt"></i>
                        </button>
                    `);

                    $('#div-upload-file-count .mc-cancel').click();
                }, 1000);
            }
        });

        $('body').on('click', '.js-remove-item-diligence', function (e) {
            e.stopPropagation();
            var $this = $(this);
            MapasCulturais.confirm('Deseja remover este item?', function () {
                var $target = $($this.data('target'));
                var href = $this.data('href');

                $.getJSON(href, function (r) {
                    if (r.error) {
                        MapasCulturais.Messages.error(r.data);
                    } else {
                        var cb = function () { };
                        if ($this.data('remove-callback'))
                            cb = $this.data('remove-callback');
                        $target.remove();
                        MapasCulturais.countFileUpload--
                        if (typeof cb === 'string')
                            eval(cb);
                        else
                            cb();
                    }
                });
            });

            return false;
        });
    })
    .catch((error) => {
        MapasCulturais.Messages.error('Erro ao carregar diligência');
    })

    $('#attach-dili-res-file').on('click', () => {
        const text = $('#descriptionDiligence').val();

        if (text.length) saveAnswerProponente(0);
    });
});

function hideAnswerDraft()
{
    $("#div-btn-actions-proponent").hide();
    $("#btn-save-diligence-proponent").hide();
    $("#btn-send-diligence-proponente").hide();
    $("#paragraph_loading_content").hide();
}

//Joga o conteudo do rascunho para text area
function editDescription(description, id, type){
    $("#descriptionDiligence").show();
    $("#attachment-info").show();
    EntityDiligence.showAnswerDraft(null);
    EntityDiligence.editDescription(description, id, type);
}

//Sempre implementar esses metodos
function hideRegistration()
{
    EntityDiligence.hideRegistration();
}
function showRegistration()
{
    EntityDiligence.showRegistration();
}
//Enviar resposta do proponente
function saveAnswerProponente(status) {
    if($("#descriptionDiligence").val() == '') {
        Swal.fire({
            title: "Ops!",
            html: `<i class="fa fa-times-circle"></i> Para enviar a resposta, você deve preencher o formulário, tente novamente.`,
            color: "#dc3545",
        })
        return false;
    }
    if (status == 3) {
        Swal.fire({
            title: "Confirmar o envio da sua resposta?",
            text: "Essa ação não pode ser desfeita. Por isso, revise sua resposta com cuidado.",
            showDenyButton: true,
            showCancelButton: false,
            denyButtonText: `Não, enviar depois`,
            confirmButtonText: "Enviar agora",
            reverseButtons: true
        }).then((result) => {
            //Formatando a view
            hideViewActions();
            if (result.isConfirmed) {
                saveRequestAnswer(status);
                Swal.fire({
                    title: "<strong>Sucesso!</strong>",
                    html: `
                      A sua resposta foi enviada!
                    `,
                    focusConfirm: false,
                    confirmButtonText: `
                      <i class="fa fa-thumbs-up"></i> OK!
                    `,
                    timer: 10000,
                    timerProgressBar: true,
                    allowOutsideClick: false,
                    showCancelButton: true,
                    reverseButtons: true,
                    confirmButtonText: "OK",
                    cancelButtonText: 'Desfazer envio',
                }).then((successResult) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (successResult.isConfirmed) {
                        sendNofificationAnswer();
                        location.reload();
                    }

                    if (successResult.isDismissed && successResult.dismiss === 'cancel') {
                        showViewActions();
                        cancelAnswer();
                    }

                    if (successResult.dismiss === Swal.DismissReason.timer) {
                        sendNofificationAnswer();
                        hideViewActions();
                        location.reload();
                    }
                }).catch((err) => {
                    Swal.close();
                    MapasCulturais.Messages.error('Ocorreu um erro ao confirmar.');
                });
            }

            if (result.isDenied) {
                $("#div-btn-actions-proponent").show();
                $("#descriptionDiligence").show();
                $("#div-content-all-diligence-send").show();
                $("#answer_diligence").hide();
            }

        }).catch((err) => {
            Swal.close();
            MapasCulturais.Messages.error('Ocorreu um erro ao confirmar.');
        });
    } else {
        saveRequestAnswer(status);
    }
}

function cancelAnswer()
{
    $.ajax({
        type: "PUT",
        url: MapasCulturais.createUrl('diligence', 'cancelsendAnswer'),
        data: `idAnswer=${MapasCulturais.idAnswer}`,
        dataType: "json",
        success: function(response) {
           if (response.status == 200) {
               MapasCulturais.Messages.help('Salvo como rascunho!');
               EntityDiligence.hideShowSuccessAction();
           }
        }
    });
}

function saveRequestAnswer(status)
{
    idAnswer = $('#id-input-diligence').val();
    $.ajax({
        type: "POST",
        url: MapasCulturais.createUrl('diligence', 'answer'),
        data: {
            diligence: MapasCulturais.idDiligence,
            answer: $("#descriptionDiligence").val(),
            status: status,
            registration: MapasCulturais.entity.id,
            idAnswer : idAnswer
        },
        dataType: "json",
        success: function(response) {
            if(response.status == 200){
                EntityDiligence.hideShowSuccessAction();
                $("#id-input-diligence").val(response.entityId);
                MapasCulturais.idAnswer = response.entityId;
            }
        },
        error: function(err) {
            Swal.close();
            showViewActions();
            cancelAnswer();
            Swal.fire({
                title: err.responseJSON.data.message,
                reverseButtons: true,
                timer: 2500
            });
            return false;
        }
    });
}

function sendNofificationAnswer()
{
    $.ajax({
        type: "POST",
        url: MapasCulturais.createUrl('diligence', 'notifiAnswer'),
        data: {
            registration: MapasCulturais.entity.id
        },
        dataType: "json",
        success: function(res) {
           if(res.status == 200){
                window.location.href=MapasCulturais.createUrl('inscricao', MapasCulturais.entity.id);
            }
        }
    });
}

function hideViewActions()
{
    $("#paragraph_content_send_answer").html($("#descriptionDiligence").val());
    $("#div-btn-actions-proponent").hide();
    $("#descriptionDiligence").hide();
    $("#div-content-all-diligence-send").show();
    $("#answer_diligence").show();
    $(".footer-btn-delete-file-diligence").hide();
}

function showViewActions()
{
    $("#paragraph_content_send_answer").html($("#descriptionDiligence").val());
    $("#div-btn-actions-proponent").show();
    $("#descriptionDiligence").show();
    $("#div-content-all-diligence-send").hide();
    $("#answer_diligence").hide();
    $("#div-content-all-diligence-send").show();
    $(".footer-btn-delete-file-diligence").show();
}

/**
 * Envia requisição com id do arquivo para exclusão
 * @param {string} id
 */
function deleteFileDiligence(id)
{
    $.ajax({
        type: "GET",
        url: MapasCulturais.createUrl('diligence','deleteFile'),
        data: {
            file: id,
            registration:  MapasCulturais.entity.id
        },
        dataType: "json"
    });
}
