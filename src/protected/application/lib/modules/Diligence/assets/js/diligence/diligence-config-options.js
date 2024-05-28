$(document).ready(() => {
    if(MapasCulturais.entity.object.use_diligence === 'Sim') {
        $('.field-use-multiple-diligence').show();
        $('.field-diligence-days').show();
    }

    $('.field-use-diligence').on('change', function(ev) {
        if (ev.target.value === 'Sim') {
            $('.field-use-multiple-diligence').show();
            $('.field-diligence-days').show();
        } else if (ev.target.value === 'Não') {
            $('.field-use-multiple-diligence').hide();
            $('.field-diligence-days').hide();
        }
    });
});
