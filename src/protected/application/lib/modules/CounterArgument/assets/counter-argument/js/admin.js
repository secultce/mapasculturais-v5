const counterArgumentAdmin = {
    text: '',
    status: null,
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
                Swal.fire({
                    title: 'Sua resposta não foi salva',
                    text: 'Erro ao salvar resposta da contrarrazão. Tente novamente.',
                    icon: 'error',
                })
            }
        })
    },
}

$(() => {
    $('[btn-view-counter-argument-response]').on('click', function (event) {
        const counterArgumentId = event.currentTarget.dataset.id
        const text = event.currentTarget.dataset.text
        const status = event.currentTarget.dataset.status

        let quillEditor

        Swal.fire({
            title: 'Responder Contrarrazão',
            html: `
                <p class="sweetalert-plain-text">Digite sua resposta para esta contrarrazão</p>
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
                </div>`,
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
})
