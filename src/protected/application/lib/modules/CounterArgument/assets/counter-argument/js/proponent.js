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
                Swal.fire({
                    title: 'Contrarrazão enviada',
                    text: res.message,
                    icon: 'success',
                    confirmButtonText: 'Ir para o painel',
                    allowOutsideClick: false,
                }).then(res => {
                    if (res.isConfirmed) {
                        window.location.href = MapasCulturais.createUrl('panel', 'counterArguments')
                    }
                })
            },
            error(err) {
                console.log(err)
                Swal.fire({
                    title: 'Contrarrazão não enviada',
                    text: err?.responseJSON?.message || 'Erro ao enviar contrarrazão. Tente novamente.',
                    icon: 'error',
                })
            }
        })
    },
    view(text) {
        Swal.fire({
            title: 'Contrarrazão',
            html: text,
            width: 700,
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
                Swal.fire({
                    title: 'Contrarrazão atualizada',
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
                    title: 'Contrarrazão não atualizada',
                    text: 'Erro ao atualizar contrarrazão. Tente novamente.',
                    icon: 'error',
                })
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
                Swal.fire({
                    title: 'Arquivo removido',
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
                    title: 'Arquivo não removido',
                    text: 'Erro ao remover arquivo da contrarrazão. Tente novamente.',
                    icon: 'error',
                })
            }
        })
    }
}

$(() => {
    $('[open-counter-argument]').on('click', function (event) {
        let quillEditor
        const registration = event.currentTarget.dataset.registration

        Swal.fire({
            title: 'Abrir Contrarrazão',
            html: `
                <p class="sweetalert-plain-text">Digite o texto da sua contrarrazão e anexe seus arquivos</p>
                <div>
                    <div counter-argument-text class="form-group"></div>
                    <input type="file" counter-argument-attachments multiple>
                </div>`,
            width: 700,
            confirmButtonText: 'Enviar',
            cancelButtonText: 'Cancelar',
            showCancelButton: true,
            allowOutsideClick: false,
            didOpen() {
                quillEditor = new Quill('[counter-argument-text]', {
                    theme: 'snow'
                })
            },
            willClose() {
                counterArgument.setText(quillEditor.getSemanticHTML())
                counterArgument.setFiles($('[counter-argument-attachments]')[0].files)
            },
        }).then(res => {
            if (res.isConfirmed) {
                if (!quillEditor.getText().trim()) {
                    Swal.fire({
                        title: 'Contrarrazão não enviada',
                        text: 'Digite o texto da sua contrarrazão',
                        icon: 'warning',
                    })
                    return
                }

                counterArgument.send(registration)
            }
        })
    })

    $('[btn-view-counter-argument]').on('click', function (event) {
        const text = event.currentTarget.dataset.text
        counterArgument.view(text)
    })

    $('[edit-counter-argument-btn]').on('click', function (event) {
        const id = event.currentTarget.dataset.id
        const text = event.currentTarget.dataset.text

        let quillEditor

        Swal.fire({
            title: 'Editar Contrarrazão',
            html: `
                <p class="sweetalert-plain-text">Você pode editar o texto da sua contrarrazão e anexar mais arquivos</p>
                <div>
                    <div counter-argument-text class="form-group">${text}</div>
                    <input type="file" counter-argument-attachments multiple>
                </div>`,
            width: 700,
            confirmButtonText: 'Atualizar',
            cancelButtonText: 'Cancelar',
            showCancelButton: true,
            allowOutsideClick: false,
            didOpen() {
                quillEditor = new Quill('[counter-argument-text]', {
                    theme: 'snow'
                })
            },
            willClose() {
                counterArgument.setText(quillEditor.getSemanticHTML())
                counterArgument.setFiles($('[counter-argument-attachments]')[0].files)
            },
        }).then(res => {
            if (res.isConfirmed) {
                if (!quillEditor.getText().trim()) {
                    Swal.fire({
                        title: 'Sua contrarrazão não foi atualizada',
                        text: 'O texto não pode estar vazio',
                        icon: 'warning',
                    })
                    return
                }

                counterArgument.update(id)
            }
        })
    })

    $('[remove-counter-argument-file]').on('click', function (event) {
        const fileId = event.currentTarget.dataset.fileId

        Swal.fire({
            title: 'Remover arquivo da contrarrazão',
            text: 'Deseja realmente remover este arquivo da contrarrazão?',
            icon: 'warning',
            confirmButtonText: 'Remover',
            cancelButtonText: 'Cancelar',
            showCancelButton: true,
            allowOutsideClick: false,
        }).then(res => {
            if (res.isConfirmed) {
                counterArgument.removeFile(fileId)
            }
        })
    })
})
