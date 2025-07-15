<?php
if( is_null($tado) || ($tado->status == 0) ) : ?>
<div style="width: 100%;
    text-align: center;
    margin-top: 10px;" class="form-group-multi">
    <label style="font-weight: 500">Selecione o status da prestação de contas</label>
    <select name="" id="situacion-refo-multi" class="form-control-multi">
        <option value="all" disabled selected>-- Selecione --</option>
        <option value="under_analysis">Em Análise</option>
        <option value="approved">Regular</option>
        <option value="partially">Regular com ressalva</option>
        <option value="disapproved">Irregular</option>
    </select>
</div>

<div style="text-align: center;width: 100%;" id="multi-div-btn-status">
    <p style="text-align: center;width: 100%; margin-bottom: 15px" class="multi-itens-select">
        <a href="<?= $app->createUrl('refo', 'report/' . $reg->id); ?>" target="_blank" class="btn btn-default" title="Gera o relatório para o financeiro analisar" style="display: block;">
            <i class="fas fa-solid fa-file-pdf"></i>
            Gerar relatório para Financeiro
        </a>
    </p>

    <p style="text-align: center; width: 100%; margin-bottom: 15px" class="multi-itens-select">
        <a class="btn btn-default send js-open-editbox hltip" data-target="#import-financial-report" title="Enviar arquivo para o proponente" style="display: block;">
            Enviar arquivo para o proponente
        </a>
    </p>

    <p style="text-align: center;width: 100%; margin-bottom: 15px" id="p-btn-tado">
        <a href="<?= $app->createUrl('tado', 'emitir/' . $reg->id); ?>"
           id="btn-generate-tado"
           target="_blank" class="btn btn-primary" title="Gera o relatório TADO" style="display: block;">
            Finalizar e emitir TADO
        </a>
    </p>
</div>
<?php endif;
