<?php

return [
     'auth.provider' => 'Fake',
//    'auth.provider' => '\MultipleLocalAuth\Provider',
    'auth.config' => [
        'salt' => 'LT_SECURITY_SALT_SECURITY_SALT_SECURITY_SALT_SECURITY_SALT_SECU',
        'timeout' => '24 hours',
        'enableLoginByCPF' => true,
        'metadataFieldCPF' => 'documento',
        'userMustConfirmEmailToUseTheSystem' => true,
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
            'Facebook' => [
                'app_id' => env('AUTH_FACEBOOK_APP_ID', null),
                'app_secret' => env('AUTH_FACEBOOK_APP_SECRET', null),
                'scope' => env('AUTH_FACEBOOK_SCOPE', 'email'),
            ],
            'LinkedIn' => [
                'api_key' => env('AUTH_LINKEDIN_API_KEY', null),
                'secret_key' => env('AUTH_LINKEDIN_SECRET_KEY', null),
                'redirect_uri' => '/autenticacao/linkedin/oauth2callback',
                'scope' => env('AUTH_LINKEDIN_SCOPE', 'r_emailaddress')
            ],
            'Google' => [
                'client_id' => env('AUTH_GOOGLE_CLIENT_ID', null),
                'client_secret' => env('AUTH_GOOGLE_CLIENT_SECRET', null),
                'redirect_uri' => '/autenticacao/google/oauth2callback',
                'scope' => env('AUTH_GOOGLE_SCOPE', 'email'),
            ],
            'Twitter' => [
                'app_id' => env('AUTH_TWITTER_APP_ID', null),
                'app_secret' => env('AUTH_TWITTER_APP_SECRET', null),
            ],
        ]
    ]
];