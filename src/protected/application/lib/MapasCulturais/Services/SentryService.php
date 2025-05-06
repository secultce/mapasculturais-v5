<?php

namespace MapasCulturais\Services;
class SentryService
{
    protected static $initialized = false;

    public static function init(): void
    {

        if (self::$initialized) {
            return;
        }

        \Sentry\init([
            'dsn' => getenv('SENTRY_DSN') ?: '',
            'environment' => getenv('APP_ENV') ?: null,
            'release' => getenv('APP_VERSION') ?: 'v1.0.0',
            'traces_sample_rate' => 1.0, // captura 100% das transações (ajuste se necessário)
        ]);

        self::$initialized = true;
    }

    public static function captureExceptions(\Throwable $exception): void
    {
        if (!self::$initialized) {
            self::init();
        }

        \Sentry\captureException($exception);
    }

    public static function captureMessage(string $message, string $level = 'error'): void
    {
        if (!self::$initialized) {
            self::init();
        }

        \Sentry\captureMessage($message, $level);
    }
}
