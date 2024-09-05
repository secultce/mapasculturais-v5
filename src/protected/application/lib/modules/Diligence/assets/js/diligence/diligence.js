//URL global para salvar a diligencia
var urlSaveDiligence = MapasCulturais.createUrl('diligence', 'save');
//Objecto com itens primário para salavar a diligência
var objSendDiligence = {
    registration: MapasCulturais.entity.id,
    openAgent: MapasCulturais.userProfile.id,
    agent: MapasCulturais.entity.ownerId,
    createTimestamp: moment().format("YYYY-MM-DD")
}

$(document).ready(function () {
    $("#paragraph_value_project").hide();

    hideBtnActionsDiligence();
    //Inciando o acoordioin Jquery
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

    }).catch((err) => {
        MapasCulturais.Messages.error('Ocorreu um erro na autorização do projeto');
    })
    //Ocutando itens em comum do parecerista e do proponente
    EntityDiligence.hideCommon();

    //Formatando o layout
    let entityDiligence = EntityDiligence.showContentDiligence();
    entityDiligence
        .then((res) => {
            let actions = true;
            if (res.message == 'sem_diligencia') {
                $("#paragraph_loading_content").hide();
                $("#paragraph_info_status_diligence").html('A sua diligência ainda não foi enviada');
                $("#subject_info_status_diligence").hide();//Oculta o assunto da diligencia
                if (res.data && res.data[0]?.status == 0) EntityDiligence.hideBtnOpenDiligence();

            }
            if (res.message == 'diligencia_aberta') {
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
                if (actions && MapasCulturais.entity.object.opportunity.use_multiple_diligence == 'Sim') {
                    showBtnActionsDiligence();
                }
            }
            $("#paragraph_loading_content").hide();
        })
        .catch((error) => {
            MapasCulturais.Messages.error('Ocorreu um erro ao carregar um conteúdo');
        });

    $("#select-value-project-diligence").on("change", function (e) {
        e.preventDefault();
        saveAuthorizedProject('option_authorized', e.target.value)
        if (e.target.value == 'Sim') {
            $("#paragraph_value_project").show();
        } else {
            $("#paragraph_value_project").hide();
        }
    });

    $("#input-value-project-diligence").on("blur", function (e) {
        saveAuthorizedProject('value_project_diligence', e.target.value)
    });
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
    $('#btn-actions-diligence').removeClass('d-none');
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

//Sempre implementar esses metodos
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
            confirmButtonText: `
              <i class="fa fa-thumbs-up"></i> OK!
            `,
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
     if ($("#descriptionDiligence").val() == '') {
         diligenceMessage.messageSimple("Ops!", "A descrição precisa ser preenchida", 2000);
         return false;
    }
    if (status == 3) {
        //Mensagem de confirmação
        Swal.fire({
            title: "Confirmar o envio da diligência?",
            text: "Essa ação não pode ser desfeita. Por isso, revise sua diligência com cuidado.",
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
        }).then((result) => {
            if (result.isConfirmed) {
                sendAjaxDiligence(status, idDiligence);
            }
        });
    } else {
        sendAjaxDiligence(status, idDiligence);
    }

}

function sendAjaxDiligence(status, idDiligence) {
    //Enviando o assunto com formato de array
    objSendDiligence['subject'] = verifySubject("subject_exec_physical", "subject_report_finance");
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
        },
        error: function (err) {
            MapasCulturais.Messages.error('Ocorreu um erro ao enviar a diligência');
        }
    });
}

/**
 * Metodo que verifica se o assunto está confirmado
 * @param subject_exec_physical string
 * @param subject_report_finance string
 * @returns {array}
 */
function verifySubject(subject_exec_physical, subject_report_finance)
{
    objSendDiligence['subject'] = [];
    //se a escolha for execusão do objeto
    if($("#"+subject_exec_physical+":checked").val() == 'on')
    {
        objSendDiligence['subject'].push('subject_exec_physical');
    }
    //se a escolha for relatorio financeiro
    if($("#"+subject_report_finance+":checked").val() == 'on')
    {
        objSendDiligence['subject'].push('subject_report_finance');
    }
    //Na situação de diligência única
    if( 
        MapasCulturais.entity.object.opportunity.use_multiple_diligence !== 'Sim'
        && MapasCulturais.entity.object.opportunity.use_diligence == "Sim"
        && $("#"+subject_exec_physical+":checked").val() == undefined 
        && $("#"+subject_report_finance+":checked").val() == undefined 
    )
    {
        diligenceMessage.messageSimple("Ops!", "Você precisa escolher um assunto ou mais assuntos", 2000);
        return false;
    }else{
        objSendDiligence['subject'].push('single_diligence');
    }
    
    return objSendDiligence['subject'];
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


