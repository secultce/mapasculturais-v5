<?php
namespace OpinionManagement;

use MapasCulturais\App,
    OpinionManagement\Controllers\OpinionManagement;
use MapasCulturais\Entities\Opportunity as OpportunityEntity;
use MapasCulturais\Entities\Registration;

/**
 * @method part(string $string,  array $args = [])
 * @property mixed|null $controller
 */
class Module extends \MapasCulturais\Module {
   function _init () 
   {
        $app = (new App)->i();
        // Load assets in head
        $app->hook('template(<<registration|opportunity|panel>>.<<single|view|registrations|index>>.head):begin', function () use ($app) {
            // $app->view->enqueueScript(
            //     'app',
            //     'swal2',
            //     'js/sweetalert2.all.min.js'
            // );
            // $app->view->enqueueStyle(
            //     'app',
            //     'swal2-theme-secultce',
            //     'css/swal2-secultce.min.css'
            // );
            $app->view->enqueueStyle(
                'app',
                'opinionManagement',
                'OpinionManagement/css/opinionManagement.css'
            );
            $app->view->enqueueScript(
                'app',
                'opinion-management',
                'OpinionManagement/js/opinionManagement.js'
            );
        });

        $plugin = $this;
      
        $app->hook('template(opportunity.edit.evaluations-config):begin', function () use ($app, $plugin) {
            $opportunity = $this->controller->requestedEntity;

            if($plugin::isEvaluationMethodValid($opportunity)) {
                $this->part('OpinionManagement/selection-autopublish', ['opportunity' => $opportunity]);
            }
        });

        $app->hook('template(opportunity.single.registration-list-header):end', function() use($app, $plugin) {
            $opportunity = $this->controller->requestedEntity;

            if($plugin::isEvaluationMethodValid($opportunity) && $opportunity->canUser('@control')) {
                $this->part('OpinionManagement/admin-registrations-table-column.php');
                $app->view->enqueueScript(
                    'app',
                    'opinion-management-tab-registrations',
                    'OpinionManagement/js/admin-tab-registrations.js'
                );

                $app->hook('template(opportunity.single.registration-list-item):end', function() {
                    $this->part('OpinionManagement/admin-btn-show-opinion.php');
                });
            }
            });

            $app->hook('template(opportunity.single.user-registration-table--registration--status):end', function ($registration, $opportunity) use ($app) {
            if($opportunity->publishedOpinions === true && $registration->canUser('@control')) {
                $this->part('OpinionManagement/user-btn-show-opinion.php', ['registration' => $registration]);
            }
            });

            $app->hook('template(opportunity.single.opportunity-registrations--tables):begin', function () use ($app, $plugin) {
            $opportunity = $this->controller->requestedEntity;
            if($plugin::isEvaluationMethodValid($opportunity)
                && $opportunity->autopublishOpinions !== 'Sim'
                && !$opportunity->publishedOpinions
                && $opportunity->canUser('@control')//Verifica a gestão
            ) {
                $this->part('OpinionManagement/admin-btn-publish-opinions.php', ['opportunity' => $opportunity]);
            }
            });

            $app->hook('template(registration.view.header-fieldset):after', function() use($app, $plugin) {
            $registration = $this->controller->requestedEntity;
            $opportunity = $registration->opportunity;

            if($plugin::isEvaluationMethodValid($opportunity)
                && $opportunity->publishedOpinions
                && $opportunity->canUser('@control')
            ) {
                $this->part('OpinionManagement/user-btn-show-opinion.php');
            }
            });

            $app->hook('template(panel.<<registrations|index>>.panel-registration):end', function (Registration $registration) use ($app,$plugin) {
            if($registration->opportunity->publishedOpinions
                && $plugin::isEvaluationMethodValid($registration->opportunity)
            ) {
                $this->part('OpinionManagement/user-btn-show-opinion.php', ['registration' => $registration]);
                $app->view->enqueueScript(
                    'app',
                    'opinion-management',
                    'OpinionManagement/js/opinionManagement.js'
                );
            }
            });

            $app->hook('entity(Opportunity).publishRegistrations:before', function () {
            if($this->autopublishOpinions === 'Sim') {
                $this->setMetadata('publishedOpinions', true);
                Controller::notificateUsers($this->id);
            }
        });
   }
   
     /**
     * @throws \Exception
     */
   function register () {
         $app = App::i();
         $app->registerController('opinionManagement', Controllers\Controller::class);

        $this->registerOpportunityMetadata('autopublishOpinions', [
            'type' => 'select',
            'default_value' => 'Não',
            'label' => \MapasCulturais\i::__('Publicar pareceres automaticamente'),
            'description' => \MapasCulturais\i::__('Indica se os pareceres devem ser publicados automaticamente'),
            'options' => ['Sim', 'Não'],
            'required' => true,
        ]);

        $this->registerOpportunityMetadata('publishedOpinions', [
            'type' => 'boolean',
            'label' => \MapasCulturais\i::__('Pareceres publicados'),
            'default_value' => false,
            'options' => [true, false],
            'required' => true,
        ]);
   }

    public static function isEvaluationMethodValid(OpportunityEntity $opportunity): bool
    {
        return $opportunity->evaluationMethodConfiguration->type == 'documentary'
            || $opportunity->evaluationMethodConfiguration->type == 'technical';
    }
}