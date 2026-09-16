<?php
use MapasCulturais\i;

$saveUrl = $app->createUrl('panel', 'entityMetadata');
$deleteUrl = $app->createUrl('panel', 'deleteEntityMetadata');
?>
<div
    class="entity-metadata-manager"
    data-user-id="<?php echo (int) $user->id; ?>"
    data-entity-type="<?php echo htmlspecialchars($entityType, ENT_QUOTES, 'UTF-8'); ?>"
    data-entity-id="<?php echo (int) $entity->id; ?>"
    data-save-url="<?php echo htmlspecialchars($saveUrl, ENT_QUOTES, 'UTF-8'); ?>"
    data-delete-url="<?php echo htmlspecialchars($deleteUrl, ENT_QUOTES, 'UTF-8'); ?>">

    <p class="entity-metadata-help"><?php i::_e('Edite ou exclua os valores de metadados desta entidade. As chaves não podem ser alteradas.'); ?></p>

    <div class="entity-metadata-table-wrapper">
        <table class="entity-table entity-metadata-table">
            <thead>
                <tr>
                    <td><?php i::_e('id'); ?></td>
                    <td><?php i::_e('chave'); ?></td>
                    <td><?php i::_e('definição'); ?></td>
                    <td><?php i::_e('valor'); ?></td>
                    <td><?php i::_e('ações'); ?></td>
                </tr>
            </thead>
            <tbody>
                <?php if (!$metadata): ?>
                    <tr class="entity-metadata-empty-row">
                        <td colspan="5"><em><?php i::_e('Esta entidade ainda não possui metadados.'); ?></em></td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($metadata as $metadataItem): ?>
                    <?php
                    $definition = isset($registeredMetadata[$metadataItem->key]) ? $registeredMetadata[$metadataItem->key] : null;
                    $sensitive = preg_match('/password|passwd|senha|token|secret|segredo|credential|credencial|salt|hash/i', $metadataItem->key);
                    ?>
                    <tr
                        class="js-entity-metadata-row"
                        data-meta-id="<?php echo (int) $metadataItem->id; ?>"
                        data-sensitive="<?php echo $sensitive ? '1' : '0'; ?>">
                        <td class="fit" data-label="<?php i::esc_attr_e('ID'); ?>"><?php echo (int) $metadataItem->id; ?></td>
                        <td data-label="<?php i::esc_attr_e('Chave'); ?>">
                            <code class="entity-metadata-key"><?php echo htmlspecialchars($metadataItem->key, ENT_NOQUOTES, 'UTF-8'); ?></code>
                        </td>
                        <td data-label="<?php i::esc_attr_e('Definição'); ?>">
                            <?php if ($definition): ?>
                                <span title="<?php echo htmlspecialchars((string) $definition->type, ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars((string) $definition->label, ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                            <?php else: ?>
                                <em><?php i::_e('Não registrado'); ?></em>
                            <?php endif; ?>
                        </td>
                        <td data-label="<?php i::esc_attr_e('Valor'); ?>">
                            <?php if ($sensitive): ?>
                                <input class="js-entity-metadata-value" type="password" name="metadata-replacement-<?php echo htmlspecialchars($entityType, ENT_QUOTES, 'UTF-8'); ?>-<?php echo (int) $metadataItem->id; ?>" value="" autocomplete="off" data-lpignore="true" placeholder="<?php i::esc_attr_e('Protegido — digite para substituir'); ?>" aria-label="<?php i::esc_attr_e('Novo valor protegido'); ?>">
                            <?php else: ?>
                                <textarea class="js-entity-metadata-value" rows="3" aria-label="<?php i::esc_attr_e('Valor do metadado'); ?>"><?php echo htmlspecialchars((string) $metadataItem->value, ENT_NOQUOTES, 'UTF-8'); ?></textarea>
                            <?php endif; ?>
                        </td>
                        <td class="fit entity-metadata-actions" data-label="<?php i::esc_attr_e('Ações'); ?>">
                            <button type="button" class="btn btn-primary js-save-entity-metadata"><?php i::_e('Salvar'); ?></button>
                            <button type="button" class="btn btn-default js-delete-entity-metadata"><?php i::_e('Excluir'); ?></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
