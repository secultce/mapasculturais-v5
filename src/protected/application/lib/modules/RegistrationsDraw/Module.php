<?php

namespace RegistrationsDraw;

use MapasCulturais\App;

class Module extends \MapasCulturais\Module
{

    public function _init()
    {
        $app = App::i();

        $app->hook('template(opportunity.single.opportunity-support--tab):after', function () {
            $this->part('opportunity/tab-draw');
        });

        $app->hook('template(opportunity.single.tabs-content):end', function () use ($app) {
            /**
             * @var \MapasCulturais\Controllers\Opportunity $this
             */

            $app->view->enqueueStyle('app', 'prize-draw', 'css/prize-draw.css');
            $categories = $this->controller->requestedEntity->registrationCategories;
            $this->part('opportunity/prize-draw-content', ['categories' => $categories]);
        });
    }

    public function register()
    {
    }
}