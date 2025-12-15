<div class="d-none" id="counter-argument-config-wrapper">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="title-h4">Contrarrazão</h4>
        </div>
        <p class="registration-help mgt-16 mgb-16">
            Espaço para configurar se a oportunidade receberá a modalidade da contrarrazão.
        </p>
        <div class="panel-body">
            <table class="table-full">
                <tr class="table-full">
                    <td>
                        <div class="form-group">
                            <span class="label">Data Inicial:</span>
                            <span class="js-editable"
                                data-type="date"
                                data-value="<?= $opportunity->getMetadata('initialDateCounterArgument'); ?>"
                                data-edit="initialDateCounterArgument"
                                data-viewformat="dd/mm/yyyy"
                                data-showbuttons="false"
                                data-original-title="Data Inicial"
                                data-emptytext="Início da Contrarrazão">
                                <?= $opportunity->getMetadata('initialDateCounterArgument'); ?>
                            </span>
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <span class="label">Hora Inicial:</span>
                            <span class="js-editable"
                                data-type="time"
                                data-value="<?= $opportunity->getMetadata('initialTimeCounterArgument'); ?>"
                                data-edit="initialTimeCounterArgument"
                                data-viewformat="HH:mm"
                                data-showbuttons="false"
                                data-original-title="Hora Inicial"
                                data-emptytext="Horário do início da Contrarrazão">
                                <?= $opportunity->getMetadata('initialTimeCounterArgument'); ?>
                            </span>
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <span class="label">Data Final:</span>
                            <span class="js-editable"
                                data-type="date"
                                data-value="<?= $opportunity->getMetadata('finalDateCounterArgument'); ?>"
                                data-edit="finalDateCounterArgument"
                                data-viewformat="dd/mm/yyyy"
                                data-showbuttons="false"
                                data-original-title="Data Final"
                                data-emptytext="Fim da Contrarrazão">
                                <?= $opportunity->getMetadata('finalDateCounterArgument'); ?>
                            </span>
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <span class="label">Hora Final:</span>
                            <span class="js-editable"
                                data-type="time"
                                data-value="<?= $opportunity->getMetadata('finalTimeCounterArgument'); ?>"
                                data-edit="finalTimeCounterArgument"
                                data-viewformat="HH:mm"
                                data-showbuttons="false"
                                data-original-title="Hora Final"
                                data-emptytext="Horário do fim da Contrarrazão">
                                <?= $opportunity->getMetadata('finalTimeCounterArgument'); ?>
                            </span>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <hr>
</div>
