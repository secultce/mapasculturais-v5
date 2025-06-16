<?php

declare(strict_types=1);

namespace MapasCulturais\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPChannelClosedException;
use PhpAmqpLib\Exception\AMQPConnectionBlockedException;
use PhpAmqpLib\Exception\AMQPConnectionClosedException;
use PhpAmqpLib\Message\AMQPMessage;

class AmqpQueueService
{

    /** @var AMQPStreamConnection */
    private $connection;

    private $channel;

    public function __construct()
    {
        $this->connection = new AMQPStreamConnection(
            $_ENV['RABBITMQ_HOST'],
            $_ENV['RABBITMQ_PORT'],
            $_ENV['RABBITMQ_USER'],
            $_ENV['RABBITMQ_PASSWORD'],
            $_ENV['RABBITMQ_VHOST']
        );
        $this->channel = $this->connection->channel();
    }

    public function createMessage($data): AMQPMessage
    {
        return new AMQPMessage(json_encode($data), ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
    }

    /**
     * @param AMQPMessage $message
     * @param string $queue
     * @param string $exchange
     * @return void
     *
     * @throws AMQPChannelClosedException
     * @throws AMQPConnectionClosedException
     * @throws AMQPConnectionBlockedException
     */
    public function sendToQueue(AMQPMessage $message, string $queue, string $exchange = ''): void
    {
        $this->channel->queue_declare($queue, false, true, false, false);

        $this->channel->basic_publish($message, $exchange);
    }
}
