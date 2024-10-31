<?php

namespace MapasCulturais\Controllers;

use MapasCulturais\App;
use MapasCulturais\Traits;
use MapasCulturais\Entities;

/**
 * EntityRevision Controller
 *
 * By default this controller is registered with the id 'entityrevision'.
 *
 */
class EntityRevision extends EntityController {

    function GET_history(){
    	$app = App::i();
        // Somente autenticado
        $this->requireAuthentication();
    	$id = $this->data['id'];
    	$entityRevision = $app->repo('EntityRevision')->findCreateRevisionObject($id);

        $entity = $entityRevision->entity;
            // Se for o usuário da entidade, a página é renderizada
        if($entity->owner->user == $app->getUser()){
    	    $this->render('history', ['entityRevision' => $entityRevision]);
        }else{
            // Caso venha do rota cliente no historico da entidade, então retornará para entidade, se não retornará para o painel
            is_null($app->request()->getReferer()) ? $app->redirect($app->createUrl('painel')) : $app->redirect($app->request()->getReferer());
        }
    }
}
