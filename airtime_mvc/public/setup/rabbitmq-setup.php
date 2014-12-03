<?php
/**
 * User: sourcefabric
 * Date: 02/12/14
 *
 * Class RabbitMQSetup
 *
 * Wrapper class for validating and setting up RabbitMQ during the installation process
 */
class RabbitMQSetup extends Setup {

    const RMQ_USER = "rmqUser",
        RMQ_PASS = "rmqPass",
        RMQ_PORT = "rmqPort",
        RMQ_HOST = "rmqHost",
        RMQ_VHOST = "rmqVHost";

    static $user, $pass, $name, $host, $port, $vhost;

    function __construct($settings) {
        self::$user = $settings[self::RMQ_USER];
        self::$pass = $settings[self::RMQ_PASS];
        self::$port = $settings[self::RMQ_PORT];
        self::$host = $settings[self::RMQ_HOST];
        self::$vhost = $settings[self::RMQ_VHOST];
    }

    /**
     * @return array associative array containing a display message and fields with errors
     */
    function runSetup() {
        $message = "";
        $errors = array();

        return array(
            "message" => $message,
            "errors" => $errors
        );
    }

}