var QuillEditor = (function () {

    const toolbarOptions = [
        ['bold', 'italic', 'underline', 'strike', 'link', 'image', 'blockquote'],
        [{ 'header': 1 }, { 'header': 2 }, { 'list': 'ordered' }, { 'list': 'bullet' }, { 'list': 'check' }],
        [{ 'indent': '-1' }, { 'indent': '+1' }, { 'size': ['small', false, 'large', 'huge'] }],
        [{ 'header': [1, 2, 3, 4, 5, 6, false] }, { 'color': [] }, { 'background': [] }],
        [{ 'font': [] }, { 'align': [] }],
        ['clean']
    ];

    /**
     * Configuração do SweetAlert com Quill + input file
     * @param {string} editorId - ID do textarea oculto
     * @param {string} fileId - ID do input file
     * @param {string} initialText - Texto inicial (opcional)
     * @returns {Object} - Configuração do Swal
     */
    function getConfig(editorId, fileId, initialText = '') {
        return {
            title: "Escrever Contrarrazão",
            html: `
                <div style="display: grid; gap: 10px;">
                    <label>Escreva sua contrarrazão:</label>
                    <textarea id="${editorId}" style="display: none;">${initialText}</textarea>
                    <div id="quill-container" style="min-height: 200px;"></div>
                    
                    <label>Anexar arquivos (máx. 2):</label>
                    <input id="${fileId}" type="file" multiple max="2" class="swal2-file" style="width: 100%;">
                </div>
            `,
            width: '900px',
            padding: '2em',
            showCancelButton: true,
            confirmButtonText: 'Enviar',
            cancelButtonText: 'Cancelar',
            showLoaderOnConfirm: true,
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-secondary'
            },
            didOpen: () => {
                const textarea = document.getElementById(editorId);
                const container = document.getElementById('quill-container');

                // Inicializa Quill
                const quill = new Quill(container, {
                    theme: 'snow',
                    placeholder: 'Escreva sua contrarrazão aqui...',
                    modules: { toolbar: toolbarOptions }
                });

                // Carrega texto inicial
                if (initialText) {
                    quill.root.innerHTML = initialText;
                }

                // Atualiza textarea oculto sempre que mudar
                quill.on('text-change', () => {
                    textarea.value = quill.root.innerHTML;
                });
            },
            preConfirm: () => {
                const textarea = document.getElementById(editorId);
                const fileInput = document.getElementById(fileId);

                const conteudo = textarea.value.trim();

                if (!conteudo || conteudo === '<p><br></p>') {
                    Swal.showValidationMessage('O texto da contrarrazão é obrigatório!');
                    return false;
                }

                // Validação: máximo 2 arquivos
                const files = fileInput.files;
                if (files.length > 2) {
                    Swal.showValidationMessage('Máximo de 2 arquivos permitidos!');
                    return false;
                }

                return [conteudo, files]; // EXATAMENTE como no Froala
            },
            allowOutsideClick: false
        };
    }

    return { getConfig };
})();