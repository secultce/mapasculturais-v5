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

            $this->part('counter-argument/send-btn', [
                'isCounterArgumentPeriod' => $isCounterArgumentPeriod,
                'registration' => $registration,
            ]);
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
            'counter-argument',
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
