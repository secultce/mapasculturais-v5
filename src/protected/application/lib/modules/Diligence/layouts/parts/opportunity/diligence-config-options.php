<?php
/**
 * @var $opportunity \MapasCulturais\Entities\Opportunity
 * @var $isEditableConfig bool
 */

if($this->isEditable()):
?>
    <div class="registration-fieldset" id="diligence-config" ng-controller="OpportunityController">
        <div>
            <h4>Configurações de Diligência</h4>
            <p class="">A diligência é um recurso para que os avaliadores peçam mais informações ou
                esclareçam dúvidas sobre um projeto ou prestação de contas.</p>
        </div>

        <p class="field-use-diligence">
            <span class="label">Usa Diligência:</span>
            <span
                class="<?= !$isEditableConfig ?: 'js-editable editable' ?> editable-click"
                data-edit="use_diligence"
                data-original-title="Usa Diligência"
                data-value="<?= $opportunity->getMetadata('use_diligence') ?>"
            >
                <?= $opportunity->use_diligence ?>
            </span>
        </p>

        <?php if($opportunity->evaluationMethodConfiguration->type == 'documentary'): ?>
            <div class="field-use-multiple-diligence" style="display:none">
                <p>
                    <span class="label">Usa Diligência Múltipla:</span>
                    <span
                        class="<?= !$isEditableConfig ?: 'js-editable editable' ?>  editable-click"
                        data-edit="use_multiple_diligence"
                        data-original-title="Usa Diligência Múltipla"
                        data-value="<?= $opportunity->getMetadata('use_multiple_diligence') ?>"
                    >
                        <?= $opportunity->use_multiple_diligence ?>
                    </span>
                </p>
                <p>
                    <strong>Sim: </strong>O avaliador abre <strong>mais de uma</strong> diligência e recebe respostas individuais do proponente para cada uma.
                    <br>
                    <strong>Não: </strong>O avaliador abre <strong>apenas uma</strong> diligência e recebe somente uma resposta do proponente.
                </p>
            </div>
        <?php endif; ?>

        <div class="field-diligence-days" style="display:none">
            <p>
                <span class="label">Dias para resposta da diligência:</span>
                <span
                    class="<?= !$isEditableConfig ?: 'js-editable editable' ?>"
                    data-edit="diligence_days"
                    data-original-title="Público presente"
                    data-emptytext="Selecione"
                >
                    <?php echo $opportunity->diligence_days; ?>
                </span>
            </p>
            <p>Informe o total de dias que o proponente terá para dar uma resposta a diligência enviada para ele.</p>
            <p>
                <span class="label">Tipo de dia para resposta da diligência:</span>
                <span
                    class="<?= !$isEditableConfig ?: 'js-editable editable' ?>"
                    data-edit="type_day_response_diligence"
                    data-value="<?= $opportunity->getMetadata('type_day_response_diligence') ?? 'Úteis' ?>"
                    data-emptytext="Selecione"
                >
                    <?php echo $opportunity->type_day_response_diligence; ?>
                </span>
            </p>
        </div>
    </div>
<?php endif; ?>
