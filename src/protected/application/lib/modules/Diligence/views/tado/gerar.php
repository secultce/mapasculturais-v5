<?php

use MapasCulturais\App;
use Diligence\Repositories\Diligence as RepoDiligence;

$app = App::i();
//Buscando o tado gerado
$tado = RepoDiligence::getTado($reg);

//INSTANCIA DO TIPO ARRAY OBJETO
$app->view->regObject = new \ArrayObject;
$app->view->regObject['reg'] = $reg;
$app->view->regObject['tado'] = $tado;

$mpdf = new \Mpdf\Mpdf([
    'tempDir' => '/tmp',
    'mode' =>
    'utf-8',
    'format' => 'A4',
    'pagenumPrefix' => 'Página ',
    'pagenumSuffix' => '  ',
    'nbpgPrefix' => ' de ',
    'nbpgSuffix' => ''
]);
ob_start();

$content = $app->view->fetch('tado/html-gerar');
$mpdf->SetTitle('Secult/CE - Relatório TADO');
$stylesheet = file_get_contents(MODULES_PATH . 'Diligence/assets/css/diligence/multi.css');

$footerPage = $app->view->fetch('tado/footer-pdf');
// Adicione o CSS ao mPDF
$mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);

$mpdf->WriteHTML(ob_get_clean());
$mpdf->WriteHTML($content);
$mpdf->SetHTMLFooter($footerPage);
$pdf = $mpdf->Output('Tado.pdf', \Mpdf\Output\Destination::DOWNLOAD);

