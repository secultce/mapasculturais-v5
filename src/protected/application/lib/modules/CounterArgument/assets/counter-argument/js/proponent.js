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
                })
            },
            error(err) {
                Swal.fire({
                    title: 'Contrarrazão não enviada',
                    text: 'Erro ao enviar contrarrazão. Tente novamente.',
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
})
