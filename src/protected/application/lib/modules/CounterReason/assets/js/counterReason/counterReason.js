/* global jQuery, $, MapasCulturais */
jQuery(document).ready(function($) {
    if(MapasCulturais.entity.object.claimDisabled == 0){
        console.log(MapasCulturais.entity.object.claimDisabled);
        $("#div-panel-counter-reason").removeClass("d-none");
    }else {
        $("#div-panel-counter-reason").addClass("d-none");
    }
})