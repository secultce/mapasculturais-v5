var EntityDiligence = (function(){
    
    function resolveAfter2Seconds() {
        return new Promise((resolve, reject) => {
            setTimeout(() => {
                $.ajax({
                    type: "GET",
                    url: MapasCulturais.createUrl('diligence', 'getcontent/' + MapasCulturais.entity.id),
                    dataType: "json",
                    success: function(res) {
                      
                        resolve(res)                
                        // return res
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
        let object = {}
       
        const result = await resolveAfter2Seconds();
        return result;
       
       
    }

    function hideRegistration() {
        $("#registration-content-all").hide();
    }
    
    function showRegistration() {
        $("#registration-content-all").show();
    }

    function hideCommon()
    {
        console.log('hideCommon')
        var urlAtual = window.location.href;
        // Declaração da variável fora de qualquer função para torná-la global
      
        // Sentença de string que você deseja verificar na URL
        var sentencaDesejada = "#/tab=diligence-diligence";

        if (urlAtual.includes(sentencaDesejada)) {
            console.log("A URL contém a sentença desejada.");
            $("#registration-content-all").hide();
        } else {
            console.log("A URL não contém a sentença desejada.");
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
        console.log(MapasCulturais.isProponent)
        $("#descriptionDiligence").on("keyup", function() {
            var texto = $(this).val(); // Obtém o valor do textarea
            if (texto.slice(-1) == '') {
               
                if (MapasCulturais.isProponent) {
                    $("#btn-save-diligence-proponent").hide()
                    $("#btn-send-diligence-proponente").hide()
                } else {
                    $("#btn-save-diligence").hide()
                }
            } else {
                if (MapasCulturais.isProponent) {
                    $("#btn-save-diligence-proponent").show()
                    $("#btn-send-diligence-proponente").show()
                } else {
                    $("#btn-save-diligence").show()
                }
            }
        });
        // if ($(this).val() > 0) {
        //     $("#btn-save-diligence").show();
        // }
        
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
        $("#btn-save-diligence").hide();
        $("#btn-send-diligence").hide();
    }

    function hideShowSuccessAction()
    {
        $("#label-save-content-diligence").show();

        setTimeout(() => {
            $("#label-save-content-diligence").hide()
        }, 2000);
    }

    function showAnswerDraft(answer)
    {
        $("#paragraph_content_send_diligence").html(answer.diligence.description);
        $("#div-content-all-diligence-send").show();
        $("#descriptionDiligence").val(answer.answer);
        $("#div-btn-actions-proponent").show();
        $("#btn-save-diligence-proponent").show();
        $("#btn-send-diligence-proponente").show();
        $("#paragraph_loading_content").hide();

    }

    function getLimitDateAnswer(sendDiligence)
    {
        // Convertendo a string de data em um objeto Date
        const originalDate = new Date(sendDiligence);

        // Adicionando três dias à data
        originalDate.setDate(originalDate.getDate() + 3);

        // Formatando a nova data de acordo com suas necessidades
        const newDate = originalDate.toISOString().split('T')[0];

        var dateTimeDiligence = new Date(newDate);

        // Obtendo a data e hora atual
        var dtNow = new Date();

        // Verificando se novaData é posterior à data atual
        if (dateTimeDiligence < dtNow) {
            return "encerrou";
        }
        return "no_periodo";
    }

    return {
        showContentDiligence: showContentDiligence,
        hideCommon: hideCommon,
        hideRegistration: hideRegistration,
        showRegistration: showRegistration,
        formatDiligenceSendProponent: formatDiligenceSendProponent,
        hideShowSuccessAction: hideShowSuccessAction,
        showAnswerDraft: showAnswerDraft,
        getLimitDateAnswer: getLimitDateAnswer
      }
}());
