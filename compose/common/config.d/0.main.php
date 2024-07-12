<?php

use MapasCulturais\i;

date_default_timezone_set('America/Fortaleza');

$base_domain = @$_SERVER['HTTP_HOST'];
$base_url = 'https://' . $base_domain . '/';

return [
    'app.siteName' => env('SITE_NAME', 'Mapa Cultural do Ceará'),
    'app.siteDescription' => env('SITE_DESCRIPTION', "O Mapa Cultural do Ceará é a plataforma livre, gratuita e colaborativa de mapeamento da Secretaria da Cultura do Estado do Ceará sobre cenÃ¡rio cultural cearense. Ficou mais fÃ¡cil se programar para conhecer as opÃ§Ãµes culturais que as cidades cearenses oferecem: shows musicais, espetÃ¡culos teatrais, sessÃµes de cinema, saraus, entre outras. Além de conferir a agenda de eventos, vocÃª também pode colaborar na gestÃ£o da cultura do estado: basta criar seu perfil de agente cultural. A partir deste cadastro, fica mais fÃ¡cil participar dos editais e programas da Secretaria e também divulgar seus eventos, espaÃ§os ou projetos."),

    'themes.active' => env('ACTIVE_THEME', 'Ceara'),

    'app.lcode' => env('APP_LCODE', 'pt_BR'),

    'app.enabled.apps' => false,
    
    'app.verifiedSealsIds' => [1], 

    'module.CompliantSuggestion' => [
        'compliant' => true,
        'compliantUrl' => 'https://cearatransparente.ce.gov.br/portal-da-transparencia/ouvidoria',
        'suggestion' => true,

    ],
	
];