<?php

namespace RegistrationsDraw;

use MapasCulturais\App;
use MapasCulturais\Controllers\RegistrationsDraw;

class Module extends \MapasCulturais\Module
{

    public function _init()
    {
        $app = App::i();

        $app->hook('template(opportunity.single.opportunity-support--tab):after', function () {
            /**
             * @var \MapasCulturais\Entities\Opportunity $opportunity
             * @var bool $drawSetted
             */
            $opportunity = $this->controller->requestedEntity;
            // todo: Implements draw setting
            $drawSetted = true;

            /** Skip the hook when opportunity not setted to prize draw or user can't control this opportunity */
            if(!$drawSetted || !$opportunity->canUser('@control'))
                return;

            $this->part('opportunity/tab-draw');
        });

        $app->hook('template(opportunity.single.tabs-content):end', function () use ($app) {
            /**
             * @var \MapasCulturais\Controllers\Opportunity $this
             * @var \MapasCulturais\Entities\Opportunity $opportunity
             * @var bool $drawSetted
             */
            $opportunity = $this->controller->requestedEntity;
            // todo: Implements draw setting
            $drawSetted = true;

            /** Skip the hook when the user can't control this opportunity */
            if(!$drawSetted || !$opportunity->canUser('@control'))
                return;

            $app->view->enqueueStyle('app', 'prize-draw', 'css/prize-draw.css');
            $drawedCategories = ['Ficção', 'Direção Estreante']; // @todo: Recuperar metadado com as categorias sorteadas
            $rankings = [];
            $categories = array_map(function ($category) use ($drawedCategories, $opportunity, &$app, &$rankings) {
                $isDrawed = in_array($category, $drawedCategories, true);
                if($isDrawed)
                    $rankings[$category] = $app->repo('RegistrationsRanking')->findRanking($opportunity, $category);

                return (object)[
                    'name' => $category,
                    'isDrawed' => $isDrawed,
                ];
            }, $opportunity->registrationCategories);

            $this->part('opportunity/prize-draw-content', [
                'categories' => $categories,
                'rankings' => $rankings,
            ]);
        });
    }

    public function register()
    {
        $app = App::i();
        $app->registerController('sorteio-inscricoes', RegistrationsDraw::class);
    }
}
