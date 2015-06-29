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
    protected static $_section = "[rabbitmq]";

    // Array of key->value pairs for airtime.conf
    protected static $_properties;

    // Constant form field names for passing errors back to the front-end
    const RMQ_USER = "rmqUser",
        RMQ_PASS = "rmqPass",
        RMQ_PORT = "rmqPort",
        RMQ_HOST = "rmqHost",
        RMQ_VHOST = "rmqVHost";

    // Message and error fields to return to the front-end
    static $message = null;
    static $errors = array();

    function __construct($settings) {
        static::$_properties = array(
            "host"      => $settings[self::RMQ_HOST],
            "port"      => $settings[self::RMQ_PORT],
            "user"      => $settings[self::RMQ_USER],
            "password"  => $settings[self::RMQ_PASS],
            "vhost"     => $settings[self::RMQ_VHOST],
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

    function checkRMQConnection() {
        $conn = new AMQPConnection(self::$_properties["host"],
                                   self::$_properties["port"],
                                   self::$_properties["user"],
                                   self::$_properties["password"],
                                   self::$_properties["vhost"]);
        return isset($conn);
    }

    function identifyRMQConnectionError() {
        // It's impossible to identify errors coming out of amqp.inc without a major
        // rewrite, so for now just tell the user ALL THE THINGS went wrong
        self::$message = _("Couldn't connect to RabbitMQ server! Please check if the server "
            . "is running and your credentials are correct.");
        self::$errors[] = self::RMQ_USER;
        self::$errors[] = self::RMQ_PASS;
        self::$errors[] = self::RMQ_HOST;
        self::$errors[] = self::RMQ_PORT;
        self::$errors[] = self::RMQ_VHOST;
    }

    protected function writeToTemp() {
        if (!file_exists(RMQ_INI_TEMP_PATH)) {
            copy(BUILD_PATH . "rabbitmq-analyzer.ini", RMQ_INI_TEMP_PATH);
        }
        $this->_write(RMQ_INI_TEMP_PATH);
        parent::writeToTemp();
    }

}