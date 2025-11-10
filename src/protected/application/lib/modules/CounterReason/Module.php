<?php
namespace CounterReason;
ini_set('error_reporting', E_ALL & ~E_NOTICE);
use CounterReason\Repositories\CounterReasonRepository;
use DateTime;
use MapasCulturais\App;
use MapasCulturais\i;

require __DIR__.'/Entities/CounterReason.php';
require __DIR__.'/Services/CounterReasonService.php';
require __DIR__.'/Repositories/CounterReasonRepository.php';
class Module extends \MapasCulturais\Module {


    public function _init()
    {
        $app = App::i();
        $app->hook('view.partial(singles/opportunity-registrations--export):after', function ($__template, &$__html) use ($app) {
        $app->view->enqueueScript('app','counterReason','js/counterReason/counterReason.js',[]);
            $opp = $this->controller->requestedEntity;
            $this->part('counterReason/configuration-counter-reason', [
                'opp' => $opp,
            ]);
        });
        $app->hook('template(opportunity.single.user-registration-table--registration--status):end', function($reg_args) use ($app){
            $app->view->enqueueScript('app', 'counterReason', 'js/counterReason/counterReason.js', []);
            $baseUrl = $app->_config['base.url'];
            if (self::validatePeriodCounterReason($reg_args))
            {
                $this->part('counterReason/open-counter-reason', [
                    'entity' => $reg_args,
                    'baseUrl' => $baseUrl,
                ]);
            }
        });

        $app->hook('template(panel.registrations.panel-registration-meta):after', function ($reg_args) use ($app) {
            $labelButton = 'Abrir Contrarrazão'; // Texto inicial
            $app->view->enqueueScript('app', 'counterReason', 'js/counterReason/counterReason.js', []);
            $app->view->enqueueStyle('app', 'counterReasoncss', 'css/counterReason/style.css', []);
            $counterReason = CounterReasonRepository::getCounterReason($reg_args, $app);

            if ($counterReason)
            {
                $labelButton = 'Editar Contrarrazão'; // Altera o texto do botão
            }

            if (self::validatePeriodCounterReason($reg_args))
            {
                $this->part('counterReason/button-open-counter-reason', [
                    'entity' => $reg_args,
                    'labelButton' => $labelButton,
                    'cr' => $counterReason
                ]);
            }
        });
    }

    /**
     * Verifica o período para habilitar o botão de submeter a contrarrazão
     * @param mixed $entity
     * @return bool
     */
    static public function validatePeriodCounterReason($entity): bool
    {
        $strToInitial = $entity->opportunity->getMetadata('counterReason_date_initial') . ' ' . $entity->opportunity->getMetadata('counterReason_time_initial');
        $initialOfPeriod = \DateTime::createFromFormat('Y-m-d H:i', $strToInitial);
        $strToEnd = $entity->opportunity->getMetadata('counterReason_date_end') . ' ' . $entity->opportunity->getMetadata('counterReason_time_end');
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
