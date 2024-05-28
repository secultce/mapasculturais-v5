<?php

use MapasCulturais\App;

$app = App::i();
// $mpdf = new \Mpdf\Mpdf();

$mpdf = new \Mpdf\Mpdf([
    'tempDir' => dirname(__DIR__) . '/vendor/mpdf/mpdf/tmp', 'mode' =>
    'utf-8', 'format' => 'A4',
    'pagenumPrefix' => 'Página ',
    'pagenumSuffix' => '  ',
    'nbpgPrefix' => ' de ',
    'nbpgSuffix' => ''
]);


ob_start();
//INSTANCIA DO TIPO ARRAY OBJETO
$app->view->regObject = new \ArrayObject;
$app->view->regObject['reg'] = $reg;

$content = $app->view->fetch('tado/html-gerar');
$footerPage = $app->view->fetch('tado/footer-pdf');
// dump($content);die;
$mpdf->SetTitle('Secult/CE - Relatório TADO');
// dump(MODULES_PATH);
// die;
$stylesheet = file_get_contents(MODULES_PATH . 'Diligence/assets/css/diligence/multi.css');
// Adicione o CSS ao mPDF
$mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);

$mpdf->WriteHTML(ob_get_clean());
$mpdf->WriteHTML($content);
$mpdf->SetHTMLFooter($footerPage);
$mpdf->Output();
exit;
