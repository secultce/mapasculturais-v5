var EventDelegator = (function () {

    // Armazena mapeamentos: classe → { handler, editorId }
    const handlers = {};

    // Listener global único
    function initGlobalListener() {
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('[data-entity-id-cr]');
            if (!btn) return;

            // Procura handler que bata com QUALQUER classe
            let config = null;
            for (let className of btn.classList) {
                if (handlers[className]) {
                    config = handlers[className];
                    break;
                }
            }

            if (!config) return;

            const entityId = btn.getAttribute('data-entity-id-cr');

            // NOVO: Extrai TODOS os data-* como objeto extraData
            const extraData = {};
            for (let attr of btn.attributes) {
                if (attr.name.startsWith('data-')) {
                    const key = attr.name.replace('data-', ''); // data-custom-title → customTitle
                    extraData[key] = attr.value;
                }
            }

            btn.disabled = true;
            config.handler(entityId, btn, config.editorId, extraData)
                .finally(() => { btn.disabled = false; });
        });
    }

    /**
     * Registra um handler para uma classe de botão
     * @param {string} buttonClass - Classe CSS do botão (ex: '.openRecourse')
     * @param {Function} handler - Função async: (entityId, btn, editorId) => Promise
     * @param {string} [editorId='quill-editor'] - ID do container Quill
     */
    function setupButtonHandler(buttonClass, handler, editorId = 'quill-editor') {
        if (!buttonClass.startsWith('.')) buttonClass = '.' + buttonClass;
        const cleanClass = buttonClass.replace('.', '');
        handlers[cleanClass] = { handler, editorId };

        if (Object.keys(handlers).length === 1) {
            initGlobalListener();
        }
    }

    return {
        setup: setupButtonHandler
    };

})();