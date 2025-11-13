// 0 - habilitado, 1 - para desabilitado
jQuery(document).ready(function($) {
    console.log('Reason')
    $("#registration-claim-configuration").hide();
    if(MapasCulturais.entity && MapasCulturais.entity.object.claimDisabled == 0){
        $("#div-panel-counter-reason").removeClass("d-none");
    }else {
        $("#div-panel-counter-reason").addClass("d-none");
    }
    // Quando alterar a escolha dos recursos
    $('#disable-appeal-wrapper').on('change', event => {
        const isDisabled = parseInt(event.target.value);
        if (isDisabled == 0) {
            $("#div-panel-counter-reason").removeClass("d-none");
        }else{
            $("#div-panel-counter-reason").addClass("d-none");
        }
    });

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btnOpen-counterReason');
        if (!btn) return;

        const entityId = btn.getAttribute('data-entity-id-cr');
        if (!entityId) return;

        btn.disabled = true;

        abrirContrarrazao(entityId, btn, 'quill-editor').finally(() => {
            btn.disabled = false;
        });
    });
});


async function abrirContrarrazao(entityId, buttonElement, selectId) {
    const result = await QuillEditor.open({
        title: 'Contrarrazão',
        placeholder: 'Escreva sua contrarrazão aqui...',
        entityId: entityId,
        selectId: selectId,
        triggerButton: buttonElement  // NOVO: passa o botão
    });

    if (result.isConfirmed) {
        const { conteudo } = result.value;

        try {
            const response = await fetch('/contrarrazao/save', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    text: conteudo,
                    registration: entityId
                })
            });

            if (response.ok) {
                McMessages.success('Sucesso', 'Contrarrazão salva!', 3000 );
                window.location.reload();
            } else {
                McMessages.error('Erro', 'Falha ao salvar.', 3000);
            }
        } catch (err) {
            McMessages.error('Erro', 'Erro de conexão.', 3000);
        }
    }
}

