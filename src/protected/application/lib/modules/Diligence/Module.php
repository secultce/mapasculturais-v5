<?php

namespace Diligence;

use MapasCulturais\App,
    MapasCulturais\i,
    MapasCulturais\Entities,
    MapasCulturais\Definitions,
    MapasCulturais\Exceptions;

require __DIR__.'/Repositories/Diligence.php';
require __DIR__.'/Entities/Diligence.php';
require __DIR__.'/Entities/AnswerDiligence.php';

use Diligence\Repositories\Diligence as DiligenceRepo;
use Diligence\Entities\Diligence as EntityDiligence;

class Module extends \MapasCulturais\Module {

    function _init () {
        $app = App::i();
       
        $app->hook('template(registration.view.content-diligence):begin', function () use ($app) {
            $app->view->enqueueStyle('app', 'diligence', 'css/diligence/style.css');
            $entity = $this->controller->requestedEntity;
            $diligenceRepository = DiligenceRepo::findBy('Diligence\Entities\Diligence',['registration' => $entity->id]);
            // dump($diligenceRepository);
            // die;
            $term = EntityDiligence::verifyTerm($diligenceRepository, $entity);
            $placeHolder = '';
            EntityDiligence::isProponent($diligenceRepository) ? $placeHolder = 'Escreva a sua resposta' : 
            $placeHolder = 'Escreva sua diligência';
            $this->part('diligence/tabs-parent',[
                'entity' => $entity,
                'diligenceRepository' => $diligenceRepository,
                'term' => $term,
                'placeHolder' => $placeHolder
            ]);
        });

        $app->hook('template(opportunity.edit.evaluations-config):begin', function () use ($app) {
            $entity = $this->controller->requestedEntity;
            $this->part('diligence/days', ['entity' => $entity]);
        });
    }
    
    function register () {
        $app = App::i();
        $app->registerController('diligence', Controllers\Controller::class);
        //Registrar metadata na tabela opportunity
        $this->registerOpportunityMetadata('diligence_days', [
            'label' => i::__('Dias corridos para resposta da diligência'),
            'type' => 'string',
            'default' => 3,
            'validations' => [
                'v::intVal()->positive()->between(1, 365)' => 'O valor deve ser um número inteiro positivo'
            ]
        ]);
    }

    /**
     * Publica todos os assets (css/js)
     *
     */
    protected function _publishAssets()
    {
        $app = App::i();

        $app->view->enqueueStyle('app', 'secultalert', 'https://raw.githubusercontent.com/secultce/plugin-Recourse/main/assets/css/recourse/secultce.min.css');
        $app->view->enqueueScript('app','sweetalert2','https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.all.min.js');
    }
}