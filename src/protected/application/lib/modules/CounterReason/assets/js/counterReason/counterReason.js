// 0 - habilitado, 1 - para desabilitado
jQuery(document).ready(function($) {
    if(MapasCulturais.entity.object.claimDisabled == 0){
        $("#div-panel-counter-reason").removeClass("d-none");
    }else {
        $("#div-panel-counter-reason").addClass("d-none");
    }
    // Quando alterar a escolha dos recursos
    $('#disable-appeal-wrapper').on('change', event => {
        const isDisabled = parseInt(event.target.value);
        if (isDisabled == 0) {
            $("#div-panel-counter-reason").removeClass("d-none");
        }else{
            $("#div-panel-counter-reason").addClass("d-none");
        }
    });
});