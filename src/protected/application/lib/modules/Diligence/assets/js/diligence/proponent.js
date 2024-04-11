$(document).ready(function () {
    
    EntityDiligence.hideCommon();
    let entityDiligence = EntityDiligence.showContentDiligence();
    entityDiligence
    .then((res) => {
        console.log({res})
        if (
            (res.message == 'sem_diligencia' || res.message == 'diligencia_aberta') &&
            MapasCulturais.userEvaluate == false) 
        {
            // if(res.data.length > 0){
            //     $("#descriptionDiligence").val(res.data[0].description);
            //     $("#btn-save-diligence").show();
            // }
            res.data.forEach((element, index) => {
                console.log({ element })
                console.log({ index })
                //Recebendo o id da diligencia
                MapasCulturais.idDiligence = element.id;
                if (element.status == 3) {
                    console.log(element.description)
                    $("#paragraph_content_send_diligence").html(element.description);
                    $("#div-content-all-diligence-send").show();
                    $("#div-btn-actions-proponent").show();
                    $("#paragraph_loading_content").hide();
               
                }else{
                    // $("#li-tab-diligence-diligence > a").remove();
                    // $("#li-tab-diligence-diligence").append('<label>Diligência</label>');
                    // $("#li-tab-diligence-diligence > label").addClass('cursor-disabled');
                }
            });         
        }

        if (res.message == 'resposta_rascunho' &&  MapasCulturais.userEvaluate == false) 
        {
            res.data.forEach((answer, index) => {
                MapasCulturais.idDiligence = answer.diligence.id;               
                EntityDiligence.showAnswerDraft(answer);
                $("#descriptionDiligence").show();
                $("#div-btn-actions-proponent").show();
            });   
        }

        if(res.message == "resposta_enviada" &&  MapasCulturais.userEvaluate == false){
            res.data.forEach((answer, index) => {
                MapasCulturais.idDiligence = answer.diligence.id;
                EntityDiligence.showAnswerDraft(answer);
                $("#paragraph_content_send_answer").html(answer.answer);
                $("#answer_diligence").show();
                $("#descriptionDiligence").hide();
                $("#div-btn-actions-proponent").hide();
            });   
        }
    })
    .catch((error) => {
        console.log(error)
    })
   
});

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
            hideViewActions()
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                saveRequestAnswer(status)
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
                }).then((result) => {
                    console.log('dialog progress', result);
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        console.log('Notificar')
                        
                    }
                    console.log('isDismissed',result.isDismissed)
                    if (result.isDismissed && result.dismiss === 'cancel') {
                        console.log('Cliquei em DESFAZER')
                        showViewActions();                       
                    }
                    console.log('DismissReason', Swal.DismissReason.timer)
                    console.log('dismiss', result.dismiss)
                    if (
                        result.dismiss === Swal.DismissReason.timer
                      ) {
                        console.log('O tempo acabou!');
                        hideViewActions();
                        // Aqui você pode adicionar a ação que deseja executar quando o tempo terminar
                      } 
                });
            }

            if (result.isDenied) {
                console.log('click no desistir')
                $("#div-btn-actions-proponent").show();
                $("#descriptionDiligence").show();
                $("#div-content-all-diligence-send").show();
                $("#answer_diligence").hide();
                // $("#paragraph_content_send_answer").html($("#descriptionDiligence").val());
                
            }

        });
    } else {
        saveRequestAnswer(status)
    }

   
}

function cancelAnswer(diligence)
{
    $.ajax({
        type: "POST",
        url: MapasCulturais.createUrl('diligence', 'cancelsendAnswer'),
        data: {
            registration: MapasCulturais.entity.id
        },
        dataType: "json",
        success: function(response) {
            // showSaveContent(status);
            console.log('resposta do cancelamento' , response)
           if(response.status == 200){
            // EntityDiligence.hideShowSuccessAction();
           }
        }
    });
}

function saveRequestAnswer(status)
{
    $.ajax({
        type: "POST",
        url: MapasCulturais.createUrl('diligence', 'answer'),
        data: {
            diligence: MapasCulturais.idDiligence,
            answer: $("#descriptionDiligence").val(),
            status: status
        },
        dataType: "json",
        success: function(response) {
            // showSaveContent(status);
            console.log({
                response
            })
           if(response.status == 200){
            EntityDiligence.hideShowSuccessAction();
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
}

function showViewActions()
{
    $("#paragraph_content_send_answer").html($("#descriptionDiligence").val());
    $("#div-btn-actions-proponent").show();
    $("#descriptionDiligence").show();
    $("#div-content-all-diligence-send").hide();
    $("#answer_diligence").hide();
    $("#div-content-all-diligence-send").show();
}



