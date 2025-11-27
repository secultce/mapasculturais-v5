<?php

namespace CounterArgument;

use MapasCulturais\App;

class Module extends \MapasCulturais\Module
{
    public function _init()
    {
        App::i()->hook('view.partial(singles/opportunity-registrations--export):after', function () {
            App::i()->view->enqueueScript('app', 'counter-argument', 'counter-argument/js/config.js');

            $opportunity = $this->controller->requestedEntity;
            $this->part('counter-argument/config', ['opportunity' => $opportunity]);
        });
    }

    public function register()
    {
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
    }
}
