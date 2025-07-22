<?php

declare(strict_types=1);

namespace MapasCulturais\Services;

use MapasCulturais\App;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPChannelClosedException;
use PhpAmqpLib\Exception\AMQPConnectionBlockedException;
use PhpAmqpLib\Exception\AMQPConnectionClosedException;
use PhpAmqpLib\Message\AMQPMessage;
use MapasCulturais\Services\SentryService;

class AmqpQueueService
{

  /** @var AMQPStreamConnection */
    private $connection;

    private $channel;
    private $config;

    public function __construct()
    {
        try {
            $app = App::i();
            $this->config = $app->config['rabbitmq'];
            $this->connection = new AMQPStreamConnection(
                $this->config['host'],
                $this->config['port'],
                $this->config['user'],
                $this->config['password'],
                $this->config['vhost'] ?? '/'
            );
            $this->channel = $this->connection->channel();
        } catch (\Exception $e) {
            SentryService::captureExceptions($e);
            throw $e;
        }
    }

    public function createMessage(array $data): AMQPMessage
    {
        try {
            return new AMQPMessage(
                json_encode($data),
                ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
            );
        } catch (\Exception $e) {
            SentryService::captureExceptions($e);
            throw $e;
        }
    }

    // Enviando mensagem    
    public function sendMessage(
        string $exchange,
        string $routingKey,
        array $messageBody,
        ?string $queueName = null,
        string $exchangeType = 'direct',
        bool $passive = false,
        bool $durable = true,
        bool $exclusive = false,
        bool $autoDelete = false
    ): void {
        try {
            if ($queueName) {
                $this->channel->queue_declare(
                    $queueName,
                    $passive,
                    $durable,
                    $exclusive,
                    $autoDelete
                );
                $this->channel->queue_bind($queueName, $exchange, $routingKey);
            }

            $message = $this->createMessage($messageBody);
            $this->channel->basic_publish($message, $exchange, $routingKey);
        } catch (AMQPChannelClosedException | AMQPConnectionClosedException | AMQPConnectionBlockedException $e) {
            SentryService::captureExceptions($e);
            throw $e;
        } catch (\Exception $e) {
            SentryService::captureExceptions($e);
            throw $e;
        }
    }

    // Fecha o canal e a conexão com o RabbitMQ quando o objeto é destruído
    public function __destruct()
    {
        try {
            if ($this->channel) {
                $this->channel->close();
            }
            if ($this->connection) {
                $this->connection->close();
            }
        } catch (\Exception $e) {
            SentryService::captureExceptions($e);
        }
    }
}
