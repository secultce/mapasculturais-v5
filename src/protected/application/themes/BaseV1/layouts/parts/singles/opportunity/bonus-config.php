<?php if ($entity->evaluationMethodConfiguration->type->id === 'technical' && $this->isEditable()): ?>
    <div ng-class="{'registration-fieldset': data.isEditable}">
        <h4>
            <?php \MapasCulturais\i::_e("Configuração de Bonificação"); ?>
        </h4>

        <p>
            <span class="label">A oportunidade terá bonificações:</span>
            <span
                class="js-editable"
                data-edit="hasBonusesForRegistrations"
                data-value="<?= $entity->getMetadata('hasBonusesForRegistrations') ?>">
            </span>
        </p>
        <p>
            <span class="label">Bonificação (em pontos):</span>
            <span
                class="js-editable"
                data-edit="bonusAmount"
                data-value="<?= $entity->getMetadata('bonusAmount') ?>">
            </span>
        </p>
        <p class="registration-help">
            <?php \MapasCulturais\i::_e("Após o somatório das avaliações, esse valor será adicionado a média."); ?>
        </p>
    </div>
<?php endif; ?>
