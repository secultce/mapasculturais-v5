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
      $mpdf = self::mpdfConfig();
      $mpdf->WriteHTML('Hello World');
      $mpdf->Output(); exit;
    //   dump($mpdf);
    }
}