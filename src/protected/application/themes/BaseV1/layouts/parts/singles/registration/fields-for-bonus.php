<?php

use MapasCulturais\i;

?>

<div ng-controller="RegistrationFieldsController">
    <div ng-if="data.enableBonusFields && data.bonusFields.length" class="registration-fieldset">
        <h4>Bonificação</h4>

        <ul class="attachment-list">
            <li ng-repeat="field in data.bonusFields" ng-if="showField(field)" id="field_{{::field.id}}" class="ng-scope js-field attachment-list-item registration-view-mode">
                <div ng-if="field.fieldType !== 'file'" style="display: flex;">
                    <div style="margin-right: auto;">
                        <label>{{field.required ? '*' : ''}} {{field.title}}: </label>
                        <span ng-if="entity[field.fieldName]" ng-bind-html="printField(field, entity[field.fieldName])"></span>
                        <span ng-if="!entity[field.fieldName]"><em><?php i::_e("Campo não informado."); ?></em></span>
                    </div>

                    <button data-field-id="{{::field.id}}" class="btn btn-primary assign-bonus-btn" ng-attr-title="{{alreadyBonusedField(field) ? 'Bonificação já atribuída' : ''}}" ng-class="alreadyBonusedField(field) ? 'disabled' : ''">
                        <?php i::_e('Atribuir bonificação'); ?>
                    </button>
                </div>

                <div ng-if="field.fieldType === 'file'">
                    <label>{{::field.required ? '*' : ''}} {{::field.title}}: </label>
                    <a ng-if="field.file" class="attachment-title" href="{{::field.file.url}}" target="_blank" rel='noopener noreferrer'>{{::field.file.name}}</a>
                    <span ng-if="!field.file"><em><?php i::_e("Arquivo não enviado."); ?></em></span>
                </div>
            </li>
        </ul>
    </div>
</div>
