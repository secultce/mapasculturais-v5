<?php

namespace Charts;

use MapasCulturais\App;

class Module extends \MapasCulturais\Module
{
    function _init()
    {
        $app = App::i();
        
        $app->view->enqueueScript('app', 'chart-js', 'js/Chart.min.js');
        $app->view->enqueueScript('app', 'chart-js-plugin', 'js/chartjs-plugin-datalabels.min.js');
        $app->view->enqueueScript('app', 'chart-main', 'js/charts-main.js', ['chart-js', 'mapasculturais']);
    }

    function register()
    {
    }
}
