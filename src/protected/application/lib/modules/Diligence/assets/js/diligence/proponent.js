$(document).ready(function () {
    EntityDiligence.showAccordion('#accordion');
    //Remove o botão de abrir diligencias
    EntityDiligence.removeBtnOpenDiligence();

    //id da diligencia
    let idDiligence = 0;
    $("#id-input-diligence").val(idDiligence);
    
    //Ocuta os arquivo comuns
    EntityDiligence.hideCommon();
    let entityDiligence = EntityDiligence.showContentDiligence();
    entityDiligence
    .then((res) => {
        console.log({res})
        //Bloqueando aba para proponente
        $("#li-tab-diligence-diligence > a").remove();
        $("#li-tab-diligence-diligence").append('<label>Diligência</label>');
        $("#li-tab-diligence-diligence > label").addClass('cursor-disabled');
        
        if (
            (res.message == 'sem_diligencia') &&
            MapasCulturais.userEvaluate == false) 
        {
            //Se tiver diligencia
            if (res.data.length > 0) {
               

                res.data.forEach((element, index) => {
                    const dateLimitDate = EntityDiligence.validateLimiteDate(MapasCulturais.diligence_days)
                    
                    if (dateLimitDate) {
                        $("#descriptionDiligence").hide();
                        $("#div-btn-actions-proponent").hide() 
                    }else{
                        $("#div-btn-actions-proponent").show();                    
                    }
                    //Id da diligencia
                    MapasCulturais.idDiligence = element.id;
                    
                   
                    $("#paragraph_loading_content").hide();
                });         
            }

            
        }

       
        if(res.message !== 'sem_diligencia' &&  MapasCulturais.userEvaluate == false) {
           
            hideAnswerDraft();
            let actions = false;
            // console.log({actions})
            idsDiligences = [];
            res.data.forEach((answer, index) => {
                // console.log({answer})
                // console.log('id ', answer.id.length)
                console.log({answer})
                if(answer?.id === undefined)
                {
                    console.log('sem_diligencia')
                    EntityDiligence.showAnswerDraft(null);
                    $("#descriptionDiligence").show();
                }else{
                    //Está em rescunho
                    // if(answer.status == 0){
                    //     $("#descriptionDiligence").hide();
                    // }
                    idsDiligences.push(answer?.id);
                }
                if(answer?.status == 0) {
                    $("#descriptionDiligence").hide();
                }
            })
    
            console.log(idsDiligences);
            MapasCulturais.idDiligence = Math.max.apply(null, idsDiligences);    
            const ahref ='<a href="#diligence-diligence" rel="noopener noreferrer" onclick="hideRegistration()" id="tab-main-content-diligence-diligence">Diligência</a>';
                $("#li-tab-diligence-diligence > label").removeClass('cursor-disabled');
                $("#li-tab-diligence-diligence > label").remove();
                $("#li-tab-diligence-diligence").append(ahref);
        }
            

        // if (res.message == 'resposta_rascunho' &&  MapasCulturais.userEvaluate == false) 
        // {
        //     console.log({res})
        //     const ahref ='<a href="#diligence-diligence" rel="noopener noreferrer" onclick="hideRegistration()" id="tab-main-content-diligence-diligence">Diligência</a>';
        //         $("#li-tab-diligence-diligence > label").removeClass('cursor-disabled');
        //         $("#li-tab-diligence-diligence > label").remove();
        //         $("#li-tab-diligence-diligence").append(ahref);
        //     res.data.forEach((answer, index) => {
        //         const limitDate = EntityDiligence.validateLimiteDate(answer.diligence.sendDiligence.date);

        //         if(limitDate){
        //             EntityDiligence.showAnswerDraft(answer);
        //             $("#descriptionDiligence").hide();
        //             $("#div-btn-actions-proponent").hide() 
        //         }else{
        //             MapasCulturais.idDiligence = answer.diligence.id;               
        //             EntityDiligence.showAnswerDraft(answer);
        //             $("#descriptionDiligence").show();
        //             $("#div-btn-actions-proponent").show();
        //         }

               
        //     });   
        // }

        // if(res.message == "resposta_enviada" &&  MapasCulturais.userEvaluate == false){
        //     res.data.forEach((answer, index) => {
        //         MapasCulturais.idDiligence = answer.diligence.id;
        //         EntityDiligence.showAnswerDraft(answer);
        //         $("#paragraph_content_send_answer").html(answer.answer);
        //         $("#answer_diligence").show();
        //         $("#descriptionDiligence").hide();
        //         $("#div-btn-actions-proponent").hide();
        //         $("#paragraph_createTimestamp").html(moment(answer.diligence.sendDiligence.date).format('lll'));
        //         $(".paragraph_createTimestamp_answer").html(moment(answer.createTimestamp.date).format('lll'))
        //         const ahref ='<a href="#diligence-diligence" rel="noopener noreferrer" onclick="hideRegistration()" id="tab-main-content-diligence-diligence">Diligência</a>';
        //         $("#li-tab-diligence-diligence > label").removeClass('cursor-disabled');
        //         $("#li-tab-diligence-diligence > label").remove();
        //         $("#li-tab-diligence-diligence").append(ahref);
        //         $(".footer-btn-delete-file-diligence").hide();
        //     });   
        // }

  
        $("#upload-file-diligence").submit(function(e) {
            MapasCulturais.countFileUpload = (MapasCulturais.countFileUpload + 1);
            const baseUrl = MapasCulturais.baseURL+'inscricao/'+MapasCulturais.entity.id
            if(MapasCulturais.countFileUpload >= 2)
            {
                $("#div-upload-file-count").hide();
                $("#info-title-limit-file-diligence").html('Limite de arquivo excedido <button class="btn-reload-diligence" onClick="window.location.reload();" title="Recarregar arquivos"> <i class="fa fa-redo-alt"></i> </button>');
            }
        });




    })
    .catch((error) => {
        // console.log({error})
        // MapasCulturais.Messages.error('Erro ao carregar diligência');
    })
   
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
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {
                        sendNofificationAnswer();
                    }
                    
                    if (result.isDismissed && result.dismiss === 'cancel') {
                        showViewActions();
                        cancelAnswer();                  
                    }
                  
                    if (
                        result.dismiss === Swal.DismissReason.timer
                      ) {
                        sendNofificationAnswer();
                        hideViewActions();
                        
                        // Aqui você pode adicionar a ação que deseja executar quando o tempo terminar
                      } 
                }).catch( (err) => {
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
        saveRequestAnswer(status)
    }

   
}

function cancelAnswer()
{
    $.ajax({
        type: "PUT",
        url: MapasCulturais.createUrl('diligence', 'cancelsendAnswer'),
        data: {
            diligence: MapasCulturais.idDiligence,
        },
        dataType: "json",
        success: function(response) {
           if(response.status == 200){
            EntityDiligence.hideShowSuccessAction();
           }
        }
    });
}

function saveRequestAnswer(status)
{
    console.log({status})
    console.log('id ', $('#id-input-diligence').val())
    idAnswer = $('#id-input-diligence').val()
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
            })
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
        success: function(response) {
           return true;
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

