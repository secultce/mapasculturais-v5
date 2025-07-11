//URL global para salvar a diligencia
const urlSaveDiligence = MapasCulturais.createUrl('diligence', 'save');
//Objeto com item primário para salvar a diligência
let objSendDiligence = {
    registration: MapasCulturais.entity.id,
    openAgent: MapasCulturais.userProfile.id,
    agent: MapasCulturais.entity.ownerId,
    createTimestamp: moment().format("YYYY-MM-DD")
}

$(document).ready(function () {
    $("#paragraph_value_project").hide();

    hideBtnActionsDiligence();
    //Iniciando o accordion Jquery
    EntityDiligence.showAccordion('#accordion');

    //id da diligencia
    let idDiligence = 0;
    $("#id-input-diligence").val(idDiligence);
    //Buscado diligencia se houver

    //Retornar valor se foi autorizado
    let authorized = EntityDiligence.returnGetAuthorized();
    authorized.then((res) => {
        //Alterando a opção do select
        $("#select-value-project-diligence").val(res.optionAuthorized).change();
        if (res.valueAuthorized !== null) {
            //Alterando o valor do projeto
            $("#input-value-project-diligence").val(res.valueAuthorized);
        }

    }).catch(() => {
        MapasCulturais.Messages.error('Ocorreu um erro na autorização do projeto');
    })
    // Ocultando itens em comum do parecerista e do proponente
    EntityDiligence.hideCommon();

    //Formatando o layout
    EntityDiligence.showContentDiligence()
        .then((res) => {
            let actions = true;
            if (res.message === 'sem_diligencia') {
                $("#paragraph_loading_content").hide();
                $("#paragraph_info_status_diligence").html('A sua diligência ainda não foi enviada');
                $("#subject_info_status_diligence").hide();//Oculta o assunto da diligencia
                if (res.data && res.data[0]?.status == 0) EntityDiligence.hideBtnOpenDiligence();

            }
            if (res.message === 'diligencia_aberta') {
                EntityDiligence.hideBtnOpenDiligence();
                res.data.forEach((element, index) => {
                    if (element?.status == 0) {
                        $("#descriptionDiligence").hide();
                        $("#paragraph_loading_content").hide();
                    }
                });
                $("#subject_info_status_diligence").hide();
            }
            if (res.message !== 'sem_diligencia') {
                EntityDiligence.hideBtnOpenDiligence();
                res.data.forEach((element, index) => {
                    if (element == null) {
                        actions = false;
                    }
                });
                if (actions && MapasCulturais.entity.object.opportunity.use_multiple_diligence === 'Sim') {
                    showBtnActionsDiligence();
                }
            }
            $("#paragraph_loading_content").hide();
        })
        .catch(() => {
            MapasCulturais.Messages.error('Ocorreu um erro ao carregar um conteúdo');
        });

    $("#select-value-project-diligence").on("change", function (e) {
        e.preventDefault();
        saveAuthorizedProject('option_authorized', e.target.value)
        if (e.target.value === 'Sim') {
            $("#paragraph_value_project").show();
        } else {
            $("#paragraph_value_project").hide();
        }
    });

    $("#input-value-project-diligence").on("blur", function (e) {
        saveAuthorizedProject('value_project_diligence', e.target.value)
    });

    $('.save-opinion-accountability-btn').on('click', function () {
        saveOrPublishOpinion('save')
    })

    $('.publish-opinion-accountability-btn').on('click', function () {
        Swal.fire({
            title: "Deseja publicar o parecer?",
            text: "Essa ação não poderá ser desfeita.",
            showCancelButton: true,
            cancelButtonText: "Cancelar",
            confirmButtonText: "Confirmar",
            reverseButtons: true
        }).then(res => {
            if (res.isConfirmed) {
                saveOrPublishOpinion('publish')
            }
        })
    })
});

//Clico do botão de abrir a diligência
function openDiligence(status) {
    objSendDiligence['description'] = '';
    objSendDiligence['status'] = status;
    //Load
    diligenceMessage.loadSimple();
    //Mostra opção do assunto
    $("#subject_info_status_diligence").show();

    setTimeout(() => {
        Swal.close();
    }, 1000);
    $("#descriptionDiligence").show();
    showBtnActionsDiligence();
    EntityDiligence.hideBtnOpenDiligence();
    EntityDiligence.hideRegistration();
}

