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
                McMessages.success('Resposta salva', res.message).then((res) => {
                    if (res.isConfirmed) {
                        window.location.reload()
                    }
                })
            },
            error(err) {
                if (err.status === 403) {
                    McMessages.error(
                        'Sua resposta não foi salva', 
                        err.responseJSON.message
                    ).then(res => {
                        if (res.isConfirmed) window.location.reload()
                    })
                    return
                }
                McMessages.error('Erro ao responder contrarrazão', 'Entre em contato com o suporte ou tente novamente mais tarde.')
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
        const statuses = new Map(Object.entries(counterArgumentAdmin.statuses).sort((a, b) => a[1].localeCompare(b[1])))

        QuillEditor.open({
            title: 'Responder Contrarrazão',
            initialHtml: text,
            entityId: counterArgumentId,
            html: `
                <p class="sweetalert-plain-text">Digite sua resposta para esta contrarrazão e selecione sua situação</p>
                <div>
                    <div counter-argument-response-text class="form-group">${text}</div>
                    <div class="form-group">
                        <label for="counter-argument-status" class="sweetalert-label">Situação:</label>
                        <select class="form-control" id="counter-argument-status">
                            <option selected disabled>--- Selecione uma situação ---</option>
                            ${Array.from(statuses.entries()).map(([key, value]) => `<option value="${key}" ${key == status ? 'selected' : ''}>${value}</option>`).join('')}
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
            console.log(res)
            const { conteudo, entityId, customFields } = res.value;
            console.log(customFields)
            if (res.isConfirmed) {
                if (!conteudo.trim() || customFields['counter-argument-status'] === null ) {
                    McMessages.error('Sua resposta não foi salva', 'Digite o texto da resposta e selecione uma situação')
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

        McMessages.messageConfirm(
            'Publicar respostas',
            'Ao publicar as respostas, não será mais possível editá-las. Deseja continuar?'
        )
    })
})
