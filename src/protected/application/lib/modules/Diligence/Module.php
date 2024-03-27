<?php

namespace Diligence;

use MapasCulturais\App,
    MapasCulturais\i,
    MapasCulturais\Entities,
    MapasCulturais\Definitions,
    MapasCulturais\Exceptions;

class Module extends \MapasCulturais\Module {

    function _init () {
        $app = App::i();
       
        $app->hook('template(registration.view.content-diligence):begin', function () use ($app) {
          $app->view->enqueueStyle('app', 'diligence', 'css/diligence/style.css');
          
         $this->part('diligence/tabs-parent');
        });
    }
    
    function register () {
        $app = App::i();
        $app->registerController('diligence', Controllers\Controller::class);
    }
}