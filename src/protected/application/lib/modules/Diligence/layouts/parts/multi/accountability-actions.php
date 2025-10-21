<?php

use MapasCulturais\Utils;

$enableBtn = false;

if (Utils::isOpportunityForAccountability($reg->opportunity)) {
    $enableBtn = true;
}

$response_template = '
    <div class="financial-report-wrapper" id="financial-report-wrapper">
        <i class="fas fa-download" style="margin-right: 10px;"></i>
        <a href="{{url}}" target="_blank" rel="noopener noreferrer">
            {{description}}
        </a>
        <div class="delete-financial-report-btn">
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
    //Visualização para o avaliador
    if ($enableBtn && $isEvaluation) {
        $this->part('multi/multi-select', ['reg' => $reg, 'tado' => $tado]);

    }
    //Visualização para avaliador e o proponente
    if( !is_null($tado) && ($tado->status == 1) ): ?>
    <p style="text-align: center;width: 100%; margin-bottom: 15px" id="">
        <a href="<?= $app->createUrl('tado', 'gerar/' . $reg->id); ?>"
            target="_blank"
            class="btn btn-primary"
            title="Visualizar o TADO"
            style="display: block;"
        >
            Visualizar TADO
        </a>
    </p>
    
<?php endif; ?>

<div id="import-financial-report" class="js-editbox mc-bottom" title="Enviar arquivo para o proponente" data-submit-label="Enviar">
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
        $file_types = '.pdf, .doc, .docx'
    );
    ?>
</div>
