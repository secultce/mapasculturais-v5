<?php
namespace Diligence\Controllers;

use DateTime;
use Carbon\Carbon;
use \MapasCulturais\App;
use MapasCulturais\Entity;
use Diligence\Entities\Tado as EntityTado;

class Refo extends \MapasCulturais\Controller
{
    use \Diligence\Traits\DiligenceSingle;

    function GET_report()
    {
        $app = App::i();
        $mpdf = self::mpdfConfig();
        $content = $app->view->fetch('refo/report-finance');
        $mpdf->SetTitle('Secult/CE - Relatório Financeiro');
        $stylesheet = file_get_contents(MODULES_PATH . 'Diligence/assets/css/diligence/multi.css');
        
        $footerPage = $app->view->fetch('tado/footer-pdf');
        // Adicione o CSS ao mPDF

        $mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);

        $mpdf->WriteHTML(ob_get_clean());
        $mpdf->WriteHTML($content);
        $mpdf->SetHTMLFooter($footerPage);
        $mpdf->Output();

    //   dump($mpdf);
    }
}