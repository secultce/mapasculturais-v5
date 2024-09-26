<?php

use MapasCulturais\i;

?>

<?php if ($entity->evaluationMethodConfiguration->type->id === 'technical' && $this->isEditable()): ?>
    <div class="bonus-config">
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
                        <tr ng-repeat="(key,bonusFieldConfig) in data.bonusFieldsConfig track by $index" id="{{bonusFieldConfig.id}}">
                            <td class="bonus-field">
                                <select ng-model="data.bonusFields[bonusFieldConfig.id].field" ng-change="changeBonusField(bonusFieldConfig); ">
                                    <option ng-repeat="field in data.registrationFieldConfigurations" value="{{field.id}}">#{{field.id}} - {{field.title}} </option>
                                </select>
                            </td>
                            <td class="bonus-value">
                                <div ng-if="bonusFieldConfig.viewDataValues == 'bool' || bonusFieldConfig.viewDataValues == null">
                                    <select ng-model="data.bonusFields[bonusFieldConfig.id].value">
                                        <option value=""> Selecione </option>
                                        <option value="true"> Sim </option>
                                        <option value="false"> Não </option>
                                    </select>
                                </div>

                                <div class="check" ng-if="bonusFieldConfig.viewDataValues == 'checkbox'">
                                    <span ng-repeat="(key, v) in bonusFieldConfig.valuesList">
                                        <label>
                                            <input type="checkbox" ng-model="data.bonusFields[bonusFieldConfig.id].value[v]">
                                            {{v}}
                                        </label>
                                    </span>
                                </div>
                            </td>
                            <td>
                                <button ng-click="removeBonusFieldConfig(bonusFieldConfig)" class="btn btn-danger delete" title="<?php i::_e('Remover') ?>"></button>
                            </td>
                        </tr>
                    </thead>
                </table>
            </section>

            <button ng-click="configBonusFields()" ng-if="!data.enableConfigBonusFields" id="enableConfigBonusFieldsBtn" class="btn btn-default add">
                <?php i::_e('Configurar campos de bonificação') ?>
            </button>
            <button ng-click="configBonusFields()" ng-if="data.enableConfigBonusFields" id="disableConfigBonusFieldsBtn" class="btn btn-danger delete">
                <?php i::_e('Cancelar configuração de bonificação') ?>
            </button>
        </div>
    </div>
<?php endif; ?>
