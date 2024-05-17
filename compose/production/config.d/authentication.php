<?php
// creating base url
$prot_part = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] ? 'https://' : 'http://';
//added @ for HTTP_HOST undefined in Tests
$host_part = @$_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);
if(substr($host_part,-1) !== '/') $host_part .= '/';
$_APP_BASE_URL = $prot_part . $host_part;

return [
    'auth.provider' => '\MultipleLocalAuth\Provider',
    'auth.config' => array(
        'salt' => 'LT_SECURITY_SALT_SECURITY_SALT_SECURITY_SALT_SECURITY_SALT_SECU',
        'timeout' => '24 hours',
        'enableLoginByCPF' => true,
        'metadataFieldCPF' => 'documento',
        'userMustConfirmEmailToUseTheSystem' => false,
        'passwordMustHaveCapitalLetters' => false,
        'passwordMustHaveLowercaseLetters' => true,
        'passwordMustHaveSpecialCharacters' => false,
        'passwordMustHaveNumbers' => true,
        'minimumPasswordLength' => 8,
        'google-recaptcha-sitekey' => env('GOOGLE_RECAPTCHA_SITEKEY'),
        'google-recaptcha-secret' => env('GOOGLE_RECAPTCHA_SECRET'),
        'sessionTime' => 7200, // int , tempo da sessao do usuario em segundos
        'numberloginAttemp' => '10', // tentativas de login antes de bloquear o usuario por X minutos
        'timeBlockedloginAttemp' => '900', //
        'strategies' => [
            'Facebook' => array(
               'app_id' => env('AUTH_FACEBOOK_APP_ID', null),
               'app_secret' => env('AUTH_FACEBOOK_APP_SECRET', null),
               'scope' => env('AUTH_FACEBOOK_SCOPE', 'email'),
            ),

            'LinkedIn' => array(
                'api_key' => env('AUTH_LINKEDIN_API_KEY', null),
                'secret_key' => env('AUTH_LINKEDIN_SECRET_KEY', null),
                'redirect_uri' => $_APP_BASE_URL . 'autenticacao/linkedin/oauth2callback',
                'scope' => env('AUTH_LINKEDIN_SCOPE', 'r_emailaddress')
            ),
            'Google' => array(
                'client_id' => env('AUTH_GOOGLE_CLIENT_ID', null),
                'client_secret' => env('AUTH_GOOGLE_CLIENT_SECRET', null),
                'redirect_uri' => $_APP_BASE_URL . 'autenticacao/google/oauth2callback',
                'scope' => env('AUTH_GOOGLE_SCOPE', 'email'),
            ),
            'Twitter' => array(
                'app_id' => env('AUTH_TWITTER_APP_ID', null),
                'app_secret' => env('AUTH_TWITTER_APP_SECRET', null),
            ),

        ],
        //url do site de suporte para ser enviado nos emails
        'urlSupportSite' => 'https://leialdirblanc.secult.ce.gov.br/suporte',

        //url dos termos de uso para utilizar a plataforma
        'urlTermsOfUse' => 'https://mapacultural.secult.ce.gov.br/autenticacao/termos-e-condicoes',

        //url de uma imagem para ser enviado como plano de fundo nos emails
        'urlImageToUseInEmails' => 'https://mapacultural.secult.ce.gov.br/assets/img/email-aldir.png',
    ),
];
