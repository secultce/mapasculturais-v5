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
        $("#descriptionDiligence").hide()
        $("#btn-actions-proponent").hide();
        $("#btn-save-diligence").hide();
        $("#btn-save-diligence-proponente").hide()
        $("#label-save-content-diligence").hide();
        $("#div-info-send").hide();
        $("#div-content-all-diligence-send").hide();
        $("#descriptionDiligence").show();
        $("#answer_diligence").hide();

        $("#descriptionDiligence").on("keyup", function() {
            var texto = $(this).val(); // Obtém o valor do textarea
            if (texto.slice(-1) == '') {
    
                if (MapasCulturais.isProponent) {
                    $("#btn-save-diligence-proponente").hide()
                } else {
                    $("#btn-save-diligence").hide()
                }
            } else {
                if (MapasCulturais.isProponent) {
                    $("#btn-save-diligence-proponente").show()
                } else {
                    $("#btn-save-diligence").show()
                }
            }
        });
        // if ($(this).val() > 0) {
        //     $("#btn-save-diligence").show();
        // }
        
    }

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

    return {
        showContentDiligence: showContentDiligence,
        hideCommon: hideCommon,
        hideRegistration: hideRegistration,
        showRegistration: showRegistration,
        formatDiligenceSendProponent: formatDiligenceSendProponent
      }
}());
