<?php
/**
 * @var $opportunity \MapasCulturais\Entities\Opportunity
 */

if($this->isEditable()):
?>
    <div class="registration-fieldset" ng-controller="OpportunityController">
        <h4>Configurações de Diligência</h4>
        <p>
            <span class="label">Usa Diligência:</span>
            <span
                class="js-editable editable editable-click editable-unsaved"
                data-edit="use_diligence"
                data-original-title="Usa Diligência"
                data-value="<?= $opportunity->getMetadata('use_diligence') ?>"
            >
                <?= $opportunity->use_diligence ?>
            </span>
        </p>
        <p class="registration-help ng-scope">
            Diligência é uma forma de atendimento ao proponente, que permite que o avaliador solicite uma
            correção ou novos dados sobre o projeto sem alteração na ficha de inscrição.
            <br>
            <strong>Diligência Simples</strong> só permite que o avaliador faça uma abertura de diligência
            e somente uma resposta do proponente.
            <br>
            <strong>Diligência Múltipla</strong> permite que o avaliador abra quantas diligências forem necessárias
            e o proponente responda individualmente. Mas apenas uma de cada vez. <!-- Verificar se pode haver várias diligências em paralelo -->
        </p>

        <br>

        <p>
            <span class="label">Dias úteis para resposta da diligência:</span>
            <span class="js-editable" data-edit="diligence_days" data-original-title="Público presente" data-emptytext="Selecione">
                <?php echo $opportunity->diligence_days; ?>
            </span>
        </p>
        <p class="registration-help ng-scope">
            Informe o total de dias úteis que o proponente terá para dá uma resposta
            a diligência enviada para ele.
        </p>
    </div>

<?php endif; ?>
