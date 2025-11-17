<?php
namespace CounterReason;

use CounterReason\Repositories\CounterReasonRepository;
use DateTime;
use MapasCulturais\App;
use MapasCulturais\i;

require __DIR__ . '/Entities/CounterReason.php';
require __DIR__ . '/Entities/CounterReasonFile.php';
// require __DIR__ . '/Entities/File.php';
require __DIR__ . '/Services/CounterReasonService.php';
require __DIR__ . '/Repositories/CounterReasonRepository.php';

class Module extends \MapasCulturais\Module {


    public function _init()
    {
        $module = $this;
        $app = App::i();
        $app->hook('view.partial(singles/opportunity-registrations--export):after', function ($__template, &$__html) use ($app,$module) {

            $module->publishAssetsCounterReason();
            $opp = $this->controller->requestedEntity;
            $this->part('counterReason/configuration-counter-reason', [
                'opp' => $opp,
            ]);
        });
        $app->hook('template(opportunity.single.user-registration-table--registration--status):end', function($reg_args) use ($app,$module){
            $module->publishAssetsCounterReason();
            $baseUrl = $app->_config['base.url'];
            if (CounterReasonRepository::validatePeriodCounterReason($reg_args))
            {
                $this->part('counterReason/open-counter-reason', [
                    'entity' => $reg_args,
                    'baseUrl' => $baseUrl
                ]);
            }
        });

        $app->hook('template(panel.registrations.panel-registration-meta):after', function ($reg_args) use ($app,$module) {
            $module->publishAssetsCounterReason();
            $counterReason = CounterReasonRepository::getCounterReason($reg_args, $app);
            $labelButton = $counterReason ? 'Editar Contrarrazão' : 'Abrir Contrarrazão';
            if (CounterReasonRepository::validatePeriodCounterReason($reg_args))
            {
                $this->part('counterReason/button-open-counter-reason', [
                    'entity' => $reg_args,
                    'labelButton' => $labelButton,
                    'cr' => $counterReason
                ]);
            }elseif(isset($counterReason->send)){
                echo '<p><label class="info-btn-recourse">Contrarrazão enviada!</label></p>';
            }
        });

        /**
         * Adiciona novos menus no painel
         */

        $app->hook('template(<<panel|contrarrazao>>.<<*>>.nav.panel.registrations):after', function () use($app) {
            $idAgent = $app->getUser()->profile->id;
            $url = $app->createUrl('contrarrazao', 'todas/'.$idAgent);
            echo '<li><a href="'.$url.'"><span class="fas fa-outdent"></span> Minhas Contrarrazão</a></li>';
        });


        $app->hook('controller(CounterReason).all:begin', function ($cr_args) use ($app,$module) {
            $module->publishAssetsCounterReason();
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

    protected function publishAssetsCounterReason() {
        $app = App::i();
        $app->view->enqueueScript('app', 'counterReason', 'js/counterReason/counterReason.js', []);
        $app->view->enqueueStyle('app', 'counterReasoncss', 'css/counterReason/style.css', []);
    }
}