$(document).ready(function () {
    $("#p-btn-tado").hide();
    $(".multi-itens-select").hide();
    //Retornando o valor da situação
    getSituacion();
    $( "#situacion-refo-multi" ).on( "change", function(e) {

        sendSituacion(e.target.value)
        
        if(e.target.value == 'approved')
        {
            $(".multi-itens-select").hide();
            $("#p-btn-tado").show();
        }else{            
            $(".multi-itens-select").show();
            $("#p-btn-tado").hide();
        }
    });
});

/**
 * Envia o situação para salvar ou alterar o valor
 * @param {string} valueSituacion 
 */
function sendSituacion(valueSituacion)
{
    $.ajax({
        type: "POST",
        url: MapasCulturais.createUrl('refo', 'situacion'),
        data: {situacion: valueSituacion, entity: MapasCulturais.entity.id},
        dataType: "json",
        success: function (res) {
            if (res.status == 200) {
                MapasCulturais.Messages.success('Salvo');
            }
        },
        error: function (err) {
            MapasCulturais.Messages.error(err.responseJSON);
        }
    });
}

//Setando o valor cadastrado no banco
function getSituacion()
{
    $.ajax({
        type: "GET",
        url: MapasCulturais.createUrl('refo', 'getSituacionPC/'+MapasCulturais.entity.id),
        dataType: "json",
        success: function (response) {
            if(response.situacion == 'all'){
                $(".multi-itens-select").hide();
                $("#p-btn-tado").hide();
            }else{
                $("#situacion-refo-multi").val(response.situacion).change();
            }
            
        }
    });
}