<div style="width: 100%;
    text-align: center;
    margin-top: 10px;" class="form-group-multi">
    <label style="font-weight: 500">Selecione o status da prestação de contas</label>
    <select name="" id="situacion-refo-multi" class="form-control-multi">
        <option value="">-- Selecione --</option>
        <option value="approved">Aprovada</option>
        <option value="partially">Parcialmente aprovada</option>
        <option value="disapproved">Reprovada</option>
    </select>

</div>
<div style="text-align: center;width: 100%;" id="multi-div-btn-status" >
    <p style="text-align: center;width: 100%; margin-bottom: 15px" class="multi-itens-select">
        <a href="<?= $app->createUrl('refo', 'report/' . $reg->id); ?>"
            target="_blank"
            class="btn btn-default"
            title="Gera o relatório para o financeiro analisar"
            style="display: block;"
        >
        <i class="fas fa-solid fa-file-pdf"></i>
            Gerar relatório para Financeiro
        </a>
    </p>

    <p style="text-align: center;width: 100%; margin-bottom: 15px" class="multi-itens-select">
        <a href="<?= $app->createUrl('tado', 'emitir/' . $reg->id); ?>"
            class="btn btn-default"
            title="Importa para P.C o relatorio do parecer financeiro"
            style="display: block;"
        >
        <i class="fas fa-solid fa-upload"></i>
            Importar relatório do Financeiro
        </a>
    </p>

    <p style="text-align: center;width: 100%; margin-bottom: 15px" id="p-btn-tado" >
        <a href="<?= $app->createUrl('tado', 'emitir/' . $reg->id); ?>"
            target="_blank"
            class="btn btn-primary"
            title="Gera o relatório TADO"
            style="display: block;"
        >
            Finalizar e emitir TADO
        </a>
    </p>
</div>