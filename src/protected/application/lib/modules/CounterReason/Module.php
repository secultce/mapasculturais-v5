<?php
namespace CounterReason;

use MapasCulturais\App;
use MapasCulturais\i;

class Module extends \MapasCulturais\Module {

    public function _init()
    {
        $app = App::i();
        $module = $this;
        $app->hook('view.partial(singles/opportunity-registrations--export):after', function($__template, &$__html) use ($app,$module){
        $app->view->enqueueScript('app','counterReason','js/counterReason/counterReason.js',[]);
            $opp = $this->controller->requestedEntity;
            $this->part('counterReason/configuration-counter-reason', [
                'opp' => $opp,
                'app' => $app,
            ]);
        });
    }
    function register () {
        $app = App::i();
        $app->registerController('contrarrazao', Controllers\Controller::class);
        // Metadados
        $this->registerOpportunityMetadata('counterReason_date_initial', [
            'label' => i::__('Data Inicial'),
            'type' => 'date',
        ]);
        $this->registerOpportunityMetadata('counterReason_time_initial', [
            'label' => i::__('Hora Inicial'),
            'type' => 'time',
        ]);
        $this->registerOpportunityMetadata('counterReason_date_end', [
            'label' => i::__('Hora Inicial'),
            'type' => 'date',
        ]);
        $this->registerOpportunityMetadata('counterReason_time_end', [
            'label' => i::__('Hora Final'),
            'type' => 'time',
        ]);
    }

}
