<?php
use MapasCulturais\i;

/**
 * @var \MapasCulturais\App $app
 * @var \MapasCulturais\Entities\Opportunity $entity
 */
?>

<div style="display:inline;float:right;margin-left: 1rem;">
    <span class="alert" style="display: none" id="feedback-import-message"></span>
    <span style="display: none" id="import-loading">
        Importando...
        <img src="/assets/img/spinner.gif" alt="Spinner">
    </span>
</div>

<span class="btn-group">
    <button class="btn btn-primary alignright hltip" title="<?php i::esc_attr_e('importar como rascunho: Utilize esta opção se for necessária interação com os agentes inscritos. A inscrição deverá ser reenviada para esta fase.') ?>" style="margin-left:1em;" data-href="<?= $app->createUrl('opportunity', 'importLastPhaseRegistrations', [$entity->id]) ?>" onclick="importRegistrationsAction(this)">
        <?php i::_e("como rascunho");?>
    </button>

    <button class="btn btn-primary alignright hltip" title="<?php i::esc_attr_e('importar como enviada: Utilize esta opção se NÃO for necessária interação com os agentes inscritos.') ?>" style="margin-left:1em;" data-href="<?= $app->createUrl('opportunity', 'importLastPhaseRegistrations', [$entity->id, 'sent' => 1]) ?>" onclick="importRegistrationsAction(this)">
        <?php i::_e("importar inscrições");?>
    </button>
</span>

<script>
function importRegistrationsAction(target) {
    target.parentElement.style.display = 'none';

    const loadingElement = document.getElementById('import-loading');
    const messageElement = document.getElementById('feedback-import-message');

    loadingElement.style.display = 'initial';

    fetch(target.dataset.href)
        .then(response => response.ok ? response.text : response.json())
        .then(data => {
            loadingElement.style.display = 'none';
            messageElement.style.display = 'initial';
            messageElement.classList.remove('success', 'info');

            if (data?.data) {
                messageElement.classList.add('info');
                messageElement.innerHTML = data.data;

                setTimeout(() => {
                    messageElement.style.display = 'none';
                    target.parentElement.style.display = 'initial';
                }, 3000);
            } else {
                messageElement.innerHTML = 'Inscrições importadas com sucesso!';
                messageElement.classList.add('success');
                window.location.reload();
            }
        })
        .catch(err => {
            console.error(err);
            loadingElement.style.display = 'none';
            MapasCulturais.Messages.error('Houve um erro inesperado.');
        });
}
</script>
