<?php

/**
 * User: sourcefabric
 * Date: 08/12/14
 *
 * Class GeneralSetup
 *
 * Wrapper class for validating and setting up general settings during the installation process
 */
class GeneralSetup extends Setup {

    // airtime.conf section header
    const SECTION = "[general]";

    // Constant form field names for passing errors back to the front-end
    const GENERAL_PORT = "generalPort",
        GENERAL_HOST = "generalHost";

    // Form field values
    static $user, $host, $port, $root;

    // Array of key->value pairs for airtime.conf
    static $properties;

    // Message and error fields to return to the front-end
    static $message = null;
    static $errors = array();

    function __construct($settings) {
        self::$host = $settings[self::GENERAL_HOST];
        self::$port = $settings[self::GENERAL_PORT];

        self::$properties = array(
            "api_key" => $this->generateRandomString(),
            "base_url" => self::$host,
            "base_port" => self::$port,
        );
    }

    function writeToTemp() {
        parent::writeToTemp(self::SECTION, self::$properties);
    }

    /**
     * @return array associative array containing a display message and fields with errors
     */
    function runSetup() {
        // TODO Do we need to validate these settings?

        if (count(self::$errors) <= 0) {
            $this->writeToTemp();
        }

        return array(
            "message" => self::$message,
            "errors" => self::$errors
        );
    }

}