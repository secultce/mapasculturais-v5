<?php

namespace MapasCulturais\Controllers;

use Diligence\Entities\Tado;
use MapasCulturais\ApiQuery;
use MapasCulturais\App;
use MapasCulturais\Entities\Space;
use MapasCulturais\Entities\Seal;
use Diligence\Repositories\Diligence as DiligenceRepo;
use MapasCulturais\Utils;
use MapasCulturais\i;

/**
 * User Panel Controller
 *
 * By default this controller is registered with the id 'panel'.
 *
 */
class Panel extends \MapasCulturais\Controller {

    function POST_setUrlCookie() {
        if( is_array($this->data) && isset($this->data['redirect_url_auth']) ) {
            setcookie('mapasculturais_user_nav_url', $this->data['redirect_url_auth'], 0 , '/');
        }
    }

    /**
     * Render the user panel.
     *
     * This method requires authentication and renders the template 'panel/index'
     *
     * <code>
     * // creates the url to this action
     * $url = $app->createUrl('panel');
     * </code>
     *
     */
    function GET_index(){
        $this->requireAuthentication();

        $app = App::i();

        $count = new \stdClass();

        $count->spaces          = $app->controller('space')->apiQuery(['@count'=>1, 'user' => 'EQ(' . $app->user->id . ')']);
        $count->agents          = $app->controller('agent')->apiQuery(['@count'=>1, 'user' => 'EQ(' . $app->user->id . ')']);
        $count->events          = $app->controller('event')->apiQuery(['@count'=>1, 'user' => 'EQ(' . $app->user->id . ')']);
        $count->projects        = $app->controller('project')->apiQuery(['@count'=>1, 'user' => 'EQ(' . $app->user->id . ')']);
        $count->opportunities   = $app->controller('opportunity')->apiQuery(['@count'=>1, 'user' => 'EQ(' . $app->user->id . ')']);
        $count->subsite         = $app->controller('subsite')->apiQuery(['@count'=>1]);
        $count->seals           = $app->controller('seal')->apiQuery(['@count'=>1, 'user' => 'EQ(' . $app->user->id . ')']);

        $this->render('index', ['count'=>$count]);
    }

    function GET_listUsers(){
        $this->requireAuthentication();
        $app = App::i();
        $subsite_id = $app->getCurrentSubsiteId();

        $roles = $app->getRoles();

        if (!$app->user->is('admin')) $app->user->checkPermission('addRole'); // dispara exceção se não for admin ou sueradmin

        $Repo = $app->repo('User');

        $vars = array();

        foreach ($roles as $roleSlug => $roleInfo) {
            $vars['list_' . $roleSlug] = $Repo->getByRole($roleSlug,$subsite_id);

            if ($roleSlug == 'superAdmin') {
                $roles[$roleSlug]['permissionSuffix'] = 'SuperAdmin';
            } elseif ($roleSlug == 'admin') {
                $roles[$roleSlug]['permissionSuffix'] = 'Admin';
            } else {
                $roles[$roleSlug]['permissionSuffix'] = '';
            }

        }

        $vars['roles'] = $roles;
        $this->render('list-users', $vars);
    }

    protected function countEntity($entityName){
        $app = App::i();
        $entityClass = '\\MapasCulturais\\Entities\\' . $entityName;
        $dql = "SELECT COUNT(e.id) FROM $entityClass e JOIN e.owner o WHERE o.user = :user AND e.status >= 0";
        $query = $app->em->createQuery($dql);
        $query->setParameter('user', $app->user);
        $count = $query->getSingleScalarResult();
        $padded = str_pad($count, 2, '0', STR_PAD_LEFT);
        return $padded;
    }

    protected function _getUser(){
        $app = App::i();
        $user = null;
        if($app->user->is('admin') && key_exists('userId', $this->data)){
            $user = $app->repo('User')->find($this->data['userId']);


        }elseif($app->user->is('admin') && key_exists('agentId', $this->data)){
            $agent = $app->repo('Agent')->find($this->data['agentId']);
            $user = $agent->user;
        }
        if(!$user)
            $user = $app->user;

        return $user;
    }

    function GET_requireAuth(){
        $this->requireAuthentication();
        $this->render('require-authentication');
    }

    /**
     * Render the agent list of the user panel.
     *
     * This method requires authentication and renders the template 'panel/agents'
     *
     * <code>
     * // creates the url to this action
     * $url = $app->createUrl('panel', 'agents');
     * </code>
     *
     */
    function GET_agents(){
        $this->requireAuthentication();
        $user = $this->_getUser();

        $this->render('agents', ['user' => $user]);
    }

