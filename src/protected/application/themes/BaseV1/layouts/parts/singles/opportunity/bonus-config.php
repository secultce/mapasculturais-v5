<?php

use MapasCulturais\i;

?>

<div class="bonus-config">
    <h4><?= i::_e("Bonificações"); ?></h4>
    <p><?= i::_e("Configure abaixo os campos de bonificação e a quantidade de pontos que será adicionado na nota final."); ?></p>

    <div style="margin-top: 10px;">
        <section ng-if="data.enableConfigBonusFields">
            <table>
                <tr>
                    <th class="bonus-field"><?php i::_e('Campo') ?></th>
                    <th class="bonus-value"><?php i::_e('Valor') ?></th>
                    <th>
                        <button ng-click="addBonusField()" class="btn btn-default add" title="<?php i::_e('Adicionar campo de bonificação') ?>"></button>
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
            </table>
            <div style="margin-bottom: 20px;">
                <span class="label">Bonificação (em pontos):</span>
                <input ng-model="data.bonusAmount" ng-change="saveConfigBonusFields()" type="number" step="0.5" value="1" min="0.1" placeholder="1" class="bonus-amount edit">
                <p class="registration-help">
                    <?php i::_e("Para cada campo que o proponente se enquadrar, ele receberá essa pontuação na nota final."); ?>
                </p>
            </div>
        </section>

        <button ng-click="configBonusFields()" ng-if="!data.enableConfigBonusFields" id="enableConfigBonusFieldsBtn" class="btn btn-default add">
            <?php i::_e('Habilitar bonificações') ?>
        </button>
        <button ng-click="configBonusFields()" ng-if="data.enableConfigBonusFields" id="disableConfigBonusFieldsBtn" class="btn btn-danger delete">
            <?php i::_e('Desabilitar bonificações') ?>
        </button>
    </div>
</div>
