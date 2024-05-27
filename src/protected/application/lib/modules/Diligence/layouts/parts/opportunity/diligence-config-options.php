<?php
/**
 * @var $opportunity \MapasCulturais\Entities\Opportunity
 */

if($this->isEditable()):
?>
    <div class="registration-fieldset" ng-controller="OpportunityController">
        <h4>Configurações de Diligência</h4>
        <p class="field-use-diligence">
            <span class="label">Usa Diligência:</span>
            <span
                class="js-editable <?= $opportunity->publishedOpinions || $opportunity->publishedRegistrations ?: 'editable' ?> editable-click"
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
        </p>
        <?php if($opportunity->evaluationMethodConfiguration->type == 'documentary'): ?>
            <p class="field-use-multiple-diligence" style="display:none">
                <span class="label">Usa Diligência Múltipla:</span>
                <span
                    class="js-editable editable editable-click"
                    data-edit="use_multiple_diligence"
                    data-original-title="Usa Diligência Múltipla"
                    data-value="<?= $opportunity->getMetadata('use_multiple_diligence') ?>"
                >
                    <?= $opportunity->use_multiple_diligence ?>
                </span>
            </p>
            <p class="field-use-multiple-diligence registration-help ng-scope" style="display: none">
                <strong>Diligência Múltipla</strong> permite que o avaliador abra quantas diligências forem necessárias
                e o proponente responda individualmente. Mas apenas uma de cada vez. <!-- Verificar se pode haver várias diligências em paralelo -->
            </p>
        <?php endif; ?>

        <br>

        <p class="field-diligence-days" style="display:none">
            <span class="label">Dias úteis para resposta da diligência:</span>
            <span class="js-editable" data-edit="diligence_days" data-original-title="Público presente" data-emptytext="Selecione">
                <?php echo $opportunity->diligence_days; ?>
            </span>
        </p>
        <p class="field-diligence-days registration-help ng-scope" style="display: none">
            Informe o total de dias úteis que o proponente terá para dá uma resposta
            a diligência enviada para ele.
        </p>
    </div>


    <script>
        <?php if($opportunity->use_diligence == 'Sim'): ?>
            $('p.field-use-multiple-diligence').show();
            $('p.field-diligence-days').show();
        <?php endif; ?>
        $('p.field-use-diligence').on('change', function(ev) {
            console.log(ev)
            if(ev.target.value === 'Sim') {
                $('p.field-use-multiple-diligence').show();
                $('p.field-diligence-days').show();
            } else if(ev.target.value === 'Não') {
                $('p.field-use-multiple-diligence').hide();
                $('p.field-diligence-days').hide();
            }
        });
    </script>

<?php endif; ?>
