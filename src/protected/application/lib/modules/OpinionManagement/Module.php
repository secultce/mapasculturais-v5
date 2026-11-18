<?php
namespace OpinionManagement;

use MapasCulturais\App;
use MapasCulturais\Entities\Opportunity as OpportunityEntity;
use MapasCulturais\Entities\Registration;
use MapasCulturais\Entities\RegistrationEvaluation;
use OpinionManagement\Controllers\Controller;
use OpinionManagement\Services\EvaluationReviewers;
use OpinionManagement\Services\OpinionPublication;

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
                $this->jsObject['opinionPublication'] = [
                    'published' => (bool) $opportunity->publishedOpinions,
                    'manual' => $opportunity->autopublishOpinions !== 'Sim',
                ];
                $this->part('OpinionManagement/admin-registrations-table-column.php');
                $app->view->enqueueScript(
                    'app',
                    'opinion-management-tab-registrations',
                    'OpinionManagement/js/admin-tab-registrations.js',
                    ['entity.module.opportunity']
                );

                $app->hook('template(opportunity.single.registration-list-item):end', function() {
                    $this->part('OpinionManagement/admin-btn-show-opinion.php');
                });
            }
            });

            $app->hook('template(opportunity.single.user-registration-table--registration--status):end', function ($registration, $opportunity) use ($app) {
            if(OpinionPublication::isPublished($registration) && $registration->canUser('@control')) {
                $this->part('OpinionManagement/user-btn-show-opinion.php', ['registration' => $registration]);
            }
            });

            $app->hook('template(opportunity.single.opportunity-registrations--tables):begin', function () use ($app, $plugin) {
            $opportunity = $this->controller->requestedEntity;
            if($plugin::isEvaluationMethodValid($opportunity)
                && !$opportunity->publishedOpinions
                && $opportunity->canUser('@control')//Verifica a gestão
            ) {
                $this->part('OpinionManagement/admin-btn-publish-opinions.php', [
                    'opportunity' => $opportunity,
                    'allEvaluationsSent' => $plugin::areAllEvaluationsSent($opportunity),
                    'manualPublication' => $opportunity->autopublishOpinions !== 'Sim',
                ]);
            }
            });

            $app->hook('template(registration.view.header-fieldset):after', function() use($app, $plugin) {
            $registration = $this->controller->requestedEntity;
            $opportunity = $registration->opportunity;

            if($plugin::isEvaluationMethodValid($opportunity)
                && OpinionPublication::isPublished($registration)
                && ($registration->canUser('@control') || $opportunity->canUser('@control'))
            ) {
                $this->part('OpinionManagement/user-btn-show-opinion.php');
            }
            });

            $app->hook('template(panel.<<registrations|index>>.panel-registration):end', function (Registration $registration) use ($app,$plugin) {
            if(OpinionPublication::isPublished($registration)
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

        $app->hook('can(Opportunity.publishRegistrations)', function ($user, &$result) use ($plugin) {
            if (
                $result
                && $this->autopublishOpinions === 'Sim'
                && $plugin::isEvaluationMethodValid($this)
                && !$plugin::areAllEvaluationsSent($this)
            ) {
                $result = false;
            }
        });

        $app->hook('entity(Opportunity).publishRegistrations:before', function () {
            if($this->autopublishOpinions === 'Sim') {
                $this->setMetadata('publishedOpinions', true);
            }
        });

        $app->hook('entity(Opportunity).publishRegistrations:committed', function () use ($app) {
            try {
                $opportunity = $app->repo(OpportunityEntity::class)->find($this->id);
                if (!$opportunity || $opportunity->autopublishOpinions !== 'Sim') {
                    return;
                }

                $sealIds = $opportunity->owner->getRelatedSealIds();
                Controller::notificateUsers($opportunity->id, false, $sealIds);
            } catch (\Throwable $exception) {
                $app->log->error(
                    "Não foi possível notificar os proponentes sobre a publicação dos pareceres " .
                    "da oportunidade {$this->id}: {$exception->getMessage()}"
                );
            }
        });
   }

     /**
     * @throws \Exception
     */
   function register () {
         $app = App::i();
         $app->registerController('opinionManagement', Controllers\Controller::class);

        $this->registerRegistrationMetadata('opinionPublication', [
            'type' => 'json',
            'label' => \MapasCulturais\i::__('Publicação dos pareceres'),
            'private' => true,
        ]);

        // Publication metadata can only be written by the opportunity management.
        $app->hook('can(RegistrationMeta.<<create|modify|remove>>)', function ($user, &$result) {
            if ($this->key === 'opinionPublication') {
                $result = $this->owner->opportunity->canUser('@control', $user);
            }
        });

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
        $configuration = $opportunity->evaluationMethodConfiguration;

        return $configuration && in_array($configuration->type->id, ['documentary', 'technical'], true);
    }

    public static function areAllEvaluationsSent(OpportunityEntity $opportunity): bool
    {
        $valuerUserIds = EvaluationReviewers::activeUserIds($opportunity);
        if (!$valuerUserIds) {
            return false;
        }

        $repository = App::i()->repo(RegistrationEvaluation::class);
        $evaluationsCount = $repository->countByOpportunityAndUsersAndStatus($opportunity, $valuerUserIds);

        if (!$evaluationsCount) {
            return false;
        }

        $sentEvaluationsCount = $repository->countByOpportunityAndUsersAndStatus(
            $opportunity,
            $valuerUserIds,
            [RegistrationEvaluation::STATUS_SENT]
        );

        return $evaluationsCount === $sentEvaluationsCount;
    }
}
