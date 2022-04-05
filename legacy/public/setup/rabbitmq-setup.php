<?php

/**
 * User: sourcefabric
 * Date: 02/12/14.
 *
 * Class RabbitMQSetup
 *
 * Wrapper class for validating and setting up RabbitMQ during the installation process
 */
class RabbitMQSetup extends Setup
{
    // airtime.conf section header
    protected static $_section = '[rabbitmq]';

    // Array of key->value pairs for airtime.conf
    protected static $_properties;

    // Constant form field names for passing errors back to the front-end
    public const RMQ_USER = 'rmqUser';
    public const RMQ_PASS = 'rmqPass';
    public const RMQ_PORT = 'rmqPort';
    public const RMQ_HOST = 'rmqHost';
    public const RMQ_VHOST = 'rmqVHost';

    // Message and error fields to return to the front-end
    public static $message;
    public static $errors = [];

    public function __construct($settings)
    {
        static::$_properties = [
            'host' => $settings[self::RMQ_HOST],
            'port' => $settings[self::RMQ_PORT],
            'user' => $settings[self::RMQ_USER],
            'password' => $settings[self::RMQ_PASS],
            'vhost' => $settings[self::RMQ_VHOST],
        ];
    }

    /**
     * @return array associative array containing a display message and fields with errors
     */
    public function runSetup()
    {
        try {
            if ($this->checkRMQConnection()) {
                self::$message = 'Connection successful!';
            } else {
                $this->identifyRMQConnectionError();
            }
        } catch (Exception $e) {
            $this->identifyRMQConnectionError();
        }

        return [
            'message' => self::$message,
            'errors' => self::$errors,
        ];
    }

    public function checkRMQConnection()
    {
        $conn = new \PhpAmqpLib\Connection\AMQPStreamConnection(
            self::$_properties['host'],
            self::$_properties['port'],
            self::$_properties['user'],
            self::$_properties['password'],
            self::$_properties['vhost']
        );
        $this->writeToTemp();

        return isset($conn);
    }

    public function identifyRMQConnectionError()
    {
        // It's impossible to identify errors coming out of amqp.inc without a major
        // rewrite, so for now just tell the user ALL THE THINGS went wrong
        self::$message = _("Couldn't connect to RabbitMQ server! Please check if the server "
            . 'is running and your credentials are correct.');
        self::$errors[] = self::RMQ_USER;
        self::$errors[] = self::RMQ_PASS;
        self::$errors[] = self::RMQ_HOST;
        self::$errors[] = self::RMQ_PORT;
        self::$errors[] = self::RMQ_VHOST;
    }
}
