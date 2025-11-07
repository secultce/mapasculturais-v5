// 0 - habilitado, 1 - para desabilitado
jQuery(document).ready(function($) {
    $("#registration-claim-configuration").hide();
    // if(MapasCulturais.entity && MapasCulturais.entity.object.claimDisabled == 0){
    //     $("#div-panel-counter-reason").removeClass("d-none");
    // }else {
    //     $("#div-panel-counter-reason").addClass("d-none");
    // }
    // Quando alterar a escolha dos recursos
    $('#disable-appeal-wrapper').on('change', event => {
        const isDisabled = parseInt(event.target.value);
        if (isDisabled == 0) {
            $("#div-panel-counter-reason").removeClass("d-none");
        }else{
            $("#div-panel-counter-reason").addClass("d-none");
        }
    });

    // $('#summernote').summernote();
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btnOpen-counterReason');
        if (!btn) return;

        const entityId = btn.getAttribute('data-entity-id');
        if (!entityId) return;

        // Impede múltiplos cliques
        btn.disabled = true;

        abrirContrarrazao(entityId, 'quill-editor').finally(() => {
            btn.disabled = false;
        });
    });
});


async function abrirContrarrazao(entityId, selectId) {
    const result = await QuillEditor.open({
        title: 'Contrarrazão',
        placeholder: 'Escreva sua contrarrazão aqui...',
        initialHtml: '', // ou carregue do backend via AJAX se quiser
        entityId: entityId,
        selectId
    });

    if (result.isConfirmed) {
        try {
            const response = await fetch('/contrarrazao/save', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    text : result.value,
                    registration : entityId
                })
            });

            if (response.ok) {
                Swal.fire('Sucesso', 'Contrarrazão salva!', 'success');
                // Opcional: recarregar página ou atualizar DOM
            } else {
                Swal.fire('Erro', 'Falha ao salvar.', 'error');
            }
        } catch (err) {
            console.error(err);
            Swal.fire('Erro', 'Erro de conexão.', 'error');
        }
    }
}

