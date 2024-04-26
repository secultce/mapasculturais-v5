<?php

namespace Diligence;

use MapasCulturais\App,
    MapasCulturais\i,
    MapasCulturais\Entities,
    MapasCulturais\Definitions,
    MapasCulturais\Exceptions;
    
require __DIR__.'/Traits/DiligenceSingle.php';
require __DIR__.'/Service/DiligenceInterface.php';
require __DIR__.'/Repositories/Diligence.php';
require __DIR__.'/Entities/Diligence.php';
require __DIR__.'/Entities/AnswerDiligence.php';
require __DIR__.'/Entities/NotificationDiligence.php';
require __DIR__.'/Service/NotificationInterface.php';

use Diligence\Repositories\Diligence as DiligenceRepo;
use Diligence\Entities\Diligence as EntityDiligence;

class Module extends \MapasCulturais\Module {
 use \Diligence\Traits\DiligenceSingle;
    function _init () {
        
        $app = App::i();
       
        $app->hook('template(registration.view.content-diligence):begin', function () use ($app) {
            $app->view->enqueueStyle('app', 'diligence', 'css/diligence/style.css');
            $this->jsObject['idDiligence'] = 0;
            $entity = self::getrequestedEntity($this);
            $entityDiligence = new EntityDiligence();
            //Verifica se já ouve o envio da avaliação
            $sendEvaluation = EntityDiligence::evaluationSend($entity);
            //Repositório de Diligencia, busca Diligencia pela id da inscrição
            $diligenceRepository = DiligenceRepo::findBy('Diligence\Entities\Diligence',['registration' => $entity->id]);
            //Mostra o prazo de forma diferente a depender do usuario logado
            $term = $entityDiligence->verifyTerm($diligenceRepository, $entity);
            $app->view->enqueueScript('app', 'entity-diligence', 'js/diligence/entity-diligence.js');
            $placeHolder = '';
            $isProponent = $entityDiligence->isProponent($diligenceRepository, $entity); 
            $context = [
                'entity' => $entity,
                'diligenceRepository' => $diligenceRepository,
                'term' => $term,
                'placeHolder' => $placeHolder
            ];
            //Verificando e globalizando se é um avaliador
            $this->jsObject['userEvaluate'] = $entity->canUser('evaluate');
            //Glabalizando se é um proponente
            $this->jsObject['isProponent']  = $isProponent;
            if($isProponent){              
              
                return $this->part('diligence/proponent',['context' => $context, 'sendEvaluation' => $sendEvaluation]);               
            }
            
            $this->part('diligence/tabs-parent',['context' => $context, 'sendEvaluation' => $sendEvaluation] );
        });

        $app->hook('template(opportunity.edit.evaluations-config):begin', function () use ($app) {
            $entity = self::getrequestedEntity($this);
            $this->part('diligence/days', ['entity' => $entity]);
        });

        $app->hook('template(registration.view.registration-sidebar-rigth-value-project):begin', function() use ($app){
            $entity = self::getrequestedEntity($this);
            $this->part('registration-diligence/value-project', ['entity' => $entity]);
        });

        //Hook para mostrar o valor destinado do projeto ao proponente apos a autorização e a publicação do resultado
        $app->hook('template(registration.view.form):end', function() use ($app) {
            $entity = self::getrequestedEntity($this);           
            $authorired = $entity->getMetadata('option_authorized');
            $valueProject = $entity->getMetadata('value_project_diligence');
            if($authorired == 'Sim') {
                $this->part('registration-diligence/info-value-project', ['authorired' => $authorired, 'valueProject' => $valueProject]);
            }
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

        $this->registerRegistrationMetadata('value_project_diligence', [
            'label' =>  i::__('Valor estimado do projeto'),
            'type' => 'string',
            'validations' => [
                "v::positive()" => "a meta de itens deve ser um número positivo"
            ]
        ]);
        
        $this->registerRegistrationMetadata('option_authorized', [
            'label' =>  i::__('Projeto Autorizado?'),
            'type' => 'string',
            'options' => ['Sim', 'Não'],
            'default' => 'Não'
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