<?php

use MapasCulturais\i;

?>

<?php if ($entity->evaluationMethodConfiguration->type->id === 'technical' && $this->isEditable()): ?>
    <div ng-controller="TechnicalEvaluationMethodConfigurationController" class="registration-fieldset">
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

        <div style="margin-top: 10px;">
            <section ng-if="data.enableConfigBonusFields">
                <table>
                    <thead>
                        <tr>
                            <th class="bonus-field"><?php i::_e('Campo') ?></th>
                            <th class="bonus-value"><?php i::_e('Valor') ?></th>
                            <th>
                                <button ng-click="addBonusField()" class="btn btn-default add" title="<?php i::_e('Adicionar Campo de Bonificação') ?>"></button>
                            </th>
                        </tr>
                    </thead>
                </table>
            </section>

            <button ng-click="configBonusFields()" ng-if="!data.enableConfigBonusFields" id="configBonusFieldsBtn" class="btn btn-default add">
                <?php i::_e('Configurar Campos de Bonificação') ?>
            </button>
        </div>
    </div>
<?php endif; ?>
