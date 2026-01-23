const counterArgumentCommon = {
    view(text) {
        Swal.fire({
            title: 'Contrarrazão',
            html: text,
            width: 700,
        })
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
