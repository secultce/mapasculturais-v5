<?php
use MapasCulturais\App;
use MapasCulturais\i;

$canControl = App::i()->user->profile->canUser('@control');
?>
<div ng-controller="RegistrationFieldsController"
     ng-init="
        data.canControl = <?= $canControl ? 'true' : 'false'; ?>;
        data.bonusB2Fields = <?= htmlspecialchars(json_encode(array_values($b2Fields)), ENT_QUOTES, 'UTF-8'); ?>
     ">
    <div ng-if="data.enableBonusFields && data.bonusFields.length" class="registration-fieldset">
        <h4>Bonificação</h4>

        <ul class="attachment-list">
            <li ng-repeat="field in data.bonusFields"
                ng-if="showField(field)"
                id="field_{{::field.id}}"
                class="ng-scope js-field attachment-list-item registration-view-mode">

                <div ng-if="field.fieldType !== 'file'" style="display: flex; gap: 10px;">
                    <div style="margin-right: auto;">
                        <label>{{field.required ? '*' : ''}} {{field.title}}: </label>
                        <span ng-if="entity[field.fieldName]" ng-bind-html="printField(field, entity[field.fieldName])"></span>
                        <span ng-if="!entity[field.fieldName]"><em><?php i::_e("Campo não informado."); ?></em></span>
                    </div>

                    <button 
                        ng-if="data.canControl"
                        data-field-id="{{::field.id}}"
                        class="btn btn-danger remove-bonus-btn"
                        ng-class="alreadyBonusedField(field) ? '' : 'disabled'"
                        title="Remover bonificação atribuída">
                        Remover bonificação
                    </button>

                    <button 
                        data-field-id="{{::field.id}}"
                        class="btn btn-primary assign-bonus-btn"
                        style="background-color: #085E55;"
                        ng-attr-title="{{alreadyBonusedField(field) ? 'Bonificação já atribuída' : ''}}"
                        ng-class="alreadyBonusedField(field) ? 'disabled' : ''">
                        <?php i::_e('Atribuir bonificação'); ?>
                    </button>
                </div>

                <div ng-if="field.fieldType === 'file'">
                    <label>{{::field.required ? '*' : ''}} {{::field.title}}: </label>
                    <a ng-if="field.file" class="attachment-title" href="{{::field.file.url}}" target="_blank" rel="noopener noreferrer">{{::field.file.name}}</a>
                    <span ng-if="!field.file"><em><?php i::_e("Arquivo não enviado."); ?></em></span>
                </div>

            </li>
        </ul>
    </div>

    <div ng-if="data.bonusB2Fields && data.bonusB2Fields.length" class="registration-fieldset">
        <h4>Bonificação B2</h4>

        <ul class="attachment-list">
            <li ng-repeat="field in data.bonusB2Fields"
                id="field_{{::field.id}}_b2"
                class="ng-scope js-field attachment-list-item registration-view-mode">

                <div style="display: flex; gap: 10px;">
                    <div style="margin-right: auto;">
                        <label>{{::field.title}}: </label>
                        <span>{{ (field.value === 'true' || field.value === true) ? 'Sim' : 'Não' }}</span>
                    </div>
                </div>

            </li>
        </ul>
    </div>

</div>
