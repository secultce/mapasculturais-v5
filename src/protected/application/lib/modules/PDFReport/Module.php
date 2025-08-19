<?php

namespace PDFReport;

use MapasCulturais\App;
use MapasCulturais\Entities\Registration;

class Module extends \MapasCulturais\Module
{
    public function _init()
    {
        $app = App::i();

        $app->hook('template(opportunity.single.header-inscritos):end', function () use ($app) {
            $app->view->enqueueScript('app', 'pdfreport', 'js/pdfreport.js');

            $entity = $this->controller->requestedEntity;
            $resource = false;

            // VERIFICANDO SE TEM A INDICAÇÃO DE RECURSO
            $isResource = array_key_exists('claimDisabled', $entity->metadata);
            if ($isResource) {
                foreach ($entity->metadata as $key => $value) {
                    // SE O CAMPO EXISTIR E TIVER RECURSO HABILITADO
                    if ($key == 'claimDisabled' && $value == 0) {
                        $resource = true;
                    }
                }
            }

            $this->part('reports/buttons-report', ['resource' => $resource]);
        });

        $app->hook('template(registration.view.header-fieldset):before', function () use ($app) {
            $app->view->enqueueStyle('app', 'pdfreport', 'css/styleButtonPrint.css');

            $registration = $app->repo('Registration')->find($this->data['entity']->id);
            if (!is_null($registration) && $registration->status <> Registration::STATUS_DRAFT) {
                $app->view->part('reports/button-print', ['registration' => $registration]);
            }
        });
    }

    public function register()
    {
        $app = App::i();
        $app->registerController('pdf', Controllers\Pdf::class);
    }
}
