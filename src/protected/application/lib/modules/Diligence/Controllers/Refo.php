<?php
namespace Diligence\Controllers;

use DateTime;
use Carbon\Carbon;
use \MapasCulturais\App;
use MapasCulturais\Entity;
use Diligence\Repositories\Diligence;

class Refo extends \MapasCulturais\Controller
{
    use \Diligence\Traits\DiligenceSingle;

    function GET_report()
    {
        $app = App::i();
        $dili = Diligence::getDiligenceAnswer($this->data['id']);
        //INSTANCIA DO TIPO ARRAY OBJETO
        $app->view->regObject = new \ArrayObject;
        $app->view->regObject['diligence'] = $dili;
        $mpdf = self::mpdfConfig();
        self::mdfBodyMulti($mpdf,
        'refo/report-finance', 
        'Secult/CE - Relatório Financeiro',
        'Diligence/assets/css/diligence/multi.css');
    }
}