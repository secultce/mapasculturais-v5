<?php

// Definição de pastas para ser inserido no autoload do composer e utilizar o namespace
return [
    'app.autoload' => true,
    'app.namespace' => [
        'Controllers',
        'Definitions',
        'DoctrineEnumTypes',
        'Entities',
        'Exceptions',
        'Middlewares',
        'Repositories',
        'Service',
        'Traits'
    ],
];
