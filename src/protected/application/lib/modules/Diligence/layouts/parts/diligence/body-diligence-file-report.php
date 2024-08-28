<?php
//Mostra na view os arquivos importados e a quantidade

//Se showDelBtn tiver vazio não mostra icone para excluir o arquivo
if ($financialReportsAccountability)
{
    foreach ($financialReportsAccountability as $keyFiles     => $financialReportAccountability)
    {
        $file_id      = $financialReportAccountability->id;
        $numberReport = ($keyFiles + 1);
        echo '
            <div class="financial-report-wrapper" id="financial-report-wrapper">
                <i class="fas fa-download" style="margin-right: 10px;"></i>
                <a href="/arquivos/privateFile/' . $file_id . '" target="_blank" rel="noopener noreferrer">
                    Relatório Financeiro ' . $numberReport . '
                </a>
                ' . sprintf($showDelBtn, $file_id) . '
            </div>
        ';
    }
}

