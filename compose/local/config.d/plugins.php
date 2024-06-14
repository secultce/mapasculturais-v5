<?php

return [
    'plugins' => [
        'EvaluationMethodTechnical' => ['namespace' => 'EvaluationMethodTechnical', 'config' => ['step' => 0.1]],
        'EvaluationMethodSimple' => ['namespace' => 'EvaluationMethodSimple'],
        'EvaluationMethodDocumentary' => ['namespace' => 'EvaluationMethodDocumentary'],
        'EvaluationMethodQualification' => ['namespace' => 'EvaluationMethodQualification'],
        'ValuersManagement' => [ 'namespace' => 'ValuersManagement' ],

        'Recourse' => [ 'namespace' => 'Recourse' ],
        'EditOpportunityType' => ['namespace' => 'EditOpportunityType'],
        'LocationStateCity' => ['namespace' => 'LocationStateCity'],
        'MultipleLocalAuth' => ['namespace' => 'MultipleLocalAuth'],
    ]
];

/*     $termsGraus = $app->repo('Term')->findBy(['taxonomy' => 'plugin']);
        $graus = array_map(function($term) { return $term; }, $termsGraus);

        $newPlugin = [];
        foreach ($graus as $key => $grau) {
//            echo $key.' = '.$grau['description']."\n";
            var_dump($grau->term);
            var_dump($grau->description);
            $newPlugin[$grau->description] = [
                'namespace' => $grau->description
            ];

//            $app->config['plugins'][$grau->term] = $grau->description;
//            array_push($app->config['plugins'], [$grau->term => $grau->description]);
            $app->config['plugins'];
            var_dump($app->config['plugins']);
        }

        $array = array_merge($app->config['plugins'], $newPlugin);

// Imprimindo o array atualizado
        var_dump($array);die;
 */