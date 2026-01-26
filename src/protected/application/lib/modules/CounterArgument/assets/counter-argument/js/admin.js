const counterArgumentAdmin = {
    text: '',
    status: null,
    statuses: null,
    getText() {
        return this.text
    },
    setText(text) {
        this.text = text
    },
    getStatus() {
        return this.status
    },
    setStatus(status) {
        this.status = status
    },
    getStatuses() {
        $.ajax({
            type: "GET",
            url: MapasCulturais.createUrl('contrarrazao', 'getStatuses'),
            dataType: "json",
            success(res) {
                counterArgumentAdmin.setStatuses(res.statuses)
            },
            error(err) {
                console.log(err)
            }
        })
    },
    setStatuses(statuses) {
        this.statuses = statuses
    },
    respond(counterArgumentId) {
        $.ajax({
            type: "POST",
            url: MapasCulturais.createUrl('contrarrazao', 'respond'),
            data: {
                counterArgumentId,
                text: this.getText(),
                status: this.getStatus(),
            },
            dataType: "json",
            success(res) {
                Swal.fire({
                    title: 'Resposta salva',
                    text: res.message,
                    icon: 'success',
                    allowOutsideClick: false,
                }).then(res => {
                    if (res.isConfirmed) {
                        window.location.reload()
                    }
                })
            },
            error(err) {
                if (err.status === 403) {
                    Swal.fire({
                        title: 'Sua resposta não foi salva',
                        text: err.responseJSON.message,
                        icon: 'warning',
                        allowOutsideClick: false,
                    }).then(res => {
                        if (res.isConfirmed) window.location.reload()
                    })
                    return
                }

                Swal.fire({
                    title: 'Erro ao responder contrarrazão',
                    text: 'Entre em contato com o suporte ou tente novamente mais tarde.',
                    icon: 'error',
                })
            }
        })
    },
    publishResponses(opportunityId) {
        $.ajax({
            type: "POST",
            url: MapasCulturais.createUrl('contrarrazao', 'publishResponses'),
            data: { opportunityId },
            dataType: "json",
            success(res) {
                Swal.fire({
                    title: 'Respostas publicadas',
                    text: res.message,
                    icon: 'success',
                    allowOutsideClick: false,
                }).then(res => {
                    if (res.isConfirmed) window.location.reload()
                })
            },
            error(err) {
                if (err.status === 403) {
                    Swal.fire({
                        title: 'Respostas não publicadas',
                        text: err.responseJSON.message,
                        icon: 'warning',
                    })
                    return
                }

                Swal.fire({
                    title: 'Erro ao publicar respostas',
                    text: 'Entre em contato com o suporte ou tente novamente mais tarde.',
                    icon: 'error',
                })
            }
        })
    },
}

$(() => {
    counterArgumentAdmin.getStatuses()

    $('[btn-counter-argument-response]').on('click', function (event) {
        const counterArgumentId = event.currentTarget.dataset.id
        const text = event.currentTarget.dataset.text
        const status = event.currentTarget.dataset.status

        let quillEditor

        Swal.fire({
            title: 'Responder Contrarrazão',
            html: `
                <p class="sweetalert-plain-text">Digite sua resposta para esta contrarrazão e selecione sua situação</p>
                <div>
                    <div counter-argument-response-text class="form-group">${text}</div>
                    <div class="form-group">
                        <label for="counter-argument-status" class="sweetalert-label">Situação:</label>
                        <select class="form-control" id="counter-argument-status">
                            <option selected disabled>--- Selecione uma situação ---</option>
                            <option ${status == 10 ? 'selected' : ''} value="10">Deferida</option>
                            <option ${status == 3 ? 'selected' : ''} value="3">Indeferida</option>
                        </select>
                    </div>
                </div>
                <p>
                    <small><b>Atenção!</b> Ao salvar a resposta, somente você poderá editá-la.</small>
                </p>`,
            width: 700,
            confirmButtonText: 'Salvar',
            cancelButtonText: 'Cancelar',
            showCancelButton: true,
            allowOutsideClick: false,
            didOpen() {
                quillEditor = new Quill('[counter-argument-response-text]', {
                    theme: 'snow'
                })
            },
            willClose() {
                counterArgumentAdmin.setText(quillEditor.getSemanticHTML())
                counterArgumentAdmin.setStatus($('#counter-argument-status').val())
            },
        }).then(res => {
            if (res.isConfirmed) {
                if (!quillEditor.getText().trim() || counterArgumentAdmin.getStatus() === null) {
                    Swal.fire({
                        title: 'Sua resposta não foi salva',
                        text: 'Digite o texto da resposta e selecione uma situação',
                        icon: 'warning',
                    })
                    return
                }

                counterArgumentAdmin.respond(counterArgumentId)
            }
        })
    })

    $('#btn-publish-responses-counter-arguments').on('click', function (event) {
        const opportunityId = event.currentTarget.dataset.opportunityId

        Swal.fire({
            title: 'Publicar respostas',
            text: 'Ao publicar as respostas, não será mais possível editá-las. Deseja continuar?',
            confirmButtonText: 'Publicar',
            cancelButtonText: 'Cancelar',
            showCancelButton: true,
        }).then(res => {
            if (res.isConfirmed) counterArgumentAdmin.publishResponses(opportunityId)
        })
    })
})
