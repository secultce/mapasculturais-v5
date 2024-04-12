
$(document).ready(function () {
    getContentDiligence()
    EntityDiligence.hideCommon();
    let entityDiligence = EntityDiligence.showContentDiligence();
    entityDiligence
        .then((res) => {
            console.log({ res })
            if (
                (res.message == 'sem_diligencia' || res.message == 'diligencia_aberta') &&
                MapasCulturais.userEvaluate == true) {
                    console.log(res.data.length)
                if (res.data.length > 0 && res.data[0].status == 0) {
                    $("#descriptionDiligence").val(res.data[0].description);
                    $("#btn-save-diligence").show();
                }
                //Se não tem diligencia
                if(res.data.length === 0){
                    $("#descriptionDiligence").show();
                    $("#paragraph_loading_content").hide();
                    $("#btn-save-diligence").hide();
                    $("#btn-send-diligence").hide();
                }
                res.data.forEach((element, index) => {
                    console.log({ element })
                    console.log({ index })
                    if (element.status == 3) {
                        EntityDiligence.formatDiligenceSendProponent(element);
                        $("#paragraph_loading_content").hide();
                    }else{
                        $("#paragraph_loading_content").hide();
                    }
                });

                $("#answer_diligence").hide();
                $("#paragraph_info_status_diligence").html('A sua Diligência ainda não foi enviada');
            }

            if (res.message == "resposta_rascunho" && MapasCulturais.userEvaluate == true) {
                res.data.forEach((answer, index) => {
                    EntityDiligence.showAnswerDraft(answer);
                    $("#descriptionDiligence").hide();
                    $("#btn-save-diligence").hide();
                    $("#btn-send-diligence").hide();
                    $("#paragraph_loading_content").hide();
                });   
            }

            if(res.message == "resposta_enviada" &&  MapasCulturais.userEvaluate == true)
            {
                res.data.forEach((answer, index) => {
                    EntityDiligence.showAnswerDraft(answer);
                    $("#paragraph_content_send_answer").html(answer.answer);
                    $("#paragraph_createTimestamp").html(moment(answer.diligence.sendDiligence.date).format('lll'));
                    $("#answer_diligence").show();
                    $("#descriptionDiligence").hide();
                    $("#btn-actions-diligence").hide();
                    $("#paragraph_createTimestamp_answer").html(moment(answer.createTimestamp.date).format('lll'))
                });   
            }
           
        })
        .catch((error) => {
            console.log(error)
        })

        $("#paragraph_createTimestamp").html(moment(answer.diligence.sendDiligence.date).format('lll'));
        $("#paragraph_createTimestamp_answer").html(moment(answer.createTimestamp.date).format('lll'))

});

function getContentDiligence() {
    console.log('getContentDiligence')
}

//Sempre implementar esses metodos
function hideRegistration() {
    EntityDiligence.hideRegistration();
}
function showRegistration() {
    EntityDiligence.showRegistration();
}

function showSaveContent(status) {
    console.log({ status })

    $("#label-save-content-diligence").show();

    setTimeout(() => {
        $("#label-save-content-diligence").hide()
    }, 2000);
    // $("#paragraph_content_send_diligence").html($("#descriptionDiligence").val());
    // $("#div-content-all-diligence-send").show();
    // $("#div-diligence").hide();
    // $("#btn-actions-diligence").hide();
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
            confirmButtonText: "OK",
            cancelButtonText: 'Desfazer envio',
        }).then((result) => {
            console.log({ result })
            if (result.isConfirmed) {
                sendNotification();
                $("#paragraph_content_send_diligence").html($("#descriptionDiligence").val());
                $("#div-content-all-diligence-send").show();
                $("#div-diligence").hide();
                $("#btn-actions-diligence").hide();
                $("#descriptionDiligence").hide();
            }
            
            if (result.isDismissed && result.dismiss === 'cancel') {
                cancelSend();              
            }

            if (
                result.dismiss === Swal.DismissReason.timer
              ) {
                sendNotification();

                setTimeout(() => {
                    $("#paragraph_content_send_diligence").html($("#descriptionDiligence").val());
                    $("#div-content-all-diligence-send").show();
                    $("#div-diligence").hide();
                    $("#btn-actions-diligence").hide();
                    $("#descriptionDiligence").hide();
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
            console.log('success sendNotification', res);
            console.log({ res })
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
            console.log({ res })

            if (res.status == 400) {
                Swal.fire("Ops! Ocorreu um erro.");
            }
        }
    });
}
function saveDiligence(status) {
    console.log('saveDiligence', status)
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
        success: function (res) {
            console.log('sendAjax', res)
            if (res.status == 200) {
                console.log('status do envio', status)
                showSaveContent(status)
            }
        },
        error: function (err) {
            console.log({
                err
            })
        }
    });
}


