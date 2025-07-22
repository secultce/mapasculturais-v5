<?php

use Diligence\Repositories\Diligence as DiligenceRepo;

// Mostra na view os arquivos importados e a quantidade
$financialReportsAccountability = DiligenceRepo::getFinancialReportsAccountability($entity->id);

// Se showDelBtn tiver vazio não mostra icone para excluir o arquivo
if ($financialReportsAccountability) {
    foreach ($financialReportsAccountability as $financialReportAccountability) {
        $file_id = $financialReportAccountability->id;
        echo '
            <div class="financial-report-wrapper" id="financial-report-wrapper">
                <i class="fas fa-download" style="margin-right: 10px;"></i>
                <a href="/arquivos/privateFile/' . $file_id . '" target="_blank" rel="noopener noreferrer">
                    ' . $financialReportAccountability->name . '
                </a>
                ' . sprintf($showDelBtn, $file_id) . '
            </div>
        ';
    }
}