    protected function renderList($viewName, $entityName, $entityFields){
        $this->requireAuthentication();

        $user = $this->_getUser();

        $app = App::i();

        $user_filter = 'EQ(' . $user->id . ')';

        $query = [
            '@select' => $entityFields,
            '@files' => '(avatar.avatarSmall):url',
            'user' => $user_filter,
            'status' => 'EQ(' . Space::STATUS_ENABLED . ')',
            '@limit' => 50,
            '@order' => ''
        ];

        if(isset($this->data['keyword'])){
            $query['@keyword'] = $this->data['keyword'];
        }

        if(isset($this->data['order'])){
            $query['@order'] = $this->data['order'];
        } else {
            $query['@order'] = 'name ASC';
        }

        if(isset($this->data['page'])){
            $query['@page'] = intval($this->data['page']);
        } else{
            $query['@page'] = 1;
        }

        $controller = $app->controller($entityName);

        $enabled = $controller->apiQuery($query);
        $meta = $controller->lastQueryMetadata;
        $draft   = $controller->apiQuery(['@select' => $entityFields, '@files' => '(avatar.avatarSmall):url', 'user' => $user_filter, 'status' => 'EQ(' . Space::STATUS_DRAFT . ')', '@permissions' => 'view']);
        $trashed = $controller->apiQuery(['@select' => $entityFields, '@files' => '(avatar.avatarSmall):url', 'user' => $user_filter, 'status' => 'EQ(' . Space::STATUS_TRASH . ')', '@permissions' => 'view']);
        $archivedMethod = 'archived'.$entityName;
        $archived = $app->user->$archivedMethod;

        $enabled = json_decode(json_encode($enabled));
        $draft   = json_decode(json_encode($draft));
        $trashed = json_decode(json_encode($trashed));
        $archived= json_decode(json_encode($archived));

        $this->render($viewName, ['enabled' => $enabled, 'draft' => $draft, 'trashed' => $trashed, 'meta'=>$meta, 'archived' => $archived]);
    }

    /**
     * Render the space list of the user panel.
     *
     * This method requires authentication and renders the template 'panel/spaces'
     *
     * <code>
     * // creates the url to this action
     * $url = $app->createUrl('panel', 'spaces');
     * </code>
     *
     */
    function GET_spaces(){
        $fields = ['name', 'type', 'status', 'terms', 'endereco', 'singleUrl', 'originSiteUrl', 'editUrl', 'destroyUrl',
                   'deleteUrl', 'undeleteUrl', 'publishUrl', 'unpublishUrl', 'acessibilidade', 'createTimestamp','archiveUrl','unarchiveUrl', 'permissionTo.destroy'];
        $app = App::i();
        $app->applyHook('controller(panel).extraFields(space)', [&$fields]);
        $this->renderList('spaces', 'space', implode(',', $fields));
    }

    /**
     * Render the event list of the user panel.
     *
     * This method requires authentication and renders the template 'panel/events'
     *
     * <code>
     * // creates the url to this action
     * $url = $app->createUrl('panel', 'events');
     * </code>
     *
     */
    function GET_events(){
        $fields = ['name', 'type', 'status', 'terms', 'classificacaoEtaria', 'singleUrl', 'destroyUrl',
                   'editUrl', 'deleteUrl', 'undeleteUrl', 'publishUrl', 'unpublishUrl', 'createTimestamp','archiveUrl','unarchiveUrl', 'permissionTo.destroy'];
        $app = App::i();
        $app->applyHook('controller(panel).extraFields(event)', [&$fields]);
        $this->renderList('events', 'event', implode(',', $fields));
    }

    /**
     * Render the project list of the user panel.
     *
     * This method requires authentication and renders the template 'panel/projects'
     *
     * <code>
     * // creates the url to this action
     * $url = $app->createUrl('panel', 'projects');
     * </code>
     *
     */
    function GET_projects(){
        $this->requireAuthentication();
        $user = $this->_getUser();

        $this->render('projects', ['user' => $user]);
    }
    
    /**
     * Render the opportunities list of the user panel.
     *
     * This method requires authentication and renders the template 'panel/opportunity'
     *
     * <code>
     * // creates the url to this action
     * $url = $app->createUrl('panel', 'opportunities');
     * </code>
     *
     */
    function GET_opportunities(){
        $this->requireAuthentication();
        $user = $this->_getUser();

        $this->render('opportunities', ['user' => $user]);
    }

