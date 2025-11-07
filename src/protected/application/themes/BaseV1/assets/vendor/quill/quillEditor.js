var QuillEditor = (function () {

    // Configuração fixa do toolbar
    const toolbarOptions = [
        ['bold', 'italic', 'underline', 'strike', 'link', 'image', 'blockquote'],
        [{ 'header': 1 }, { 'header': 2 }, { 'list': 'ordered' }, { 'list': 'bullet' }, { 'list': 'check' }],
        [{ 'indent': '-1' }, { 'indent': '+1' }, { 'size': ['small', false, 'large', 'huge'] }],
        [{ 'header': [1, 2, 3, 4, 5, 6, false] }, { 'color': [] }, { 'background': [] }],
        [{ 'font': [] }, { 'align': [] }],
        ['clean']
    ];

    /**
     * Abre um SweetAlert2 com editor Quill integrado
     * @param {Object} options - Configurações do editor
     * @param {string} options.title - Título do popup
     * @param {string} [options.placeholder] - Placeholder do editor
     * @param {string} [options.initialHtml] - HTML inicial (opcional)
     * @param {string} [options.entityId] - ID da entidade (opcional, capturado de input)
     * @returns {Promise} - Resolvido com { conteudo: string, entityId: string }
     */
    function openEditor(options = {}) {
        const {
            title = 'Editor de Texto Rico',
            placeholder = 'Digite seu texto aqui...',
            initialHtml = '',
            entityId = null,
            selectorId = 'quill-editor'
        } = options;

        return Swal.fire({
            title: title,
            html: `
                <div id="${selectorId}" style="min-height: 200px;"></div>
                <input type="hidden" id="entity-id" value="${entityId || ''}">
            `,
            width: '800px',
            showCancelButton: true,
            confirmButtonText: 'Salvar',
            cancelButtonText: 'Cancelar',
            focusConfirm: false,
            didOpen: () => {
                const container = document.querySelector('#'+selectorId);
                if (!container) return;

                // Inicializa o Quill
                const quill = new Quill(container, {
                    theme: 'snow',
                    placeholder: placeholder,
                    modules: {
                        toolbar: toolbarOptions
                    }
                });

                // Se houver HTML inicial, insere
                if (initialHtml) {
                    quill.root.innerHTML = initialHtml;
                }

                // Armazena a instância do Quill no DOM para acesso no preConfirm
                container.quillInstance = quill;
            },
            preConfirm: () => {
                const editorContainer = document.querySelector('#'+selectorId);

                if (!editorContainer || !editorContainer.quillInstance) {
                    Swal.showValidationMessage('Erro ao carregar o editor.');
                    setTimeout(() => {
                        Swal.resetValidationMessage();
                    }, 2000);
                    return false;
                }

                const quill = editorContainer.quillInstance;
                const conteudo = quill.root.innerHTML.trim();

                if (!conteudo || conteudo === '<p><br></p>') {
                    Swal.showValidationMessage('O texto não pode estar vazio!');
                    // Remove a mensagem após 3 segundos
                    setTimeout(() => {
                        Swal.resetValidationMessage();
                    }, 3000);
                    return false;
                }

                return conteudo;
            }
        });
    }

    return {
        open: openEditor
    };

})();