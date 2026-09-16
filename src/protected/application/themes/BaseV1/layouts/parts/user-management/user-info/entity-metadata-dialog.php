<?php
$app = MapasCulturais\App::i();
?>
<div
    id="entity-metadata-dialog"
    class="entity-modal js-dialog entity-metadata-dialog"
    title="<?php MapasCulturais\i::esc_attr_e('Metadados da entidade'); ?>"
    data-load-url="<?php echo htmlspecialchars($app->createUrl('panel', 'entityMetadata'), ENT_QUOTES, 'UTF-8'); ?>"
    style="display: none">
    <div class="js-entity-metadata-dialog-content">
        <p><?php MapasCulturais\i::_e('Selecione uma entidade para carregar seus metadados.'); ?></p>
    </div>
</div>
