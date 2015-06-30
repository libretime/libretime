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
    protected static $_section = "[general]";

    // Constant form field names for passing errors back to the front-end
    const GENERAL_PORT = "generalPort",
        GENERAL_HOST = "generalHost";

    // Array of key->value pairs for airtime.conf
    protected static $_properties;

    // Message and error fields to return to the front-end
    static $message = null;
    static $errors = array();

    function __construct($settings) {

        self::$_properties = array(
            "api_key" => $this->generateRandomString(),
            "base_url" => $settings[self::GENERAL_HOST],
            "base_port" => $settings[self::GENERAL_PORT],
        );
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