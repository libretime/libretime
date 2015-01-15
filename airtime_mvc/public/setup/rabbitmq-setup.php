<?php

require_once dirname(dirname( __DIR__)) . '/library/php-amqplib/amqp.inc';

/**
 * User: sourcefabric
 * Date: 02/12/14
 *
 * Class RabbitMQSetup
 *
 * Wrapper class for validating and setting up RabbitMQ during the installation process
 */
class RabbitMQSetup extends Setup {

    // airtime.conf section header
    const SECTION = "[rabbitmq]";

    // Constant form field names for passing errors back to the front-end
    const RMQ_USER = "rmqUser",
        RMQ_PASS = "rmqPass",
        RMQ_PORT = "rmqPort",
        RMQ_HOST = "rmqHost",
        RMQ_VHOST = "rmqVHost";

    // Form field values
    static $user, $pass, $host, $port, $vhost;

    // Array of key->value pairs for airtime.conf
    static $properties;

    // Message and error fields to return to the front-end
    static $message = null;
    static $errors = array();

    function __construct($settings) {
        self::$user = $settings[self::RMQ_USER];
        self::$pass = $settings[self::RMQ_PASS];
        self::$port = $settings[self::RMQ_PORT];
        self::$host = $settings[self::RMQ_HOST];
        self::$vhost = $settings[self::RMQ_VHOST];

        self::$properties = array(
            "host" => self::$host,
            "port" => self::$port,
            "user" => self::$user,
            "password" => self::$pass,
            "vhost" => self::$vhost,
        );
    }

    /**
     * @return array associative array containing a display message and fields with errors
     */
    function runSetup() {
        try {
            if ($this->checkRMQConnection()) {
                self::$message = "Connection successful!";
            } else {
                $this->identifyRMQConnectionError();
            }
        } catch(Exception $e) {
            $this->identifyRMQConnectionError();
        }

        if (count(self::$errors) <= 0) {
            $this->writeToTemp();
        }

        return array(
            "message" => self::$message,
            "errors" => self::$errors
        );
    }

    function writeToTemp() {
        parent::writeToTemp(self::SECTION, self::$properties);
    }

    function checkRMQConnection() {
        $conn = new AMQPConnection(self::$host,
                                   self::$port,
                                   self::$user,
                                   self::$pass,
                                   self::$vhost);
        return isset($conn);
    }

    function identifyRMQConnectionError() {
        // It's impossible to identify errors coming out of amqp.inc without a major
        // rewrite, so for now just tell the user ALL THE THINGS went wrong
        self::$message = "Couldn't connect to RabbitMQ server! Please check if the server "
            . "is running and your credentials are correct.";
        self::$errors[] = self::RMQ_USER;
        self::$errors[] = self::RMQ_PASS;
        self::$errors[] = self::RMQ_HOST;
        self::$errors[] = self::RMQ_PORT;
        self::$errors[] = self::RMQ_VHOST;
    }

}