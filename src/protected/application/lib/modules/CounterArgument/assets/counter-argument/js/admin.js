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
                McMessages.success('Resposta salva', res.message).then((res) => {
                    if (res.isConfirmed) {
                        window.location.reload()
                    }
                })
            },
            error(err) {
                McMessages.error('Sua resposta não foi salva', 'Erro ao salvar resposta da contrarrazão. Tente novamente.')
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

        QuillEditor.open({
            title: 'Responder Contrarrazão',
            initialHtml: text,
            entityId: counterArgumentId,
            html: `
                <p class="sweetalert-plain-text">
                    Digite sua resposta para esta contrarrazão
                </p>

                <div class="form-group">
                    <label for="counter-argument-status" class="sweetalert-label">
                        Situação:
                    </label>
                    <select
                        class="form-control"
                        id="counter-argument-status"
                        name="status"
                    >
                        <option value="" disabled selected>
                            --- Selecione uma situação ---
                        </option>
                        <option value="10" ${status == 10 ? 'selected' : ''}>Deferida</option>
                        <option value="3"  ${status == 3  ? 'selected' : ''}>Indeferida</option>
                    </select>
                </div>
            `,
            showFile: false,

            onOpen: () => {
                const confirmBtn = Swal.getConfirmButton();
                const statusSelect = document.getElementById('counter-argument-status');

                // Disable confirm initially
                confirmBtn.disabled = !statusSelect.value;

                // Enable when a valid status is selected
                statusSelect.addEventListener('change', () => {
                    confirmBtn.disabled = !statusSelect.value;
                });
            }
        }).then(result => {
            if (!result.isConfirmed) return;

            const { conteudo, entityId, customFields } = result.value;

            counterArgumentAdmin.setText(conteudo);
            counterArgumentAdmin.setStatus(customFields.status);
            counterArgumentAdmin.respond(entityId);
        });
    })
})