//Editando a descrição do rascunho.
function editDescription(description, id) {
    EntityDiligence.editDescription(description, id);
    showBtnActionsDiligence();
    //Mostrando itens de assunto
    $("#subject_info_status_diligence").show();
}

//Mostrar os botões de ação da diligência
function showBtnActionsDiligence() {
    $('#btn-actions-diligence').removeClass('d-none');
    $("#btn-save-diligence").show();
    $("#btn-send-diligence").show();
}

//Salvando a autorização e o valor do projeto
function saveAuthorizedProject(keyAuth, valueAuth) {
    const dataAuthorized = {
        entity: MapasCulturais.entity.id
    };
    dataAuthorized[keyAuth] = valueAuth;
    $.ajax({
        type: "POST",
        url: MapasCulturais.createUrl('diligence', 'valueProject'),
        data: dataAuthorized,
        dataType: "json",
        success: function (res) {
            if (res.status == 200) {
                MapasCulturais.Messages.success('Valor destinado registrado');
            }
        },
        error: function (err) {
            MapasCulturais.Messages.error(err.responseJSON.data);
        }
    });
}

//Sempre implementar esses métodos
function hideRegistration() {
    EntityDiligence.hideRegistration();
}
function showRegistration() {
    EntityDiligence.showRegistration();
}

//Enviando diligência
function showSaveContent(status) {
    $("#label-save-content-diligence").show();

    setTimeout(() => {
        $("#label-save-content-diligence").hide()
    }, 2000);

    if (status == 3) {
        Swal.fire({
            title: "<strong>Sucesso!</strong>",
            html: `
              A sua diligência foi enviada!
            `,
            focusConfirm: false,
            timer: 10000,
            timerProgressBar: true,
            didOpen: () => {
                const timer = Swal.getPopup().querySelector("b");
                timerInterval = setInterval(() => {
                }, 100);
            },
            willClose: () => {
                clearInterval(timerInterval);
            },
            confirmButtonAriaLabel: "Thumbs up, great!",
            allowOutsideClick: false,
            showCancelButton: true,
            reverseButtons: true,
            cancelButtonText: 'Desfazer envio',
            confirmButtonText: "OK",
        }).then((result) => {
            if (result.isConfirmed) {
                sendNotification();
                hideAfterSend();
                showBtnSubmitEvaluation();
                EntityDiligence.hideBtnOpenDiligence();
                location.reload();
            }

            if (result.isDismissed && result.dismiss === 'cancel') {
                cancelSend();
            }

            if (
                result.dismiss === Swal.DismissReason.timer
            ) {
                sendNotification();
                hideAfterSend();
                showBtnSubmitEvaluation();
                EntityDiligence.hideBtnOpenDiligence();
                location.reload();
            }
        });
    }

}
//Enviando notificação
function sendNotification() {
    $.ajax({
        type: "POST",
        url: MapasCulturais.createUrl('diligence', 'sendNotification'),
        data: {
            registration: MapasCulturais.entity.id,
            openAgent: MapasCulturais.userProfile.id,
            agent: MapasCulturais.entity.ownerId,
        },
        dataType: "json",
        success: function (res) {
            MapasCulturais.Messages.success('Notificação enviada');
            if (res.status == 200) {
                window.location.href = MapasCulturais.createUrl('inscricao', MapasCulturais.entity.id)
            }
        }
    });
}

function cancelSend() {

    const urlSend = MapasCulturais.createUrl('diligence', 'cancelsend');
    $.ajax({
        type: "PUT",
        url: urlSend,
        data: { registration: MapasCulturais.entity.id },
        dataType: "json",
        success: function (res) {
            if (res.status == 400) {
                Swal.fire("Ops! Ocorreu um erro.");
            }
        }
    });
}

