<?php

$_ENV['APP_MODE'] = 'development';
$__process_assets = false;

date_default_timezone_set('America/Fortaleza');

return [
    /* MAIN */
    'themes.active' => env('ACTIVE_THEME', 'MapasCulturais\Themes\BaseV1'),
    'app.mode' => $_ENV['APP_MODE'],
    'app.sentry' => $_ENV['SENTRY_ENABLED'],
    'doctrine.isDev' => false, // deixe true somente se estiver trabalhando nos mapeamentos das entidades
    'slim.debug' => true,
    'cep.token' => '1a61e4d00bf9c6a85e3b696ef7014372',

    /* SELOS */
    'app.verifiedSealsIds' => [1],

    /* ASSET MANAGER */
    'themes.assetManager' => new \MapasCulturais\AssetManagers\FileSystem([
        'publishPath' => BASE_PATH . 'assets/',

        'mergeScripts' => $__process_assets,
        'mergeStyles' => $__process_assets,

        'process.js' => !$__process_assets ?
                'cp {IN} {OUT}':
                'terser {IN} --source-map --output {OUT} ',

        'process.css' => !$__process_assets ?
                'cp {IN} {OUT}':
                'uglifycss {IN} > {OUT}',

        'publishFolderCommand' => 'cp -R {IN} {PUBLISH_PATH}{FILENAME}'
    ]),
];
