<?php

namespace MapasCulturais\Exceptions;

use MapasCulturais\Services\SentryService;
use Throwable;
use Slim\Log;
use Doctrine\ORM\EntityManager;

class GlobalThrowableHandler
{
    private $logger; // Para registrar logs (opcional)
    private $entityManager; // Para gravar no banco

    public function __construct(Log $logger, EntityManager $entityManager )
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
    }

    public function handle(Throwable $e)
    {
        // Informações básicas da exceção
        $errorData = [
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTrace()
        ];

        // Decisão de como tratar o erro
        switch ($this->shouldHandle($e)) {
            case 'log_to_db': // para registra no banco de dados
                $this->saveToDatabase($errorData);
                break;
            case 'error_sentry': // para registrar no sentry
                SentryService::captureExceptions($e);
                break;
            case 'send_email': // para enviar email em casos importantes
                $this->sendEmail($errorData);
                break;
            default:
                $this->logError($errorData); // para registro no log
        }

        // Retornar uma resposta genérica para o usuário, se necessário
        return $this->formatResponse($e);
    }

    private function shouldHandle(\Throwable $e)
    {
    
        if ($e instanceof \Error) {
            return 'error_sentry'; // Erros graves vão para o sentry
        }
        $code = $e->getCode();
        // Lógica personalizada para decidir o que fazer com o erro
        if ($code === 500) {
            return 'log_to_db';
        } elseif ($code === 400) {
            return 'send_email';
        }
        return 'ignore'; // Default: ignorar
    }

    private function saveToDatabase(array $errorData)
    {
        if ($this->entityManager) {            
            // Exemplo de entidade fictícia para erros
            // Possível registro no banco de dados
        }
    }

    private function sendEmail(array $errorData)
    {
        // @todo implementar disparo de email
    }

    private function logError(array $errorData)
    {
        if ($this->logger) {
            $this->logger->error('Erro capturado', $errorData);
        }
    }

    private function formatResponse(Throwable $e)
    {
        // Resposta padrão para o Slim
        return [
            'status' => 'error',
            'message' => 'Ocorreu um erro interno. Tente novamente mais tarde.',
            'code' => $e->getCode()
        ];
    }
}