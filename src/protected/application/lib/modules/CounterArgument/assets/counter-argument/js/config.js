$(() => {
    const opportunity = MapasCulturais.entity.object

    if (opportunity.appealEnabled === 'Sim') $('#counter-argument-config-wrapper').removeClass('d-none')

    $('#enabled-appeal-wrapper').on('change', event => {
        const opt = event.target.value
        const appealEnabled = opt === 'Sim' ? true : false

        if (appealEnabled) {
            $('#counter-argument-config-wrapper').removeClass('d-none')
            return
        }

        $('#counter-argument-config-wrapper').addClass('d-none')
    })
})
