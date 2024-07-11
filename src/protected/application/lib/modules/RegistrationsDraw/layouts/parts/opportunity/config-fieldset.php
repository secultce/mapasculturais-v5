<?php
/**
 * @var \MapasCulturais\Entities\Opportunity $opportunity
 */
?>
<?php $this->applyTemplateHook('draw-config-fieldset', 'before'); ?>
<div class="registration-fieldset ng-scope">
    <h4>Sorteio</h4>
    <p class="registration-help">Habilite ou desabilite sorteios para esta oportunidade.</p>
    <label class="label" for="use-registrations-draw">Habilitar sorteio:</label>
    <span
        id="use-registrations-draw"
        class="js-editable editable editable-click"
        data-edit="useRegistrationsDraw"
        data-original-title="Usa Diligência"
        data-value="<?= $opportunity->getMetadata('useRegistrationsDraw') ?>"
    >
                <?= $opportunity->getMetadata('useRegistrationsDraw') ?>
    </span>
</div>
<?php $this->applyTemplateHook('draw-config-fieldset', 'after'); ?>
