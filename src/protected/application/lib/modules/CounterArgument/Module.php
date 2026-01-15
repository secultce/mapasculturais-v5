<?php

namespace CounterArgument;

use MapasCulturais\App;
use MapasCulturais\Services\CounterArgumentService;

class Module extends \MapasCulturais\Module
{
    public function _init()
    {
        App::i()->hook('view.partial(singles/opportunity-registrations--export):after', function () {
            App::i()->view->enqueueScript('app', 'counter-argument', 'counter-argument/js/config.js');

            $opportunity = $this->controller->requestedEntity;
            $this->part('counter-argument/config', ['opportunity' => $opportunity]);
        });

        App::i()->hook('template(panel.<<registrations|index>>.panel-registration-meta):after', function ($registration) {
            App::i()->view->enqueueScript('app', 'counter-argument', 'counter-argument/js/proponent.js');

            $counterArgumentService = new CounterArgumentService();
            $isCounterArgumentPeriod = $counterArgumentService->isCounterArgumentPeriod($registration->opportunity);
            $hasCounterArgument = App::i()->repo('CounterArgument')->findOneBy(['registration' => $registration]);

            $this->part('counter-argument/send-btn', [
                'isCounterArgumentPeriod' => $isCounterArgumentPeriod,
                'registration' => $registration,
                'hasCounterArgument' => $hasCounterArgument
            ]);
        });

        App::i()->hook('template(panel.counterArguments.view):before', function () {
            App::i()->view->enqueueScript('app', 'counter-argument-common', 'counter-argument/js/common.js');
            App::i()->view->enqueueScript('app', 'counter-argument', 'counter-argument/js/proponent.js');
            App::i()->view->enqueueStyle('app', 'counter-argument', 'counter-argument/css/panel.css');
        });

        App::i()->hook('template(opportunity.single.opportunity-recourse--tab):after', function () {
            $this->part('counter-argument/opportunity--tab');
        });

        App::i()->hook('template(opportunity.single.tabs-content):end', function () {
            App::i()->view->enqueueScript('app', 'counter-argument-common', 'counter-argument/js/common.js');
            App::i()->view->enqueueScript('app', 'counter-argument-admin', 'counter-argument/js/admin.js');
            App::i()->view->enqueueStyle('app', 'counter-argument', 'counter-argument/css/panel.css');

            $opportunity = $this->controller->requestedEntity;
            $counterArguments = App::i()->repo('CounterArgument')->getAllByOpportunityId($opportunity->id);

            $this->part('counter-argument/opportunity', ['counterArguments' => $counterArguments]);
        });
    }

    public function register()
    {
        App::i()->registerController('contrarrazao', Controllers\Controller::class);

        $this->registerOpportunityMetadata('initialDateCounterArgument', [
            'label' => 'Data Inicial',
            'type' => 'date',
        ]);
        $this->registerOpportunityMetadata('initialTimeCounterArgument', [
            'label' => 'Hora Inicial',
            'type' => 'time',
        ]);
        $this->registerOpportunityMetadata('finalDateCounterArgument', [
            'label' => 'Data Final',
            'type' => 'date',
        ]);
        $this->registerOpportunityMetadata('finalTimeCounterArgument', [
            'label' => 'Hora Final',
            'type' => 'time',
        ]);

        App::i()->registerFileGroup(
            'contrarrazao',
            new \MapasCulturais\Definitions\FileGroup(
                'counter-argument-attachment',
                [
                    'text/plain',
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'image/*',
                ],
                'Esse formato de arquivo não é válido'
            )
        );
    }
}
