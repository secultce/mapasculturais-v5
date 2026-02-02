const counterArgumentCommon = {
    view(text) {
       McMessages.custom('Contrarrazão', null, text, true);
    },
    viewResponse(text) {
        Swal.fire({
            title: 'Resposta da Contrarrazão',
            html: text,
            width: 700,
        })
    }
}

$(() => {
    $('[btn-view-counter-argument]').on('click', function (event) {
        const text = event.currentTarget.dataset.text
        counterArgumentCommon.view(text)
    })

    $('[btn-view-counter-argument-response]').on('click', function (event) {
        const text = event.currentTarget.dataset.text
        counterArgumentCommon.viewResponse(text)
    })
})
