<?php

namespace MapasCulturais\Services;

use MapasCulturais\App;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQService
{
    protected $connection;
    protected $channel;
    protected $config;

    public function __construct()
    {
        $app = App::i();
        $config['host'] = $app->config['rabbitmq']['host'];
        $config['port'] = $app->config['rabbitmq']['port'];
        $config['user'] = $app->config['rabbitmq']['user'];
        $config['password'] = $app->config['rabbitmq']['password'];

        $this->config = $config;

        $this->connect();
    }

    protected function connect()
    {
        $this->connection = new AMQPStreamConnection(
            $this->config['host'],
            $this->config['port'],
            $this->config['user'],
            $this->config['password']
        );

        $this->channel = $this->connection->channel();
    }

    public function sendMessage(
        string $exchange,
        string $routingKey,
        array $messageBody,
        string $queueName = null, // Novo parâmetro opcional
        string $exchangeType = 'direct',
        bool $durable = true
    ) {
        // Declara a exchange se não existir
        if ($queueName) {
            $this->channel->queue_declare(
                $queueName,
                false,  // passive
                $durable, // durable
                false,  // exclusive
                false   // auto_delete
            );

            $this->channel->queue_bind($queueName, $exchange, $routingKey);
        }

        // Cria a mensagem
        $msg = new AMQPMessage(
            json_encode($messageBody),
            ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]
        );

        // Publica a mensagem
        $this->channel->basic_publish($msg, $exchange, $routingKey);
    }

    public function __destruct()
    {
        if ($this->channel) {
            $this->channel->close();
        }
        if ($this->connection) {
            $this->connection->close();
        }
    }
}