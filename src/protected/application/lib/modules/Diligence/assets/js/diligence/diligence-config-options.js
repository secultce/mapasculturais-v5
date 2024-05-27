$(document).ready(() => {
    if(MapasCulturais.entity.object.use_diligence === 'Sim') {
        $('p.field-use-multiple-diligence').show();
        $('p.field-diligence-days').show();
    }

    $('p.field-use-diligence').on('change', function(ev) {
        if (ev.target.value === 'Sim') {
            $('p.field-use-multiple-diligence').show();
            $('p.field-diligence-days').show();
        } else if (ev.target.value === 'Não') {
            $('p.field-use-multiple-diligence').hide();
            $('p.field-diligence-days').hide();
        }
    });
});
