<?php

namespace RegistrationsDraw;

use MapasCulturais\App;
use MapasCulturais\Controllers\RegistrationsDraw;
use MapasCulturais\Entities\Draw;
use MapasCulturais\Entities\Opportunity;
use MapasCulturais\Entities\Registration;
use MapasCulturais\Entities\User;
use MapasCulturais\GuestUser;

/**
 * @method part(string $string, ?array $params = null)
 */
class Module extends \MapasCulturais\Module
{
    private $app;

    public function _init()
    {
        $this->app = App::getInstance();
        $module = $this;

        $this->app->hook('template(opportunity.edit.opportunity-registrations--rules):after', function () {
            $opportunity = $this->controller->requestedEntity;
            if ($opportunity->evaluationMethodConfiguration->getType()->id !== 'documentary') {
                return;
            }

            $this->part('opportunity/config-fieldset', ['opportunity' => $opportunity]);
        });

        $this->app->hook('template(opportunity.single.tabs):end', function () use ($module) {
            /**
             * @var Opportunity $opportunity
             */
            $opportunity = $this->controller->requestedEntity;
            if (!$module->canUserAccessDraws($opportunity, $module->app->user)) {
                return;
            }

            $this->part('opportunity/tab-draw');
        });

        $this->app->hook('template(opportunity.single.tabs-content):end', function () use ($module) {
            /**
             * @var \MapasCulturais\Controllers\Opportunity $this
             * @var Opportunity $opportunity
             */
            $opportunity = $this->controller->requestedEntity;
            if (!$module->canUserAccessDraws($opportunity, $module->app->user)) {
                return;
            }

            $module->app->view->enqueueStyle('app', 'prize-draw', 'css/prize-draw.css');
            $module->app->view->enqueueScript('app', 'prize-draw', 'js/prize-draw-content.js');
            $rankings = [];
            $categories = $opportunity->registrationCategories ?: [""];

            foreach ($categories as $category) {
                $rankings[$category] = $module->app->repo(Draw::class)
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
        $this->app->registerController('sorteio-inscricoes', RegistrationsDraw::class);

        $this->registerOpportunityMetadata('useRegistrationsDraw', [
            'type' => 'select',
            'label' => 'Usar sorteio de para ranking de inscrições',
            'default' => false,
            'options' => [
                false => 'Não',
                true => 'Sim',
            ],
            'unserialize' => static function ($value) {
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

    /**
     * @param int|Opportunity $opportunity
     * @param User|GuestUser $user
     * @return bool
     */
    private function isProponent($opportunity, $user): bool
    {
        return !empty($this->app->repo(Registration::class)
            ->findByOpportunityAndUser($opportunity, $user));
    }

    /**
     * Return true when opportunity setted to prize draw and user can control or is a proponent
     * and draws published to proponents.
     *
     * @param Opportunity $opportunity
     * @param User|GuestUser $user
     * @return bool
     */
    public function canUserAccessDraws(Opportunity $opportunity, $user): bool
    {
        /** @var bool $drawSetted */
        $drawSetted = $opportunity->useRegistrationsDraw;
        $isProponent = $this->isProponent($opportunity, $user);
        $userAllowed = $opportunity->canUser('@control', $user) || ($opportunity->isPublishedDraw && $isProponent);

        return $drawSetted && $userAllowed;
    }
}
