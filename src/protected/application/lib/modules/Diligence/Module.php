<?php

namespace Diligence;

use Doctrine\ORM\Query\Expr;
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
        $module = $this;

        $app->hook('template(registration.view.content-diligence):begin', function () use ($app, $module) {
            if($this->data['entity']->opportunity->use_diligence == 'Não')
                return;

            $app->view->enqueueStyle('app', 'diligence', 'css/diligence/style.css');
            $this->jsObject['idDiligence'] = 0;
            $entity = self::getRequestedEntity($this);

            $entityDiligence = new EntityDiligence();
            //Verifica se já ouve o envio da avaliação
            $sendEvaluation = EntityDiligence::evaluationSend($entity);
            $diligenceAndAnswers = DiligenceRepo::getDiligenceAnswer($entity->id);
            //Repositório de Diligencia, busca Diligencia pela id da inscrição
            $diligenceRepository = DiligenceRepo::findBy('Diligence\Entities\Diligence',['registration' => $entity->id]);
            //Verifica a data limite para resposta contando com dias úteis
            if(isset($diligenceRepository[0]) && count($diligenceRepository) > 0) {
                $diligence_days = AnswerDiligence::vertifyWorkingDays($diligenceRepository[0]->sendDiligence, $entity->opportunity->getMetadata('diligence_days'));
            }else{
                $diligence_days = null;
            }
            //Prazo registrado de dias uteis para responder a diligencia
            $this->jsObject['diligence_days'] = $diligence_days;

            /**
             * @var $opportunity \MapasCulturais\Entities\Opportunity
             */
            $opportunity = $this->data['entity']->opportunity;

            $app->view->enqueueScript('app', 'entity-diligence', 'js/diligence/entity-diligence.js');
            $placeHolder = '';
            $isProponent = $entityDiligence->isProponent($diligenceRepository, $entity);
            $isEvaluator = $module->isEvaluator($opportunity, $this->data['entity']);
            $context = [
                'entity' => $entity,
                'diligenceRepository' => $diligenceRepository,
                'diligenceDays' => $diligence_days ,
                'placeHolder' => $placeHolder,
                'isProponent' => $isProponent,
                'isEvaluator' => $isEvaluator,
            ];

            //Glabalizando se é um proponente
            $this->jsObject['isProponent']  = $isProponent;
            //Verificando e globalizando se é um avaliador
            $this->jsObject['isEvaluator'] = $isEvaluator;

            $app->view->enqueueStyle('app', 'jquery-ui', 'css/diligence/jquery-ui.css');
            $app->view->enqueueScript('app', 'jquery-ui', 'js/diligence/jquery-ui.min.js');
            $app->view->enqueueScript('app', 'diligence', 'js/diligence/diligence.js');

            if($isProponent){
                return $this->part('diligence/proponent',['context' => $context, 'sendEvaluation' => $sendEvaluation, 'diligenceAndAnswers' => $diligenceAndAnswers]);
            }
            if($isEvaluator) {
                $app->view->enqueueScript('app', 'multi-diligence', 'js/diligence/multi-diligence.js');
                $this->part('diligence/tabs-parent',['context' => $context, 'sendEvaluation' => $sendEvaluation, 'diligenceAndAnswers' => $diligenceAndAnswers] );
            }
        });

        $app->hook('template(opportunity.edit.evaluations-config):begin', function () use ($app, $module) {
            $entity = self::getRequestedEntity($this);
            $isEditableConfig = $module::isEditableConfig($entity);

            $app->view->enqueueStyle('app', 'form-config', 'css/diligence/form-config.css');
            $app->view->enqueueScript(
                'app',
                'diligence-config-options',
                'js/diligence/diligence-config-options.js'
            );
            $this->part('opportunity/diligence-config-options', [
                'opportunity' => $entity,
                'isEditableConfig' => $isEditableConfig
            ]);
        });

        $app->hook('template(registration.view.registration-sidebar-rigth-value-project):begin', function() use ($app){
            $entity = self::getRequestedEntity($this);
            if($entity->opportunity->use_multiple_diligence === 'Não')
                $this->part('registration-diligence/value-project', ['entity' => $entity]);
        });

        //Hook para mostrar o valor destinado do projeto ao proponente apos a autorização e a publicação do resultado
        $app->hook('template(registration.view.form):end', function() use ($app) {
            $entity = self::getRequestedEntity($this);
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
            'description' => i::__('Configura se deve usar diligência'),
            'type' => 'select',
            'options' => ['Sim', 'Não'],
            'default' => 'Não',
            'required' => true,
        ]);
        $this->registerOpportunityMetadata('use_multiple_diligence', [
            'label' =>  i::__('Usar diligência múltipla?'),
            'description' => i::__('Configura se deve usar diligência múltipla'),
            'type' => 'select',
            'options' => ['Sim', 'Não'],
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

    private function isEvaluator(Entities\Opportunity $opportunity, Entities\Registration $registration): bool
    {
        $app = App::i();

        if($opportunity->owner === $app->user->profile)
            return true;

        /**
         * Verifica se o usuário tem permissão direta de avaliar a inscrição
         * sem considerar o papel de Admin ou superior na plataforma
         */
        $evaluateAction = $app->repo('RegistrationPermissionCache')->findBy([
            'user' => $app->user,
            'action' => 'evaluate',
            'owner' => $registration,
        ]);
        if(count($evaluateAction) > 0)
            return true;

        $queryBuilder = $app->em->createQueryBuilder()
            ->select('a')
            ->from('\MapasCulturais\Entities\Agent', 'a')
            ->innerJoin('\MapasCulturais\Entities\AgentRelation', 'ar')
            ->where("ar.objectId = {$opportunity->id}")
            ->andWhere("ar.agent = a")
            ->andWhere("ar.group = 'group-admin'")
            ->andWhere("ar.status = 1");
        $query = $queryBuilder->getQuery();
        /**
         * @var $agentsAdmin Entities\Agent[]
         */
        $opportunityAdminAgents = $query->getResult();

        // verifica se o usuário tem permissão sobre os agentes administradores da oportunidade
        foreach ($opportunityAdminAgents as $agent) {
            if($agent->canUser('control'))
                return true;
        }

        return false;
    }

    /**
     * Verifica se é pode editar as configurações de deligência em uma oportunidade
     */
    private static function isEditableConfig(Entities\Opportunity $opportunity): bool
    {
        if($opportunity->publishedRegistrations || $opportunity->publishedOpinions)
            return false;

        // Traz as diligências referentes a essa oportunidade
        $app = App::i();
        $qb = $app->em->createQueryBuilder()
            ->select('d')
            ->from('Diligence\Entities\Diligence', 'd')
            ->innerJoin('\MapasCulturais\Entities\Registration', 'r', Expr\Join::WITH, 'd.registration = r')
            ->where("r.opportunity = :opportunity")
            ->setParameter('opportunity', $opportunity);
        $query = $qb->getQuery();
        $diligences = $query->getResult();

        // Se existir diligência retorna 'false'
        if(count($diligences) > 0)
            return false;

        return true;
    }
}
