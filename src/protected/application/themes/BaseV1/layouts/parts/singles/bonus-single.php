<div ng-controller="RegistrationFieldsController" ng-init="data.canEvaluate = <?php echo $canEvaluate ? 'true' : 'false'; ?>">
    <div ng-if="data.bonusFields && data.bonusFields.length" style="background:#ffffff; border:1px solid #eceaeaff; padding:20px; margin:20px 0;">
        <h3 style="margin:0 0 20px;">Projeto se enquadra na temática exigida?</h3>
        <ul class="attachment-list">
            <li ng-repeat="field in data.bonusFields" 
                ng-if="showField(field) && field.assignmentByTheEvaluator === 'true'" 
                style="margin-bottom:25px; padding-bottom:15px; border-bottom:1px solid #eee;">

                <p style="margin:0 0 8px;">
                    * <strong>{{::field.title}}:</strong>
                </p>

                <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap;">
                    <span style="font-size:16px; color:#333; min-width:200px;">
                        <span ng-if="field.fieldType !== 'file'" ng-bind-html="printField(field, entity[field.fieldName])"></span>
                        <em ng-if="field.fieldType !== 'file' && !entity[field.fieldName]">Não preenchido</em>

                        <a ng-if="field.fieldType === 'file' && field.file" href="{{::field.file.url}}" target="_blank" rel="noopener noreferrer">
                            {{::field.file.name}}
                        </a>
                        <em ng-if="field.fieldType === 'file' && !field.file">Arquivo não enviado.</em>
                    </span>

                    <select 
                        class="bonus-select"
                        name="{{::field.id}}" 
                        data-field-id="{{::field.id}}"
                        ng-model="field.bonused"
                        ng-options="opt.value as opt.label for opt in [{label: 'Não', value: false}, {label: 'Sim', value: true}]"
                        ng-disabled="!data.canEvaluate"
                        ng-change="updateBonus(field)"
                        style="padding:6px 12px; border:1px solid #ccc; border-radius:4px; font-size:14px; background:white; cursor:pointer;">
                    </select>
                </div>
            </li>
        </ul>
    </div>
</div>
