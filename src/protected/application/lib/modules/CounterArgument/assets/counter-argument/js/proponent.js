const counterArgument = {
    text: '',
    files: [],
    getText() {
        return this.text
    },
    setText(text) {
        this.text = text
    },
    getFiles() {
        return this.files
    },
    setFiles(files) {
        this.files = files
    },
    send(registration) {
        const formData = new FormData()
        formData.append('text', this.getText())
        formData.append('registration', registration)

        Array.from(this.getFiles()).forEach((file, index) => {
            formData.append(index, file)
        })

        $.ajax({
            type: "POST",
            url: MapasCulturais.createUrl('contrarrazao', 'send'),
            data: formData,
            contentType: false,
            processData: false,
            dataType: "json",
            success(res) {
                McMessages.messageConfirm(
                    'Contrarrazão enviada',
                    res.message,
                    'Ficar nesta página',
                    'Ir para o painel',
                    'btn btn-primary',
                    'btn btn-secondary',
                    false,
                    null,
                    false
                ).then(res => {
                    if (res.isConfirmed) {
                        window.location.href = MapasCulturais.createUrl('panel', 'counterArguments')
                    }
                })
            },
            error(err) {
                McMessages.error('Sua contrarrazão não foi enviada!', 'Erro ao enviar contrarrazão. Tente novamente.')
            }
        })
    },
    update(id) {
        const formData = new FormData()
        formData.append('id', id)
        formData.append('text', this.getText())

        Array.from(this.getFiles()).forEach((file, index) => {
            formData.append(index, file)
        })

        $.ajax({
            type: "POST",
            url: MapasCulturais.createUrl('contrarrazao', 'update'),
            data: formData,
            contentType: false,
            processData: false,
            dataType: "json",
            success(res) {
                McMessages.success('Contrarrazão atualizada!', res.message).then((res) => {
                    if (res.isConfirmed) {
                        window.location.reload()
                    }
                })
            },
            error(err) {
                McMessages.error('Sua contrarrazão não foi atualizada!', 'Erro ao atualizar contrarrazão. Tente novamente.')
            }
        })
    },
    removeFile(fileId) {
        $.ajax({
            type: "POST",
            url: MapasCulturais.createUrl('contrarrazao', 'removeFile'),
            data: { fileId },
            dataType: "json",
            success(res) {
                McMessages.success('Arquivo removido', res.message).then((res) => {
                    if (res.isConfirmed) {
                        window.location.reload()
                    }
                })
            },
            error(err) {
                McMessages.error('Arquivo não removido', 'Erro ao remover arquivo da contrarrazão. Tente novamente.')
            }
        })
    }
}

$(() => {
    $('[open-counter-argument]').on('click', function (event) {
        const registration = event.currentTarget.dataset.registration

        QuillEditor.open({
            title: 'Abrir Contrarrazão',
            placeholder: 'Digite o texto da sua contrarrazão...',
            entityId: registration,
            html: `
                <p class="sweetalert-plain-text">
                    Digite o texto da sua contrarrazão e anexe seus arquivos
                </p>
            `,
            showFile: true
        }).then(result => {
            if (!result.isConfirmed) return;

            const { conteudo, entityId } = result.value;

            counterArgument.setText(conteudo);

            const fileInput = document.querySelector(`#edit-recourse-file-${entityId}`);
            if (fileInput) {
                counterArgument.setFiles(fileInput.files);
            }

            counterArgument.send(entityId);
        });
    })

    $('[edit-counter-argument-btn]').on('click', function (event) {
        const id = event.currentTarget.dataset.id
        const text = event.currentTarget.dataset.text

        QuillEditor.open({
            title: 'Editar Contrarrazão',
            initialHtml: text,
            entityId: id,
            html: `
                <p class="sweetalert-plain-text">
                    Você pode editar o texto da sua contrarrazão e anexar mais arquivos
                </p>
            `,
            showFile: true
        }).then(result => {
            if (!result.isConfirmed) return;

            const { conteudo, entityId, customFields } = result.value;

            counterArgument.setText(conteudo);

            const fileInput = document.querySelector(`#edit-recourse-file-${entityId}`);
            if (fileInput) {
                counterArgument.setFiles(fileInput.files);
            }

            counterArgument.update(entityId);
        });
    })

    $('[remove-counter-argument-file]').on('click', function (event) {
        const fileId = event.currentTarget.dataset.fileId

        McMessages.messageConfirm(
            'Remover arquivo da contrarrazão',
            'Deseja realmente remover este arquivo da contrarrazão?',
            'Cancelar',
            'Remover',
            'btn btn-danger',
            'btn btn-secondary',
            false,
            null,
            false
        ).then(res => {
            if (res.isConfirmed) {
                counterArgument.removeFile(fileId)
            }
        })
    })
})
