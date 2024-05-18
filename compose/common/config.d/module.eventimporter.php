<?php

use MapasCulturais\App;

$app = App::i();

return [
    'module.EventImporter' => [
        'enabled' => function () use ($app) {
            if($app->user->is("admin")) {
                return true;
            }

            $agentSealRelations = $app->repo('AgentSealRelation')->findBy(['seal' => 12]);
            $agents = array_map(function($sealRelation) {
                return $sealRelation->owner;
            }, $agentSealRelations);

            for($i = 0; $i < count($agents); $i++) {
                if($agents[$i]->canUser('@control'))
                    return true;
            }

            return false;
        }
    ]
];
