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
use Diligence\Entities\AnswerDiligence;

class Module extends \MapasCulturais\Module {
    use \Diligence\Traits\DiligenceSingle;
    function _init () {

        $app = App::i();

        $app->hook('template(registration.view.content-diligence):begin', function () use ($app) {
            if($this->data['entity']->opportunity->use_diligence == 'Não')
                return;

            $app->view->enqueueStyle('app', 'diligence', 'css/diligence/style.css');
            $this->jsObject['idDiligence'] = 0;
            $entity = self::getrequestedEntity($this);

            $entityDiligence = new EntityDiligence();
            //Verifica se já ouve o envio da avaliação
            $sendEvaluation = EntityDiligence::evaluationSend($entity);
            $diligenceAndAnswers = DiligenceRepo::getDiligenceAnswer($entity->id);
            //Repositório de Diligencia, busca Diligencia pela id da inscrição
            $diligenceRepository = DiligenceRepo::findBy('Diligence\Entities\Diligence',['registration' => $entity->id]);
            //Verifica a data limite para resposta contando com dias úteis
           if(isset($diligenceRepository[0]) && count($diligenceRepository) > 0)
           {
                $diligence_days = AnswerDiligence::vertifyWorkingDays($diligenceRepository[0]->sendDiligence, $entity->opportunity->getMetadata('diligence_days'));
           }else{
                $diligence_days = null;
           }
            //Prazo registrado de dias uteis para responder a diligencia
            $this->jsObject['diligence_days'] = $diligence_days;
            
            $app->view->enqueueScript('app', 'entity-diligence', 'js/diligence/entity-diligence.js');
            $placeHolder = '';
            $isProponent = $entityDiligence->isProponent($diligenceRepository, $entity); 
            $context = [
                'entity' => $entity,
                'diligenceRepository' => $diligenceRepository,
                'diligenceDays' => $diligence_days ,
                'placeHolder' => $placeHolder,
                'isProponent' => $isProponent
            ];

            //Verificando e globalizando se é um avaliador
            $this->jsObject['userEvaluate'] = false;
            if($entity->canUser('evaluate') || $app->user->is('superAdmin') )
            {
                $this->jsObject['userEvaluate'] = true;
            }
            //Glabalizando se é um proponente
            $this->jsObject['isProponent']  = $isProponent;

            if($isProponent){
                $app->view->enqueueStyle('app', 'jquery-ui', 'css/diligence/jquery-ui.css');
                $app->view->enqueueScript('app', 'jquery-ui', 'js/diligence/jquery-ui.min.js');
                return $this->part('diligence/proponent',['context' => $context, 'sendEvaluation' => $sendEvaluation, 'diligenceAndAnswers' => $diligenceAndAnswers]);
            }

            if($entity->opportunity->getMetadata('use_diligence') == 'multiple') {
                $app->view->enqueueScript('app', 'diligence', 'js/diligence/diligence.js');
                $app->view->enqueueScript('app', 'multi-diligence', 'js/diligence/multi-diligence.js');
                $app->view->enqueueStyle('app', 'jquery-ui', 'css/diligence/jquery-ui.css');
                $app->view->enqueueScript('app', 'jquery-ui', 'js/diligence/jquery-ui.min.js');

                $this->part('diligence/tabs-parent',['context' => $context, 'sendEvaluation' => $sendEvaluation, 'diligenceAndAnswers' => $diligenceAndAnswers] );
            }else{
                $app->view->enqueueScript('app', 'diligence', 'js/diligence/diligence.js');
            }
        });

        $app->hook('template(opportunity.edit.evaluations-config):begin', function () use ($app) {
            $entity = self::getrequestedEntity($this);
            $this->part('opportunity/diligence-config-options', ['opportunity' => $entity]);
        });

        $app->hook('template(registration.view.registration-sidebar-rigth-value-project):begin', function() use ($app){
            $entity = self::getrequestedEntity($this);
            if($entity->opportunity->use_diligence == 'Não')
                return;
            $this->part('registration-diligence/value-project', ['entity' => $entity]);
        });

        //Hook para mostrar o valor destinado do projeto ao proponente apos a autorização e a publicação do resultado
        $app->hook('template(registration.view.form):end', function() use ($app) {
            $entity = self::getrequestedEntity($this);
            if($entity->opportunity->use_diligence == 'Não')
                return;
            $authorized = $entity->getMetadata('option_authorized');
            $valueProject = $entity->getMetadata('value_project_diligence');
            if($authorized == 'Sim') {
                $this->part('registration-diligence/info-value-project', ['authorized' => $authorized, 'valueProject' => $valueProject]);
            }
        });

        //Hook para antes de upload para um logica para diligência
        $app->hook('POST(registration.upload):before', function() use ($app) {
            $registration = $this->requestedEntity;
            //Se Files é diferente de null
            //Se Files tem o indice com o grupo da diligencia
            //Se da inscrição é o mesmo quem está logado enviando a requisição.
            //$registration->getOwnerUser() == $app->getUser()
            if(
                isset($_FILES) && 
                array_key_exists('file-diligence', $_FILES)
            ) {
                $app->disableAccessControl();
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

        $this->registerOpportunityMetadata('use_diligence', [
            'label' =>  i::__('Usar diligência?'),
            'description' => i::__('Configura o tipo de diligência a ser usada'),
            'type' => 'select',
            'options' => [
                'Não',
                'simple' => i::__('Diligência Simples'),
                'multiple' => i::__('Diligência Múltipla'),
            ],
            'default' => 'Não',
            'required' => true,
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


        $app->registerFileGroup(
            'registration',
            new Definitions\FileGroup(
                'file-diligence',
                ['application/pdf','image/(gif|jpeg|pjpeg|png)'],
                'O arquivo não e valido'
            )
        );

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