function saveDiligence(status, st, idDiligence) {
    if ($("#descriptionDiligence").val() === '') {
        diligenceMessage.messageSimple("Ops!", "A descrição precisa ser preenchida", 2000);
        return false;
    }
    if (status == 3) {
        //Mensagem de confirmação
        Swal.fire({
            title: "Confirmar o envio da diligência?",
            text: "Você pode desfazer o envio em até 10 segundos. Revise sua diligência com cuidado.",
            showConfirmButton: true,
            showCloseButton: false,
            showCancelButton: true,
            reverseButtons: true,
            cancelButtonText: `Não, enviar depois`,
            confirmButtonText: "Enviar agora",
            customClass: {
                confirmButton: "btn-success-rec",
                cancelButton: "btn-warning-rec"
            },
        })
            .then((result) => {
                if (result.isConfirmed) {
                    sendAjaxDiligence(status, idDiligence);
                }
            })
    } else {
        sendAjaxDiligence(status, idDiligence);
    }
}

function sendAjaxDiligence(status, idDiligence) {
    //Enviando o assunto com formato de array
    const subject = verifySubject();
    if (!subject) {
        return;
    }

    objSendDiligence['subject'] = subject;
    objSendDiligence['description'] = $("#descriptionDiligence").val();
    objSendDiligence['status'] = status;
    objSendDiligence['idDiligence'] = idDiligence;

    $.ajax({
        type: "POST",
        url: urlSaveDiligence,
        data: objSendDiligence,
        dataType: "json",
        success: function (res) {
            if (res.status == 200) {
                showSaveContent(status)
                $("#id-input-diligence").val(res.entityId);
            }
            if (res.status == 403) {
                diligenceMessage.messageError('Ops! Um erro.', res.message, 3500);
            }
        },
        error: function () {
            MapasCulturais.Messages.error('Ocorreu um erro ao enviar a diligência');
        }
    });
}

/**
 * Método que verifica se o assunto está confirmado
 * @returns {array|false}
 */
function verifySubject() {
    const subjects = [];

    document.querySelectorAll('input[name="assunto[]"]:checked').forEach(el => {
        subjects.push(el.value);
    });

    if (subjects.length === 0) {
        diligenceMessage.messageSimple("Ops!", "Você precisa escolher um assunto ou mais assuntos", 2000);
        return false;
    }

    if (MapasCulturais.entity.object.opportunity.use_multiple_diligence !== 'Sim') {
        subjects.push('single_diligence');
    }

    return subjects;
}

//Oculta o botão de Finalizar avaliação
function showBtnSubmitEvaluation() {
    $("#btn-submit-evaluation").removeAttr('disabled');
    $("#btn-submit-evaluation").removeClass('btn-diligence-open-desactive');
}
//Mostra o botão de avaliação
function hideBtnSubmitEvaluation() {
    $("#btn-submit-evaluation").attr('disabled', true);
    $("#btn-submit-evaluation").addClass('btn-diligence-open-desactive');
}

//Oculta os botões de ação da diligência
function hideBtnActionsDiligence() {
    $("#btn-save-diligence").hide();
    $("#btn-send-diligence").hide();
}

//Após o envio da diligência
function hideAfterSend() {
    $("#paragraph_content_send_diligence").html($("#descriptionDiligence").val());
    $("#div-content-all-diligence-send").show();
    $("#div-diligence").hide();
    $("#btn-actions-diligence").hide();
    $("#descriptionDiligence").hide();
}

function trashDraftDiligence(idDiligence, titleQuestion, textTrash, titleCancel, titleConfirm, classBtnConfirm, classBtnCancel) {
    const trashDraft = diligenceMessage.messageConfirm(
        titleQuestion, textTrash, titleCancel, titleConfirm, classBtnCancel, classBtnConfirm
    );
    trashDraft.then(() => {
        $.ajax({
            type: "POST",
            url: MapasCulturais.createUrl('diligence', 'trashDraftDiligence'),
            data: { id: idDiligence },
            dataType: 'json',
            success: function (response) {
                diligenceMessage.messageSimple('Excluído', 'Rascunho excluído com sucesso', 1500);
                window.location.reload();
            }
        });
    })
}

function saveOrPublishOpinion(action) {
    $.ajax({
        type: "POST",
        url: MapasCulturais.createUrl('diligence', `${action}Opinion`),
        data: {
            opinion: $('.opinion-form #opinion-accountability').val(),
            registrationId: MapasCulturais.entity.id,
        },
        dataType: "json",
        success(res) {
            MapasCulturais.Messages.success(res.message)

            if (action === 'publish') {
                setTimeout(() => {
                    location.reload()
                }, 1500)
            }
        },
        error(err) {
            MapasCulturais.Messages.error(err.responseJSON.message);
        }
    })
}
