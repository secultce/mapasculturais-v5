<?php

namespace Diligence\Traits;

use \MapasCulturais\App;

trait DiligenceSingle{

    /**
     * Responsável para salvar os dados da entidade
     *
     * @param [object] $entity
     * @return void
     */
    static protected function saveEntity($entity)
    {
        $app        = App::i();
        $app->em->persist($entity);
        $app->em->flush();
        $app->disableAccessControl();
        $save       = $entity->save();
        $app->enableAccessControl();
        return ['save' => $save , 'entityId' => $entity->id];
    }

    static protected function returnJson($instance, $class)
    {
        if(is_null($instance)){
            $class->json(['message' => 'success', 'status' => 200], 200);
        }else{
            $class->json(['message' => 'Error: ', 'status' => 400], 400);
        }    
    }

    static protected function getRequestedEntity($class){
        return $class->controller->requestedEntity;
    }


}