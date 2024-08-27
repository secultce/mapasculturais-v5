<?php
use Diligence\Repositories\Diligence as DiligenceRepo;

//entityAnswer = A Resposta de uma diligência
$files = DiligenceRepo::getFilesDiligence($entityAnswer->diligence->id);

foreach ($files as $keyFiles => $file) {
    $countFile = $keyFiles + 1;
    echo '
        <p style="margin-bottom: 10px;">
            Arquivo '.$countFile.' : 
            <a href="/arquivos/privateFile/' . $file["id"] . '" target="_blank" rel="noopener noreferrer">
                ' . $file["name"] . '
            </a>
        </p>
    ';
}