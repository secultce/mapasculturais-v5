<?php if ($entity->evaluationMethodConfiguration->type->id === 'technical' && $this->isEditable()): ?>
    <div ng-class="{'registration-fieldset': data.isEditable}">
        <h4>
            <?php \MapasCulturais\i::_e("Configuração de Cotas"); ?>
        </h4>

        <p>
            <span class="label">Será disponibilizado vagas para cotistas:</span>
            <span
                class="js-editable"
                data-edit="hasVacanciesForQuotaHolders"
                data-value="<?= $entity->getMetadata('hasVacanciesForQuotaHolders') ?>">
            </span>
        </p>
    </div>
<?php endif; ?>
