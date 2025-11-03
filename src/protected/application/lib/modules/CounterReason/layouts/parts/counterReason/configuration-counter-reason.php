<div>
    <div class="panel panel-default " id="div-panel-counter-reason">
        <div class="panel-heading"> <h4 class="title-h4">Contrarrazão</h4></div>
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
                                  data-value="<?php echo $opp->getMetadata('counterReason_date_initial'); ?>"
                                  data-edit="counterReason_date_initial"
                                  data-viewformat="dd/mm/yyyy"
                                  data-showbuttons="false"
                                  data-original-title="Data Inicial"
                                  data-emptytext="Início do Contrarrazão">
                                   <?php echo $opp->getMetadata('counterReason_date_initial'); ?>
                                </span>
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <span class="label">Hora Inicial:</span>
                            <span class="js-editable"
                                  data-type="time"
                                  data-value="<?php echo $opp->getMetadata('counterReason_time_initial'); ?>"
                                  data-edit="counterReason_time_initial"
                                  data-viewformat="HH:mm"
                                  data-showbuttons="false"
                                  data-original-title="Hora Inicial"
                                  data-emptytext="Horário do início da Contrarrazão">
                                  <?php echo $opp->getMetadata('counterReason_time_initial'); ?>
                                </span>
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <span class="label">Data Final:</span>
                            <span class="js-editable"
                                  data-type="date"
                                  data-value="<?php echo $opp->getMetadata('counterReason_date_end'); ?>"
                                  data-edit="counterReason_date_end"
                                  data-viewformat="dd/mm/yyyy"
                                  data-showbuttons="false"
                                  data-original-title="Data Final"
                                  data-emptytext="Fim do Contrarrazão">
                                   <?php echo $opp->getMetadata('counterReason_date_end'); ?>
                                </span>
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <span class="label">Hora Final:</span>
                            <span class="js-editable"
                                  data-type="time"
                                  data-value="<?php echo $opp->getMetadata('counterReason_time_end'); ?>"
                                  data-edit="counterReason_time_end"
                                  data-viewformat="HH:mm"
                                  data-showbuttons="false"
                                  data-original-title="Hora Final"
                                  data-emptytext="Horário do fim da Contrarrazão">
                                   <?php echo $opp->getMetadata('counterReason_time_end'); ?>
                                </span>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
