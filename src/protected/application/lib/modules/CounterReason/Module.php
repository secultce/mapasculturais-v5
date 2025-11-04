<?php
namespace CounterReason;

use DateTime;
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
            ]);
        });
        $app->hook('template(opportunity.single.user-registration-table--registration--status):end', function($reg_args) use ($app){
            $isPeriod = self::validatePeriod(
                'recourse_date_initial',
                'recourse_date_end',
                'recourse_time_initial',
                'recourse_time_end',
                    $reg_args
                );
            $baseUrl = $app->_config['base.url'];
            if(  $isPeriod )
            {
                $this->part('counterReason/open-counter-reason', [
                    'entity' => $reg_args,
                    'baseUrl' => $baseUrl,
                ]);
            }
        });
        $app->hook('template(panel.registrations.panel-registration-meta):after', function($reg_args) use ($app){
            $isPeriod = self::validatePeriod(
                'counterReason_date_initial',
                'counterReason_date_end',
                'counterReason_time_initial',
                'counterReason_time_end',
                $reg_args
            );

            if($isPeriod)
            {
                $this->part('counterReason/button-open-counter-reason', [
                    'entity' => $reg_args
                ]);
            }

        });

    }

    static public function validatePeriod($dtInit, $dtEnd, $tmInit, $tmEnd, $entity) : bool
    {
        $strToInitial = $entity->opportunity->getMetadata($dtInit).' '.$entity->opportunity->getMetadata($tmInit);
        $initialOfPeriod = \DateTime::createFromFormat('Y-m-d H:i', $strToInitial);
        $strToEnd = $entity->opportunity->getMetadata($dtEnd).' '.$entity->opportunity->getMetadata($tmEnd);
        $endOfPeriod = \DateTime::createFromFormat('Y-m-d H:i', $strToEnd);
        $now = new DateTime();
        if(  $now >= $initialOfPeriod &&  $now <= $endOfPeriod )
            return true;

        return false;

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
