<?php
return [
   'auth.provider' => '\MultipleLocalAuth\Provider',
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
       'strategies' => [],
       //url de suporte por chat para ser enviado nos emails
       //'urlSupportChat' => 'https://www.google.com',


       //url de suporte por email para ser enviado nos emails
       //'urlSupportEmail' => 'https://www.google.com',


       //url do site de suporte para ser enviado nos emails
       'urlSupportSite' => 'https://leialdirblanc.secult.ce.gov.br/suporte',


       //url dos termos de uso para utilizar a plataforma
       'urlTermsOfUse' => 'https://mapacultural.secult.ce.gov.br/autenticacao/termos-e-condicoes',
       //url de uma imagem para ser enviado como plano de fundo nos emails
       'urlImageToUseInEmails' => 'https://mapacultural.secult.ce.gov.br/assets/img/email-aldir.png',
   ],
];
