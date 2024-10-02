<?php

namespace Diligence;

use Doctrine\ORM\Query\Expr;
use MapasCulturais\App,
    MapasCulturais\i,
    MapasCulturais\Entities,
    MapasCulturais\Definitions;
    
require __DIR__.'/Traits/DiligenceSingle.php';
require __DIR__.'/Service/DiligenceInterface.php';
require __DIR__.'/Repositories/Diligence.php';
require __DIR__.'/Entities/Diligence.php';
require __DIR__.'/Entities/DiligenceFile.php';
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
            $registration = $this->controller->requestedEntity;

            $entityDiligence = new EntityDiligence();
            //Verifica se já ouve o envio da avaliação
            $sendEvaluation = EntityDiligence::evaluationSend($registration);
            $diligenceAndAnswers = DiligenceRepo::getDiligenceAnswer($registration->id);
            //Repositório de diligência, busca Diligencia pelo id da inscrição
            $diligencesByRegistration = DiligenceRepo::findBy(EntityDiligence::class, ['registration' => $registration]);
            //Verifica a data limite para resposta contando com dias úteis
            if(isset($diligencesByRegistration[0]) && count($diligencesByRegistration) > 0) {
                $diligence_days = AnswerDiligence::setNumberDaysAnswerDiligence(
                    $diligencesByRegistration[0]->sendDiligence,
                    $registration->opportunity->getMetadata('diligence_days'),
                    $registration->opportunity->getMetadata('type_day_response_diligence')
                );
            }else{
                $diligence_days = null;
            }
            //Prazo registrado de dias uteis para responder à diligência
            $this->jsObject['diligence_days'] = $diligence_days;

            /**
             * @var $opportunity \MapasCulturais\Entities\Opportunity
             */
            $opportunity = $this->data['entity']->opportunity;

            $app->view->enqueueScript('app', 'entity-diligence', 'js/diligence/entity-diligence.js');
            $placeHolder = '';
            $isProponent = $entityDiligence->isProponent($diligencesByRegistration, $registration);
            $isEvaluator = $module->isEvaluator($opportunity, $this->data['entity']);
            $context = [
                'entity' => $registration,
                'diligenceRepository' => $diligencesByRegistration,
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
            //Todos os assets para multi diligencia
            self::multiPublishAssets();
            if($isProponent){
                return $this->part('diligence/proponent',['context' => $context, 'sendEvaluation' => $sendEvaluation, 'diligenceAndAnswers' => $diligenceAndAnswers]);
            }
            if($isEvaluator) {
                $this->part('diligence/tabs-parent',['context' => $context, 'sendEvaluation' => $sendEvaluation, 'diligenceAndAnswers' => $diligenceAndAnswers] );
            }
        });

        $app->hook('template(opportunity.edit.evaluations-config):begin', function () use ($app, $module) {
            $entity = $this->controller->requestedEntity;
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

        $app->hook('template(opportunity.single.tab-evaluations):after', function () use ($app) {
            if($this->data['entity']->use_diligence === 'Sim') {
                $this->part('opportunity/tab-diligences');
            }
        });

        $app->hook('template(opportunity.single.tabs-content):end', function () use ($app) {
            if($this->data['entity']->use_diligence === 'Sim' && $this->data['entity']->canUser('@control')) {
                $qb = $app->em->createQueryBuilder();

                $registrations = $qb
                    ->select('r')
                    ->from('\MapasCulturais\Entities\Registration', 'r')
                    ->innerJoin('\Diligence\Entities\Diligence', 'd', 'WITH', 'd.registration = r')
                    ->where($qb->expr()->in('r.opportunity', '?1'))
                    ->groupBy('r.id')
                    ->having('COUNT(d) > 0')
                    ->setParameter(1, $this->data['entity']->id)
                    ->getQuery()
                    ->getResult();

                $registrationsWithDiligences = [];
                foreach ($registrations as $registration) {
                    $diligences = $app->em->createQueryBuilder()
                        ->select('d')
                        ->from('\Diligence\Entities\Diligence', 'd')
                        ->where('d.registration = :registration')
                        ->setParameter('registration', $registration->id)
                        ->getQuery()
                        ->getResult();

                    $registrationsWithDiligences[] = [
                        'registration' => $registration,
                        'diligences' => $diligences
                    ];
                }

                $evaluators = $app->em->createQueryBuilder()
                    ->select('a')
                    ->from('\MapasCulturais\Entities\Agent', 'a')
                    ->join('\Diligence\Entities\Diligence', 'd', 'WITH', 'd.openAgent = a')
                    ->join('\MapasCulturais\Entities\Registration', 'r', 'WITH', 'r = d.registration')
                    ->where('r.opportunity = :opportunity')
                    ->setParameter('opportunity', $this->data['entity']->id)
                    ->getQuery()
                    ->getResult();

                $app->view->enqueueScript('app','opp-diligence','js/diligence/opportunity-diligence.js');

                $this->part('opportunity/diligence-content', ['registrationsWithDiligences' => $registrationsWithDiligences, 'evaluators' => $evaluators]);
            }
        });

        $app->hook('template(registration.view.registration-sidebar-rigth-value-project):begin', function() use ($app){
            $entity = $this->controller->requestedEntity;
            if($entity->opportunity->use_diligence === 'Sim' && (is_null($entity->opportunity->use_multiple_diligence) || $entity->opportunity->use_multiple_diligence === 'Não'))
                $this->part('registration-diligence/value-project', ['entity' => $entity]);
        });

        $app->hook('template(registration.view.registration-sidebar-rigth):end', function() use ($app, $module){
            Module::publishAssets();
            $entity = $this->controller->requestedEntity;
            //A pessoa dona da inscrição tem acesso a visualizar o TADO    
            $ownerRegistration = $app->user->profile == $entity->owner ? true : false;
            //Se é avaliador
            $isEvaluation = $module->isEvaluator($entity->opportunity, $entity);
            if ($isEvaluation || $ownerRegistration)
            {
                $tado = DiligenceRepo::getTado($entity);
                $app->view->enqueueStyle('app', 'multi-css', 'css/diligence/multi.css');
                $app->view->enqueueScript('app', 'multi-js', 'js/multi/multi.js');
                $this->part('multi/accountability-actions', [
                    'reg'           => $entity,
                    'app'           => $app,
                    'tado'          => $tado,
                    'isEvaluation'  => $isEvaluation
                ]);
            };

        });

        //Hook para mostrar o valor destinado do projeto ao proponente apos a autorização e a publicação do resultado
        $app->hook('template(registration.view.form):end', function() use ($app) {
            $entity = $this->controller->requestedEntity;
            if($entity->opportunity->use_diligence == 'Não')
                return;
            $authorized = $entity->getMetadata('option_authorized');
            $valueProject = $entity->getMetadata('value_project_diligence');
            if($authorized == 'Sim') {
                $this->part('registration-diligence/info-value-project', ['authorized' => $authorized, 'valueProject' => $valueProject]);
            }
        });

        //Hook para antes de upload para um logica para diligência
        $app->hook('POST(diligence.upload):before', function () use ($app) {
            $diligence = DiligenceRepo::findBy('Diligence\Entities\Diligence', ['id' => $this->data["id"]]);

            //Se Files é diferente de null
            //Se Files tem o indice com o grupo da diligencia
            //Se da inscrição é o mesmo quem está logado enviando a requisição.
            if (
                isset($_FILES) &&
                array_key_exists('answer-diligence', $_FILES) &&
                $diligence[0]->getOwnerUser() == $app->getUser()
            ) {
                $app->disableAccessControl();
            }
        });

        $app->hook('doctrine.emum(object_type).values', function (&$result) {
            $result["Diligence"] = 'Diligence\Entities\Diligence';
        });

        $app->hook('controller(opportunity).partial(report-evaluations)', function ($template, &$data) use ($app) {
            $opportunity = $this->requestedEntity;
            $useDiligence = $opportunity->use_diligence == 'Sim';
            if(!$useDiligence)
                return;

            $useMultipleDiligence = $opportunity->use_multiple_diligence === 'Sim' ? true : false;
            if (!$useMultipleDiligence) {
                $data['cfg']['evaluation']->columns['projectValue'] = (object)[
                    'label' => i::__('Valor destinado ao projeto'),
                    'getValue' => function (int $registration): ?string
                    {
                        $app = \MapasCulturais\App::i();
                        $metadata = $app->repo('RegistrationMeta')->findBy([
                            'owner' => $registration,
                            'key' => 'value_project_diligence',
                        ]);
                        return isset($metadata[0]) ? 'R$ ' . $metadata[0]->value : '--';
                    }
                ];
            }
        });

        $app->hook('template(panel.index.content.registration):before', function() {
            $this->part('multi/session');
        });

        $app->hook('template(panel.index.content.registration):after', function() {
           unset($_SESSION['error']);
        });
    }

    function register () {
        $app = App::i();
        $app->registerController('diligence', Controllers\Controller::class);
        $app->registerController('tado', Controllers\Tado::class);
        $app->registerController('refo', Controllers\Refo::class);
        //Registrar metadata na tabela opportunity
        $this->registerOpportunityMetadata('diligence_days', [
            'label' => i::__('Dias para resposta da diligência'),
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
        $this->registerOpportunityMetadata('type_day_response_diligence', [
            'label' =>  i::__('Tipo de dia para resposta da diligência:'),
            'description' => i::__('Configura o tipo de dia que será usado para a resposta da diligência'),
            'type' => 'select',
            'options' => ['Úteis', 'Corridos'],
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
            'diligence',
            new Definitions\FileGroup(
                'answer-diligence',
                ['application/pdf', 'image/(gif|jpeg|pjpeg|png)'],
                'O arquivo não e valido'
            )
        );

        $app->registerFileGroup(
            'registration',
            new Definitions\FileGroup('financial-report-accountability', ['application/pdf'], 'O arquivo não é válido', false, null, true)
        );

        $this->registerRegistrationMetadata('situacion_diligence', [
            'label' =>  i::__('Situação do REFO'),
            'type' => 'select',
            'options' => ['approved', 'partially', 'disapproved']
        ]);
    }

    /**
     * Publica todos os assets (css/js)
     *
     */
    static protected function publishAssets()
    {
        $app = App::i();

        $app->view->enqueueStyle('app', 'secultalert', 'https://raw.githubusercontent.com/secultce/plugin-Recourse/main/assets/css/recourse/secultce.min.css');
        $app->view->enqueueScript('app','sweetalert2','https://cdn.jsdelivr.net/npm/sweetalert2@11.10.0/dist/sweetalert2.all.min.js');

    }

    /**
     * Todos os assets que serão usados na multi diligencia
     * @return void
     */
    static protected function multiPublishAssets()
    {
        $app = App::i();
        $app->view->enqueueScript('app', 'diligence-message', 'js/diligence/diligenceMessage.js');
        $app->view->enqueueScript('app', 'entity-diligence', 'js/diligence/entity-diligence.js');
        $app->view->enqueueScript('app', 'multi-diligence', 'js/diligence/multi-diligence.js');
        $app->view->enqueueStyle('app', 'multi-diligence', 'css/diligence/multi.css');
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
