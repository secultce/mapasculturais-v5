<?php

$config = [
    'host' => env('RABBITMQ_HOST', 'localhost'),
    'port' => env('RABBITMQ_PORT', '5672'),
    'user' => env('RABBITMQ_USER', 'user'),
    'password' => env('RABBITMQ_PASSWORD', 'password')
];

return [
    'rabbitmq' => $config
];