    /**
     * Render the seal list of the user panel.
     *
     * This method requires authentication and renders the template 'panel/seals'
     *
     * <code>
     * // creates the url to this action
     * $url = $app->createUrl('panel', 'seals');
     * </code>
     *
     */
    function GET_seals(){
    	$this->requireAuthentication();
    	$user = $this->_getUser();

        $app = App::i();

        $query = new ApiQuery(Seal::class, ['@permissions' => '@control', '@order' => 'name ASC']);
        $seal_ids = $query->findIds();
        
        if ($seal_ids) {
            $seals = $app->repo('Seal')->findBy(['id' => $seal_ids]);
        } else {
            $seals = [];
        }

        $this->render('seals', ['user' => $user, 'seals' => $seals]);
    }

    /**
     * Render the registration list of the user panel.
     *
     * This method requires authentication and renders the template 'panel/registrations'
     *
     * <code>
     * // creates the url to this action
     * $url = $app->createUrl('panel', 'registrations');
     * </code>
     *
     */
    function GET_registrations(){
        $this->requireAuthentication();
        $user = $this->_getUser();

        $this->render('registrations', ['user' => $user]);
    }

    /**
     * Render the integrations list of the user panel.
     *
     * This method requires authentication and renders the template 'panel/apps'
     *
     * <code>
     * // creates the url to this action
     * $url = $app->createUrl('panel', 'apps');
     * </code>
     *
     */
    function GET_apps(){
        $this->requireAuthentication();
        $user = $this->_getUser();
        $enabledApps = App::i()->repo('UserApp')->findBy(['user' => $user, 'status' => \MapasCulturais\Entities\UserApp::STATUS_ENABLED]);
        $thrashedApps = App::i()->repo('UserApp')->findBy(['user' => $user, 'status' => \MapasCulturais\Entities\UserApp::STATUS_TRASH]);
        $this->render('apps', ['user' => $user, 'enabledApps' => $enabledApps, 'thrashedApps' => $thrashedApps]);
    }

    /**
     * Render the subsite list of the user panel (Only SuperAdmin, Admin and Owner Subsite panel).
     *
     * This method requires authentication and renders the template 'panel/subsite'
     *
     * <code>
     * // creates the url to this action
     * $url = $app->createUrl('panel', 'registrations');
     * </code>
     *
     */
    function GET_subsite(){
        $this->requireAuthentication();
        $user = $this->_getUser();

        $this->render('subsite', ['user' => $user]);
    }

    /**
     * Render the user management of the user panel (Only SuperAdmin, Admin and Owner Subsite panel).
     *
     * This method requires authentication and renders the template 'panel/user-management'
     *
     * <code>
     * // creates the url to this action
     * $url = $app->createUrl('panel', 'userManagement');
     * </code>
     *
     */
    function GET_userManagement() {
        $this->requireAuthentication();
        $app = App::i();
        if(!isset($this->getData['userId'])) {
            if(isset($this->getData['admin'])) {
                $this->render('user-management', ['admin' => true]);
            } else {
                $this->render('user-management');
            }
        } else {
            $user = $app->repo('User')->find($this->getData['userId']);
            if (!$user) {
                $app->pass();
            }

            $roles = $app->repo('User')->getRoles($this->getData['userId']);
            $can_manage_entity_metadata = $this->_canManageEntityMetadata();

            $this->render('user-management', [
                'user' => $user,
                'roles' => $roles,
                'canManageEntityMetadata' => $can_manage_entity_metadata,
            ]);
        }
    }

    /**
     * Returns the metadata editor for one entity in the user-management modal.
     */
    function GET_entityMetadata() {
        $this->_requireEntityMetadataManager();
        $app = App::i();
        $data = $this->getData;
        $user = $this->_getManagedMetadataUser($data);
        $entity_type = isset($data['entityType']) && is_scalar($data['entityType'])
            ? strtolower((string) $data['entityType'])
            : '';
        $entity = $this->_getManagedMetadataEntity($user, $entity_type, $data);
        $metadata_class = $entity->getMetadataClassName();
        $metadata = $app->repo($metadata_class)->findBy(
            ['owner' => $entity],
            ['key' => 'ASC', 'id' => 'ASC']
        );
        $types = $this->_getManagedMetadataEntityTypes();

        $html = $app->view->partialRender('user-management/user-info/info-metadata', [
            'user' => $user,
            'entity' => $entity,
            'entityType' => $entity_type,
            'metadata' => $metadata,
            'registeredMetadata' => $entity->getRegisteredMetadata(null, true),
        ], true);

        $this->json([
            'success' => true,
            'title' => sprintf(i::__('Metadados de %s #%d'), $types[$entity_type]['label'], $entity->id),
            'html' => $html,
        ]);
    }

