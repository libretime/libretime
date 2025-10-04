<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;


class Celery
{
    private static $connection = null;

    /**
     * Get AMQP connection.
     */
    private static function getConnection($config)
    {
        if (is_null(self::$connection)) {
            self::$connection = new AMQPStreamConnection(
                $config['rabbitmq']['host'],
                $config['rabbitmq']['port'],
                $config['rabbitmq']['user'],
                $config['rabbitmq']['password'],
                $config['rabbitmq']['vhost']
            );
        }

        return  self::$connection;
    }

    /**
     * Send AMQP message to Celery to perform a task.
     */
    public static function sendTask($name, $args = [], $kwargs = []): string
    {
        $config =  Config::getConfig();

        $conn = self::getConnection($config);

        $channel = $conn->channel();
        $channel->exchange_declare('celery', 'direct', false, true, false);

        $task_id = self::uuid4();
        $task_name = $name;
        $task_args = $args;
        $task_kwargs = $kwargs;

        $properties = [
            'correlation_id' =>  $task_id,

            'content_encoding' => 'utf-8',
            'content_type' => 'application/json',

            'application_headers' =>  new AMQPTable([
                'lang' => 'py',
                'task' => $task_name,
                'id' =>  $task_id,
                'origin' => self::origin(),
            ]),
        ];

        $payload = [
            $task_args,
            (object) $task_kwargs,
            (object) [],
        ];

        $message = new AMQPMessage(json_encode($payload), $properties);

        $channel->basic_publish($message, 'celery', 'celery');
        $channel->close();

        return $task_id;
    }

    /**
     * Build a random UUID v4.
     * Thanks to https://stackoverflow.com/a/15875555/1285669.
     */
    public static function uuid4()
    {
        $data = random_bytes(16);

        // Set version to 01006.
        $data[6] = chr(ord($data[6]) & 0x0F | 0x40);
        // Set bits 6-7 to 10.
        $data[8] = chr(ord($data[8]) & 0x3F | 0x80);

        // Output the 36 character UUID.
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * Build the 'origin' of Celery task messages the similar way as
     * Python Celery does.
     *
     * See `celery/utils/nodenames.py` in Celery library.
     */
    public static function origin()
    {
        static $result = null;
        return $result ??= sprintf('legacy:%d@%s', getmypid(), gethostname());
    }
}
