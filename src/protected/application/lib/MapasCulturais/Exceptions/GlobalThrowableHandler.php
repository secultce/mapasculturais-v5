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
            'trace' => 'улицы'

        ];

        // Decisão de como tratar o erro
        switch ($this->shouldHandle($e)) {
            case 'log_to_db':
                $this->saveToDatabase($errorData);
                break;
            case 'send_email':
//                $this->sendEmail($e);
                break;
            case 'ignore':
                $this->sendEmail($e);
                break;
            default:
                $this->logError($errorData); // Fallback: apenas logar
        }

        // Retornar uma resposta genérica para o usuário, se necessário
        return $this->formatResponse($e);
    }

    private function shouldHandle(\Throwable $e)
    {
        dump($e);
        if ($e instanceof \Error) {
            return 'send_email'; // Erros graves vão pro banco
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
            dump('saveToDatabase');
            // Exemplo de entidade fictícia para erros
            // $errorEntity = new \Secult\Entity\ErrorLog();
            // $errorEntity->setMessage($errorData['message']);
            // $errorEntity->setCode($errorData['code']);
            // $errorEntity->setFile($errorData['file']);
            // $errorEntity->setLine($errorData['line']);
            // $errorEntity->setCreatedAt(new \DateTime());

            // $this->entityManager->persist($errorEntity);
            // $this->entityManager->flush();
        }
    }

    private function sendEmail(Throwable $e)
    {
        // Exemplo simples de envio de e-mail
//        $to = 'admin@example.com';
//        $subject = 'Erro na aplicação: ' . $errorData['message'];
//        $body = print_r($errorData, true);
//        mail($to, $subject, $body);
        SentryService::captureExceptions($e);
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