    /**
     * Updates metadata belonging to an Agent, Space, Event, Project or
     * Opportunity from the user-management panel.
     */
    function POST_entityMetadata() {
        $this->_requireEntityMetadataManager();
        $app = App::i();
        $data = $this->postData;
        $user = $this->_getManagedMetadataUser($data);
        $entity_type = isset($data['entityType']) && is_scalar($data['entityType'])
            ? strtolower((string) $data['entityType'])
            : '';
        $entity = $this->_getManagedMetadataEntity($user, $entity_type, $data);
        $metadata_class = $entity->getMetadataClassName();
        $meta_id = isset($this->postData['metaId']) ? filter_var($this->postData['metaId'], FILTER_VALIDATE_INT) : false;

        if (!$meta_id) {
            $this->errorJson(i::__('A criação de novas chaves de metadados não é permitida por esta ferramenta.'), 400);
        }

        $metadata = $app->repo($metadata_class)->find($meta_id);
        if (!$metadata || !$metadata->owner || $metadata->owner->id !== $entity->id) {
            $this->errorJson(i::__('Metadado não encontrado para esta entidade.'), 404);
        }

        if (array_key_exists('key', $this->postData)
            && (!is_scalar($this->postData['key']) || (string) $this->postData['key'] !== $metadata->key)
        ) {
            $this->errorJson(i::__('Não é permitido renomear chaves de metadados.'), 400);
        }

        if (array_key_exists('value', $this->postData)) {
            if (!is_scalar($this->postData['value']) && !is_null($this->postData['value'])) {
                $this->errorJson(i::__('O valor do metadado deve ser um texto.'), 400);
            }
            $metadata->value = is_null($this->postData['value']) ? null : (string) $this->postData['value'];
        }

        // Entity metadata delegates modify permission inconsistently between
        // entity types. Authorization and ownership were checked
        // above; persisting directly keeps Doctrine lifecycle hooks intact.
        $app->em->persist($metadata);
        $app->em->flush();

        $this->json(['success' => true, 'id' => $metadata->id]);
    }

    /**
     * Deletes metadata belonging to one of the supported entity types.
     */
    function POST_deleteEntityMetadata() {
        $this->_requireEntityMetadataManager();
        $app = App::i();
        $data = $this->postData;
        $user = $this->_getManagedMetadataUser($data);
        $entity_type = isset($data['entityType']) && is_scalar($data['entityType'])
            ? strtolower((string) $data['entityType'])
            : '';
        $entity = $this->_getManagedMetadataEntity($user, $entity_type, $data);
        $metadata_class = $entity->getMetadataClassName();
        $meta_id = isset($this->postData['metaId']) ? filter_var($this->postData['metaId'], FILTER_VALIDATE_INT) : false;

        if (!$meta_id) {
            $this->errorJson(i::__('Metadado inválido.'), 400);
        }

        $metadata = $app->repo($metadata_class)->find($meta_id);
        if (!$metadata || !$metadata->owner || $metadata->owner->id !== $entity->id) {
            $this->errorJson(i::__('Metadado não encontrado para esta entidade.'), 404);
        }

        // Calling Metadata::delete() delegates "remove" to the owner. Profile
        // agents deliberately deny that action, so remove only the authorized
        // metadata row directly and retain Doctrine lifecycle callbacks.
        $app->em->remove($metadata);
        $app->em->flush();

        $this->json(['success' => true]);
    }

    private function _getManagedMetadataUser(array $data) {
        $app = App::i();
        $user_id = isset($data['userId']) ? filter_var($data['userId'], FILTER_VALIDATE_INT) : false;
        $user = $user_id ? $app->repo('User')->find($user_id) : null;

        if (!$user) {
            $this->errorJson(i::__('Usuário não encontrado.'), 404);
        }

        return $user;
    }

