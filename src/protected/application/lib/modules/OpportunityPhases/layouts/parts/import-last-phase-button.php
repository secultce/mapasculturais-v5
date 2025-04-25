<?php
use MapasCulturais\i;

/**
 * @var \MapasCulturais\App $app
 * @var \MapasCulturais\Entities\Opportunity $entity
 */
?>

<button class="btn btn-primary alignright hltip" title="<?php i::esc_attr_e('importar como rascunho: Utilize esta opção se for necessária interação com os agentes inscritos. A inscrição deverá ser reenviada para esta fase.') ?>" style="margin-left:1em;" data-href="<?= $app->createUrl('opportunity', 'importLastPhaseRegistrations', [$entity->id]) ?>" onclick="importRegistrationsAction(this)">
    <?php i::_e("como rascunho");?>
</button>

<button class="btn btn-primary alignright hltip" title="<?php i::esc_attr_e('importar como enviada: Utilize esta opção se NÃO for necessária interação com os agentes inscritos.') ?>" style="margin-left:1em;" data-href="<?= $app->createUrl('opportunity', 'importLastPhaseRegistrations', [$entity->id, 'sent' => 1]) ?>" onclick="importRegistrationsAction(this)">
    <?php i::_e("importar inscrições");?>
</button>

<script>
function importRegistrationsAction(target) {
    target.style.pointerEvents = 'none';
    target.disabled = 'true';
    target.classList.add('disabled');

    fetch(target.dataset.href)
        .then(response => response.ok ? response.text : response.json())
        .then(data => {
            if (data?.data) {
                MapasCulturais.Messages.help(data.data);
            } else {
                window.location.reload();
            }
        })
        .catch(err => {
            console.error(err);
            MapasCulturais.Messages.error('Houve um erro inesperado.');
        });
}
</script>
