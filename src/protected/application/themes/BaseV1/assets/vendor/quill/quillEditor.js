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
     * @param {string} [options.html] - HTML customizado com inputs/selects (opcional)
     * @param {Function} [options.onOpen] - Callback executado após abertura do modal (opcional)
     * @returns {Promise} - Resolvido com { conteudo: string, entityId: string, customFields: object }
     */

    function validateContent(html, message = 'O texto não pode estar vazio!') {
        const cleanText = html.replace(/<[^>]*>/g, '').trim();

        if (!cleanText) {
            Swal.showValidationMessage(message);
            setTimeout(() => Swal.resetValidationMessage(), 2000);
            return false;
        }

        return true;
    }
    
    function openEditor(options = {}) {
        const {
            title = 'Editor de Texto Rico',
            placeholder = 'Digite seu texto aqui...',
            initialHtml = '',
            entityId = null,
            selectorId = 'quill-editor',
            html = '',
            showFile = true,
            onOpen = null
        } = options;

        return Swal.fire({
            title: title,
            html: `
                <div id="${selectorId}" style="min-height: 200px;"></div>
                <input type="hidden" id="entity-id" value="${entityId || ''}">
                ${html ? `<div id="custom-fields-container">${html}</div>` : ''}
                ${showFile ? `<input id="edit-recourse-file-${entityId}" type="file" multiple max="2" class="swal2-file">`: ''}
            `,
            width: '800px',
            showCancelButton: true,
            confirmButtonText: 'Salvar',
            cancelButtonText: 'Cancelar',
            focusConfirm: false,
            didOpen: () => {
                const container = document.querySelector('#' + selectorId);
                if (!container) return;

                const quill = new Quill(container, {
                    theme: 'snow',
                    placeholder: placeholder,
                    modules: { toolbar: toolbarOptions }
                });

                // HTML inicial: 1. triggerButton → 2. initialHtml
                let htmlToLoad = initialHtml;

                if (options.triggerButton && options.triggerButton.dataset.entityContextCr) {
                    htmlToLoad = options.triggerButton.dataset.entityContextCr;
                }

                if (htmlToLoad && htmlToLoad.trim() !== '') {
                    quill.root.innerHTML = htmlToLoad;
                }

                container.quillInstance = quill;

                // Executa callback customizado se fornecido
                if (onOpen && typeof onOpen === 'function') {
                    onOpen(quill, container);
                }
            },
            preConfirm: () => {
                const editorContainer = document.querySelector('#'+selectorId);
                const entityInput = document.getElementById('entity-id');

                if (!editorContainer || !editorContainer.quillInstance) {
                    Swal.showValidationMessage('Erro ao carregar o editor.');
                    return false;
                }

                const quill = editorContainer.quillInstance;
                const conteudo = quill.root.innerHTML.trim();
                const entityId = entityInput ? entityInput.value : null;

                if (!validateContent(conteudo)) return false;


                // Captura valores dos campos customizados
                const customFields = {};
                const customContainer = document.getElementById('custom-fields-container');

                if (customContainer) {
                    // Captura todos os inputs (text, hidden, radio, checkbox)
                    const inputs = customContainer.querySelectorAll('input, select, textarea','select-one');
                    inputs.forEach(input => {
                        if (input.type === 'radio' || input.type === 'checkbox' ) {
                            if (input.checked) {
                                customFields[input.name || input.id] = input.value;
                            }
                        } else {
                            customFields[input.name || input.id] = input.value;
                        }
                    });
                }

                return { conteudo, entityId, customFields };
            }
        });
    }

    return {
        open: openEditor
    };

})();