<?php
$app = MapasCulturais\App::i();
$canManageMetadata = ($app->user->is('saasSuperAdmin') || $app->user->is('superAdmin'))
    && $entity->canUser('modify');
?>
<?php if ($canManageMetadata): ?>
    <button
        type="button"
        class="btn btn-small btn-default js-open-entity-metadata"
        data-user-id="<?php echo (int) $entity->getOwnerUser()->id; ?>"
        data-entity-type="<?php echo htmlspecialchars($entityType, ENT_QUOTES, 'UTF-8'); ?>"
        data-entity-id="<?php echo (int) $entity->id; ?>">
        <?php MapasCulturais\i::_e('editar metadados'); ?>
    </button>
<?php endif; ?>
