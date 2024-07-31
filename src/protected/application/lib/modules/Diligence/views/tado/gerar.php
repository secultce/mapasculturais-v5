<?php

use MapasCulturais\App;
use Carbon\Carbon;

$reg = $app->view->regObject['reg'];
$urlOpp = App::i()->createUrl('opportunity' . $reg->opportunity->id);
$tado = $app->view->regObject['tado'];

require THEMES_PATH . 'BaseV1/layouts/headpdf.php';

$this->part('multi/report-tado',[
    'reg'=>$reg,
    'urlOpp'=>$urlOpp,
    'tado'=>$tado,
    'carbon' => new Carbon()
]);

