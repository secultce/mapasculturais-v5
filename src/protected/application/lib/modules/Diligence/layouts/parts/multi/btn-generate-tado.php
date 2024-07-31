<?php

$enableBtn = false;

foreach ($reg->opportunity->getMetadata() as $key => $value) {
    if ($key == 'use_multiple_diligence' && $value == 'Sim') {
        $enableBtn = true;
    }
}

$response_template = '
    <div style="background-color: #F5F5F5; padding: 15px; border-radius: 5px; margin-bottom: 20px; position: relative;" id="financial-report-wrapper">
        <i class="fas fa-download" style="margin-right: 10px;"></i>
        <a href="{{url}}" target="_blank" rel="noopener noreferrer">
            relatorio_financeiro.pdf
        </a>
        <div style="position: absolute; top: 4px; right: 5px; background-color: #E0E0E0; border-radius: 3px;">
            <a delete-financial-report data-file-id="{{id}}" class="icon icon-close hltip" title="Excluir arquivo">
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

<?php
if ($enableBtn) {
    $this->part('multi/multi-select', ['reg' => $reg]);
}
?>

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
