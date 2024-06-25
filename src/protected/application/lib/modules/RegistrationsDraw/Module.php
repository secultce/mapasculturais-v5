<?php

namespace RegistrationsDraw;

use MapasCulturais\App;
use MapasCulturais\Controllers\RegistrationsDraw;

class Module extends \MapasCulturais\Module
{

    public function _init()
    {
        $app = App::i();

        $app->hook('template(opportunity.edit.opportunity-registrations--rules):after', function () use ($app) {
            $opportunity = $this->controller->requestedEntity;
            if($opportunity->evaluationMethodConfiguration->getType()->id !== 'documentary')
                return;

            $this->part('opportunity/config-fieldset', ['opportunity' => $opportunity]);
        });

        $app->hook('template(opportunity.single.opportunity-support--tab):after', function () {
            /**
             * @var \MapasCulturais\Entities\Opportunity $opportunity
             * @var bool $drawSetted
             */
            $opportunity = $this->controller->requestedEntity;
            $drawSetted = $opportunity->useRegistrationsDraw;

            /** Skip the hook when opportunity not setted to prize draw, user can't control this opportunity,
             *  or registrations period is open.
             */
            if(!$drawSetted || !$opportunity->canUser('@control') || $opportunity->registrationTo > new \DateTime())
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
            $drawSetted = $opportunity->useRegistrationsDraw;

            /** Skip the hook when opportunity not setted to prize draw, user can't control this opportunity,
             *  or registrations period is open.
             */
            if(!$drawSetted || !$opportunity->canUser('@control') || $opportunity->registrationTo > new \DateTime())
                return;

            $app->view->enqueueStyle('app', 'prize-draw', 'css/prize-draw.css');
            $app->view->enqueueScript('app', 'prize-draw', 'js/prize-draw-content.js');
            $drawedCategories = $opportunity->drawedRegistrationsCategories ?? [];
            $rankings = [];
            $categories = array_map(function ($category) use ($drawedCategories, $opportunity, &$app, &$rankings) {
                $isDrawed = in_array($category, $drawedCategories, true);
                if($isDrawed)
                    $rankings[$category] = $app->repo('RegistrationsRanking')->findRanking($opportunity, $category);

                return (object)[
                    'name' => $category,
                    'isDrawed' => $isDrawed,
                ];
            }, $opportunity->registrationCategories ?: [""]);

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

        $this->registerOpportunityMetadata('drawedRegistrationsCategories', [
            'type' => 'string',
            'label' => 'Categorias com ranking sorteados',
            'default' => json_encode([]),
            'serialize' => function($value) {
                return json_encode($value);
            },
            'unserialize' => function($value) {
                return json_decode($value);
            },
        ]);

        $this->registerOpportunityMetadata('useRegistrationsDraw', [
            'type' => 'select',
            'label' => 'Usar sorteio de para ranking de inscrições',
            'default' => false,
            'options' => [
                false => 'Não',
                true => 'Sim',
            ],
            'unserialize' => function($value) {
                return $value == 'Sim';
            },
        ]);
    }
}
