var EntityDiligence = (function(){
    //Conteúdo da diligencia
    function contentDiligence() {
        return new Promise((resolve, reject) => {
            setTimeout(() => {
                $.ajax({
                    type: "GET",
                    url: MapasCulturais.createUrl('diligence', 'getcontent/' + MapasCulturais.entity.id),
                    dataType: "json",
                    success: function(res) {
                      
                        resolve(res)
                    },
                    error: function(err) {
                        return err
                    }
                });
            }, 2000);
          });
      }

    async function showContentDiligence()
    {
        const result = await contentDiligence();
        return result;
    }
    //Promise para buscar e devolver informação da 
    function getAuthorized()
    {
        const regis = MapasCulturais.entity.id;
        return new Promise((resolve, reject) => {
            setTimeout(() => {
                $.ajax({
                    type: "GET",
                    url: MapasCulturais.createUrl('diligence', 'getAuthorizedProject/'+regis),
                    dataType: "json",
                    success: function (response) {
                        resolve(response)
                    }
                });
            }, 2000);
        });
    }

    async function returnGetAuthorized()
    {
        const result = await getAuthorized();
        return result;        
    }

    function hideRegistration() {
        $("#registration-content-all").hide();
        $('#registration-attachments').hide();
    }
    
    function showRegistration() {
        $("#registration-content-all").show();
        $('#registration-attachments').show();
    }

    function hideCommon()
    {  
        // Declaração da variável fora de qualquer função para torná-la global
        var urlAtual = window.location.href;      
        // Sentença de string para ser verificada na URL
        var sentencaDesejada = "#/tab=diligence-diligence";
        if (urlAtual.includes(sentencaDesejada)) {
            $("#registration-content-all").hide();
            $('#registration-attachments').hide();
        } else {
            $("#diligence-diligence").hide();

        }
        $("#img-loading-content").attr('src', MapasCulturais.spinnerUrl)
        $("#descriptionDiligence").hide()
        $("#btn-save-diligence-proponent").hide()
        $("#btn-send-diligence-proponente").hide()        
        $("#btn-save-diligence").hide();
        $("#label-save-content-diligence").hide();
        $("#div-info-send").hide();
        $("#div-content-all-diligence-send").hide();
        $("#descriptionDiligence").show();
        $("#answer_diligence").hide();
    }
    /**
     * Formata a página para o parecerista apos envio de diligencia so proponente
     * @param {diligence} res 
     */
    function formatDiligenceSendProponent(res)
    {
        $("#descriptionDiligence").hide();
        $("#paragraph_info_status_diligence").hide();
        $("#paragraph_content_send_diligence").html(res.description);
        $("#div-content-all-diligence-send").show();
        $("#paragraph_createTimestamp").html(moment(res.sendDiligence.date).format("LLL"));
        $("#div-info-send").show();
    }

    function hideShowSuccessAction()
    {
        $("#label-save-content-diligence").show();
        setTimeout(() => {
            $("#label-save-content-diligence").hide()
        }, 2000);
    }

    function showAnswerDraft()
    {
        $("#div-btn-actions-proponent").show();
        $("#btn-save-diligence-proponent").show();
        $("#btn-send-diligence-proponente").show();
        $("#paragraph_loading_content").hide();
        $("#div-content-all-diligence-send").show();
    }

    function validateLimiteDate(diligence_days)
    {
        //data atual
        const dateNow = new Date();
        //Data limitada para resposta
        const dateLimitDate = new Date(diligence_days);

        if (dateNow >= dateLimitDate ) {
            return true
        }
        return false
    }

    //Oculta botão de abrir diligencia
    function hideBtnOpenDiligence()
    {
        $("#btn-open-diligence").attr('disabled', true);
        $("#btn-open-diligence").addClass('btn-diligence-open-desactive');
        $(".btn-diligence-open-active").addClass('btn-diligence-open-desactive');
    }
    //Remove o botão de abrir diligencia
    function removeBtnOpenDiligence()
    {
        $("#btn-open-diligence").remove();
        $("#btn-open-diligence").addClass('btn-diligence-open-desactive');
    }

    //Mostra o botão de abrir diligencia
    function showBtnOpenDiligence()
    {
        $("#btn-open-diligence").removeAttr('disabled');
        $("#btn-open-diligence").removeClass('btn-diligence-open-desactive');
    }

    function showAccordion(idRef)
    {
        $( idRef ).accordion({
            active: false,
            collapsible: true,
            heightStyle: "content"
        });
        $(".div-accordion-diligence > span").remove();
    }


    function editDescription(description, id)
    {
        $("#descriptionDiligence").show();
        $("#descriptionDiligence").html(description);
        $("#id-input-diligence").val(id);
        $("#draft-description-diligence").remove();
    }
    
    return {
        showContentDiligence: showContentDiligence,
        hideCommon: hideCommon,
        hideRegistration: hideRegistration,
        showRegistration: showRegistration,
        formatDiligenceSendProponent: formatDiligenceSendProponent,
        hideShowSuccessAction: hideShowSuccessAction,
        showAnswerDraft: showAnswerDraft,
        validateLimiteDate: validateLimiteDate,
        returnGetAuthorized: returnGetAuthorized,
        hideBtnOpenDiligence: hideBtnOpenDiligence,
        showBtnOpenDiligence: showBtnOpenDiligence,
        removeBtnOpenDiligence: removeBtnOpenDiligence,
        showAccordion: showAccordion,
        editDescription: editDescription
      }
}());
