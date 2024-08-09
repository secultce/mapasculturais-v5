<?php

namespace RegistrationsDraw;

use MapasCulturais\App;
use MapasCulturais\Controllers\RegistrationsDraw;
use MapasCulturais\Entities\Draw;
use MapasCulturais\GuestUser;
use MapasCulturais\Repositories\RegistrationsRanking;

/**
 * @method part(string $string, ?array $params = null)
 */
class Module extends \MapasCulturais\Module
{
    public function _init()
    {
        $app = App::getInstance();

        $app->hook('template(opportunity.edit.opportunity-registrations--rules):after', function () use ($app) {
            if (is_a($app->user, GuestUser::class)) {
                return;
            }
            $opportunity = $this->controller->requestedEntity;
            if ($opportunity->evaluationMethodConfiguration->getType()->id !== 'documentary') {
                return;
            }

            $this->part('opportunity/config-fieldset', ['opportunity' => $opportunity]);
        });

        $app->hook('template(opportunity.single.tab-main-content):after', function () use ($app) {
            if (is_a($app->user, GuestUser::class)) {
                return;
            }
            $opportunity = $this->controller->requestedEntity;
            if ($opportunity->evaluationMethodConfiguration->getType()->id !== 'documentary') {
                return;
            }

            if ($opportunity->useRegistrationsDraw && $opportunity->isPublishedDraw) {
                $this->part('opportunity/tab-draw', ['opportunity' => $opportunity]);
            }
        });

        $app->hook('template(opportunity.single.opportunity-support--tab):after', function () use ($app) {
            if (is_a($app->user, GuestUser::class)) {
                return;
            }
            /**
             * @var \MapasCulturais\Entities\Opportunity $opportunity
             * @var bool $drawSetted
             */
            $opportunity = $this->controller->requestedEntity;
            $drawSetted = $opportunity->useRegistrationsDraw;

            /** Skip the hook when opportunity not setted to prize draw, user can't control this opportunity,
             *  or registrations period is open.
             */
            if (!$drawSetted || !$opportunity->canUser('@control') || $opportunity->registrationTo > new \DateTime()) {
                return;
            }

            $this->part('opportunity/tab-draw');
        });

        $app->hook('template(opportunity.single.tabs-content):end', function () use ($app) {
            if (is_a($app->user, GuestUser::class)) {
                return;
            }
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
            if (
                !$drawSetted
                || (!$opportunity->canUser('@control') && !$opportunity->isPublishedDraw)
                || $opportunity->registrationTo > new \DateTime()
            ) {
                return;
            }

            $app->view->enqueueStyle('app', 'prize-draw', 'css/prize-draw.css');
            $app->view->enqueueScript('app', 'prize-draw', 'js/prize-draw-content.js');
            $drawedCategories = $opportunity->drawedRegistrationsCategories ?? []; // @todo: remover metadado
            $rankings = [];
            $categories = $opportunity->registrationCategories ?: [""];

            foreach ($categories as $category) {
                $rankings[$category] = $app->repo(Draw::class)
                    ->findBy([
                        'category' => $category,
                        'opportunity' => $opportunity->id,
                    ]);
            }


            $this->part('opportunity/prize-draw-content', [
                'categories' => $categories,
                'rankings' => $rankings,
                'opportunity' => $opportunity,
                'isAdmin' => $opportunity->canUser('@control'),
                'isPublished' => $opportunity->isPublishedDraw,
            ]);
        });
    }

    public function register()
    {
        $app = App::getInstance();
        $app->registerController('sorteio-inscricoes', RegistrationsDraw::class);

        $this->registerOpportunityMetadata('useRegistrationsDraw', [
            'type' => 'select',
            'label' => 'Usar sorteio de para ranking de inscrições',
            'default' => false,
            'options' => [
                false => 'Não',
                true => 'Sim',
            ],
            'unserialize' => function ($value) {
                return $value == 'Sim';
            },
        ]);

        $this->registerOpportunityMetadata('isPublishedDraw', [
            'type' => 'boolean',
            'required' => 'true',
            'label' => 'Sorteio de inscrições publicados',
            'default' => false,
            'options' => [false, true],
        ]);
    }
}