    private function _getManagedMetadataEntity($user, $entity_type, array $data) {
        $app = App::i();
        $types = $this->_getManagedMetadataEntityTypes();
        $entity_id = isset($data['entityId']) ? filter_var($data['entityId'], FILTER_VALIDATE_INT) : false;

        if (!$entity_id || !isset($types[$entity_type])) {
            $this->errorJson(i::__('Entidade de metadados inválida.'), 400);
        }

        $entity = $app->repo($types[$entity_type]['class'])->find($entity_id);
        if (!$entity || !$entity->getOwnerUser() || $entity->getOwnerUser()->id !== $user->id) {
            $this->errorJson(i::__('Entidade não encontrada para este usuário.'), 404);
        }

        if (!$entity->canUser('modify')) {
            $this->errorJson(i::__('Você não pode gerenciar os metadados desta entidade.'), 403);
        }

        return $entity;
    }

    private function _getManagedMetadataEntityTypes() {
        return [
            'agent' => ['class' => 'MapasCulturais\\Entities\\Agent', 'label' => i::__('Agente')],
            'space' => ['class' => 'MapasCulturais\\Entities\\Space', 'label' => i::__('Espaço')],
            'event' => ['class' => 'MapasCulturais\\Entities\\Event', 'label' => i::__('Evento')],
            'project' => ['class' => 'MapasCulturais\\Entities\\Project', 'label' => i::__('Projeto')],
            'opportunity' => ['class' => 'MapasCulturais\\Entities\\Opportunity', 'label' => i::__('Oportunidade')],
        ];
    }

    private function _canManageEntityMetadata() {
        $user = App::i()->user;
        return $user->is('saasSuperAdmin') || $user->is('superAdmin');
    }

    private function _requireEntityMetadataManager() {
        $this->requireAuthentication();

        if (!$this->_canManageEntityMetadata()) {
            $this->errorJson(i::__('Apenas super administradores podem gerenciar metadados.'), 403);
        }
    }

    function GET_accountability()
    {
        $this->requireAuthentication();

        $user = $this->_getUser();
        $registrations = App::i()->repo('Registration')->findByUser($user, 'sent');
        $opportunities = $user->opportunitiesCanBeEvaluated;

        // Retorna as inscrições em oportunidades de prestação de contas (proponente)
        $regAsProp = array_filter($registrations, function ($reg) {
            return Utils::isOpportunityForAccountability($reg->opportunity);
        });

        // Retorna as inscrições de prestação de contas finalizadas (TADO gerado)
        $regAsPropFinished = array_filter($regAsProp, function ($reg) {
            $tado = DiligenceRepo::getTado($reg);

            return isset($tado) && $tado->status === Tado::STATUS_ENABLED;
        });

        // Retorna as inscrições em processo de prestação de contas
        $regAsPropInProcess = array_diff($regAsProp, $regAsPropFinished);

        // Retorna as oportunidades de prestação de contas (fiscal)
        $oppAsFiscal = array_filter($opportunities, function ($opp) {
            return Utils::isOpportunityForAccountability($opp);
        });

        /**
         * Retorna as oportunidades que estão em processo de monitoramento
         * Se em alguma inscrição o TADO ainda não tiver sido gerado, a oportunidade estará em processo de monitoramento
         */
        $oppInMonitoringProcess = array_filter($oppAsFiscal, function ($opp) {
            $inMonitoring = false;

            $registrations = $opp->getSentRegistrations();
            foreach ($registrations as $reg) {
                $tado = DiligenceRepo::getTado($reg);
                if (empty($tado) || $tado->status !== Tado::STATUS_ENABLED) {
                    $inMonitoring = true;
                    break;
                }
            }

            return $inMonitoring;
        });

        // Oportunidades com processo de monitoramento finalizado (Todos os TADO's gerados)
        $oppMonitoringFinished = array_diff($oppAsFiscal, $oppInMonitoringProcess);

        $this->render('accountability', [
            'regAsPropInProcess' => $regAsPropInProcess,
            'regAsPropFinished' => $regAsPropFinished,
            'oppInMonitoringProcess' => $oppInMonitoringProcess,
            'oppMonitoringFinished' => $oppMonitoringFinished,
            'isProponent' => $regAsProp ? true : false,
            'isFiscal' => $oppAsFiscal ? true : false,
        ]);
    }

    public function GET_counterArguments()
    {
        $this->requireAuthentication();

        $userId = App::i()->getUser()->id;
        $counterArguments = App::i()->repo('CounterArgument')->getAllByUserId($userId);

        $this->render('counter-arguments', ['counterArguments' => $counterArguments]);
    }
}
