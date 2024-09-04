<?php

namespace QuotasSet;

use MapasCulturais\App;
use MapasCulturais\Controller;
use MapasCulturais\Theme;

class Module extends \MapasCulturais\Module
{
    /** @var App $app */
    private $app;

    public function _init(): void
    {
        $this->app = App::getInstance();
        $module = $this;

        $this->app->hook('template(panel.<<*>>.nav.panel.accountability):after', function () use ($module) {
            /** @var Theme $this */
            if ($module->canUserAccess()) {
                $this->part('quotas-set.nav.panel');
            }
        });

        $this->app->hook('GET(panel.cotas-e-politicas)', function () use ($module) {
            /** @var Controller $this */
            $this->requireAuthentication();
            if ($module->canUserAccess()) {
                $this->render('panel-quotas-set');
            } else {
                $module->app->redirect('/panel');
            }
        });
    }

    public function register(): void
    {
        // TODO: Implement register() method.
    }

    private function canUserAccess(): bool
    {
        foreach ($this->app->user->profile->sealRelations as $sealRelation) {
            if ($sealRelation->seal->id == env('SECULT_SEAL_ID')) {
                return true;
            }
        }

        return false;
    }
}
