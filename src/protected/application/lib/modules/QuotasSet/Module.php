<?php

namespace QuotasSet;

use MapasCulturais\App;
use MapasCulturais\Controller;
use MapasCulturais\Theme;

class Module extends \MapasCulturais\Module
{

    public function _init(): void
    {
        $app = App::getInstance();

        $app->hook('template(panel.<<*>>.nav.panel.accountability):after', function () {
            /** @var Theme $this */
            $this->part('quotas-set.nav.panel');
        });

        $app->hook('GET(panel.cotas-e-politicas)', function () {
            /** @var Controller $this */
            $this->requireAuthentication();
            $this->render('panel-quotas-set');
        });
    }

    public function register(): void
    {
        // TODO: Implement register() method.
    }
}
