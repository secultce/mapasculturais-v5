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
    }
    
    function showRegistration() {
        $("#registration-content-all").show();
    }

    function hideCommon()
    {
       
        // Declaração da variável fora de qualquer função para torná-la global
        var urlAtual = window.location.href;
      
        // Sentença de string para ser verificada na URL
        var sentencaDesejada = "#/tab=diligence-diligence";

        if (urlAtual.includes(sentencaDesejada)) {
            $("#registration-content-all").hide();
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

    function verifySituation(data)
    {
          //Sempre verifica a situação para habilitar o botão de Fializar avaliação e avançar
        if (data.id > 0) {
           
           switch (data.situation) {
            case 2:
                $("#btn-open-diligence").addClass('btn-diligence-open-desactive');
                $("#btn-open-diligence").attr('disabled', true);
                $("#btn-submit-evaluation").addClass('btn-diligence-open-desactive');
                $("#btn-submit-evaluation").attr('disabled', true);
                break;
            case 3:
                $("#btn-open-diligence").removeClass('btn-diligence-open-desactive');
                $("#btn-open-diligence").attr('disabled',false);
                $("#btn-submit-evaluation").removeClass('btn-diligence-open-desactive');
                $("#btn-submit-evaluation").attr('disabled', false);
                break;            
            default:
                break;
           }
        }
    }

    return {
        showContentDiligence: showContentDiligence,
        hideCommon: hideCommon,
        hideRegistration: hideRegistration,
        showRegistration: showRegistration,
        formatDiligenceSendProponent: formatDiligenceSendProponent,
        hideShowSuccessAction: hideShowSuccessAction,
        showAnswerDraft: showAnswerDraft,
        getLimitDateAnswer: getLimitDateAnswer,
        verifySituation: verifySituation,
        returnGetAuthorized: returnGetAuthorized
      }
}());
