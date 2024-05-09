
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

    $( "#accordion" ).accordion({
        active: false,
        collapsible: true,
        heightStyle: "content"
    });
    $("#div-accordion-diligence > span").remove();
    //id da diligencia
    let idDiligence = 0;
    $("#id-input-diligence").val(idDiligence);
    //Buscado diligencia se houver

    //Retornar valor se foi autorizado
    let authorized = EntityDiligence.returnGetAuthorized();
    authorized.then( (res) => {
        //Alterando a opção do select
        $("#select-value-project-diligence").val(res.optionAuthorized).change();
        if(res.valueAuthorized !== null) {
            //Alterando o valor do projeto
            $("#input-value-project-diligence").val(res.valueAuthorized);
        }
        
    }).catch( (err) => {
        MapasCulturais.Messages.error('Ocorreu um erro na autorização do projeto');
    })
    //Ocutando itens em comum do parecerista e do proponente
    EntityDiligence.hideCommon();
    //Formatando o layout
    let entityDiligence = EntityDiligence.showContentDiligence();
    entityDiligence
        .then((res) => {
            console.log({res})
            res.data.forEach((element, index) => {
               //Verifica a situação da diligencia
                EntityDiligence.verifySituation(element);
                $("#btn-save-diligence").show();
                $("#paragraph_loading_content").hide();
                console.log(element.status)
                if (element.status == 3) {
                    // EntityDiligence.formatDiligenceSendProponent(element);
                    showBtnSubmitEvaluation();
                    // hideBtnActionsDiligence();                    
                }
                // $("#descriptionDiligence").html(element.description)
                    $("#descriptionDiligence").show();
            });

        })
        .catch((error) => {
            console.log({error})
            MapasCulturais.Messages.error('Ocorreu um erro ao carregar um conteúdo');
        })

    $( "#select-value-project-diligence" ).on( "change", function (e) {
        e.preventDefault();
        saveAuthorizedProject('option_authorized', e.target.value)
        if(e.target.value == 'Sim') {
            $("#paragraph_value_project").show();
        }else{
            $("#paragraph_value_project").hide();
        }
    } );

    $( "#input-value-project-diligence" ).on( "blur", function (e) {
        saveAuthorizedProject('value_project_diligence',e.target.value)
    } );

});

function saveAuthorizedProject(keyAuth, valueAuth)
{
    const dataAuthorized = {
        entity : MapasCulturais.entity.id
    }
    dataAuthorized[keyAuth] = valueAuth
    $.ajax({
        type: "POST",
        url: MapasCulturais.createUrl('diligence', 'valueProject'),
        data: dataAuthorized,
        dataType: "json",
        success: function (res) {
            if(res.status == 200) {
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
                setTimeout(() => {
                    hideAfterSend();
                }, 500);
            }
        });
    }

}

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
    console.log({status})
    console.log({st})
    console.log({idDiligence})
    if($("#descriptionDiligence").val() == ''){
        Swal.fire({
            title: "Ops! A descrição precisa ser preenchida",
            timer: 2000,
            showConfirmButton: true,
            reverseButtons: false,
        });
        return false;
    }
    if (status == 3) {
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
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                sendAjaxDiligence(status, idDiligence)
            }
        });
    } else {
        sendAjaxDiligence(status, idDiligence)
    }

}
function sendAjaxDiligence(status, idDiligence) {
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
                $("#id-input-diligence").val(222);
            }

        },
        error: function (err) {
            MapasCulturais.Messages.error('Ocorreu um erro ao enviar a diligência');
        }
    });
}

function openDiligence(status) {
    objSendDiligence['description'] = '';
    objSendDiligence['status'] = status
    const imgLoad = MapasCulturais.spinnerUrl;
    Swal.fire({
        title: "Abrindo a sua diligência",
        html: '<img src="'+imgLoad+'" style="height: 24px" />',
        showConfirmButton: false,
    });

    $.ajax({
        type: "POST",
        url: urlSaveDiligence,
        data: objSendDiligence,
        dataType: "json",
        success: function (res) {
            if(res.status == 200)    {
               setTimeout(() => {
                Swal.close();
               }, 1000);
            }
        }
    });
    $("#descriptionDiligence").show();
    $("tab-diligence-principal").removeClass('active');
    $("li-tab-diligence-diligence").addClass('active');
    showBtnActionsDiligence();
    EntityDiligence.hideBtnOpenDiligence();
}


//Oculta o botão de Finalizar avaliação
function showBtnSubmitEvaluation()
{
    $("#btn-submit-evaluation").removeAttr('disabled');
    $("#btn-submit-evaluation").removeClass('btn-diligence-open-desactive');
}
//Mostra o botão de avaliação
function hideBtnSubmitEvaluation()
{
    $("#btn-submit-evaluation").attr('disabled', true);
    $("#btn-submit-evaluation").addClass('btn-diligence-open-desactive');
}

//Oculta os botões de ação da diligência
function hideBtnActionsDiligence()
{
    $("#btn-save-diligence").hide();
    $("#btn-send-diligence").hide();
}
//Mostrar os botões de ação da diligência
function showBtnActionsDiligence()
{
    $("#btn-save-diligence").show();
    $("#btn-send-diligence").show();
}

function hideAfterSend()
{
    $("#paragraph_content_send_diligence").html($("#descriptionDiligence").val());
    $("#div-content-all-diligence-send").show();
    $("#div-diligence").hide();
    $("#btn-actions-diligence").hide();
    $("#descriptionDiligence").hide();
}


