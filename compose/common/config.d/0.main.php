<?php 

return [
    'app.siteName' => 'Mapa Cultural do Ceará',
    'app.siteDescription' => 'O Mapas Culturais é uma plataforma colaborativa que reúne informações sobre agentes, espaços, eventos, projetos culturais e oportunidades',
    
    // Define o tema ativo no site principal. Deve ser informado o namespace do tema e neste deve existir uma classe Theme.
    'themes.active' => env('ACTIVE_THEME', 'Ceara'),

    'app.lcode' => env('APP_LCODE', 'pt_BR'),

    // Ids dos selos verificadores. Para utilizar múltiplos selos informe os ids separados por vírgula.
    'app.verifiedSealsIds' => '1', 

];