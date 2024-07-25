<?php

$enableBtn = false;

foreach ($reg->opportunity->getMetadata() as $key => $value) {
    if ($key == 'use_multiple_diligence' && $value == 'Sim') {
        $enableBtn = true;
    }
}

$response_template = '
    <div id="file-{{id}}" style="background-color: #F5F5F5; padding: 15px; border-radius: 5px; margin-bottom: 20px; position: relative;">
        <i class="fas fa-download" style="margin-right: 10px;"></i>
        <a href="{{url}}" target="_blank" rel="noopener noreferrer">
            relatorio_financeiro.pdf
        </a>
        <div style="position: absolute; top: 4px; right: 5px; background-color: #E0E0E0; border-radius: 3px;">
            <a data-href="{{deleteUrl}}" data-target="#file-{{id}}" class="icon icon-close hltip js-remove-item" data-hltip-classes="hltip-ajuda" title="Excluir arquivo">
            </a>
        </div>
    </div>
';

?>

<div>
    <p>
        <hr>
    </p>
</div>

<?php if ($enableBtn) : ?>
    <div style="display: flex; justify-content: center; margin: 10px 0;">
        <a class="btn btn-default send js-open-editbox hltip" data-target="#import-financial-report" title="Importar Relatório Financeiro">
            Importar Relatório Financeiro
        </a>
        <div id="import-financial-report" class="js-editbox mc-bottom" title="Importar Relatório Financeiro" data-submit-label="Importar">
            <?php
            $this->ajaxUploader(
                $reg,
                'financial-report-accountability',
                'append',
                '.import-financial-report',
                $response_template,
                $response_transform = '',
                $add_description_input = false,
                $humanCrop = false,
                $file_types = '.pdf'
            );
            ?>
        </div>
    </div>

    <p style="text-align: center">
        <a href="<?= $app->createUrl('tado', 'emitir/' . $reg->id); ?>" class="btn btn-primary" title="Gera o relatório TADO">
            Finalizar e emitir TADO
        </a>
    </p>
<?php endif; ?>
