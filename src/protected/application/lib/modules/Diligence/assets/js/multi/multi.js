$(document).ready(function () {
    $("#p-btn-tado").hide();
    $(".multi-itens-select").hide();

    $( "#situacion-refo-multi" ).on( "change", function(e) {
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