const counterArgumentCommon = {
    view(text) {
       McMessages.custom('Contrarrazão', null, text, true);
    },
}

$(() => {
    $('[btn-view-counter-argument]').on('click', function (event) {
        const text = event.currentTarget.dataset.text
        counterArgumentCommon.view(text)
    })
})
