<?php

define("CONFIG_PATH", dirname(dirname( __DIR__)) . "/application/configs/");

require_once(dirname(dirname( __DIR__)) . "/../vendor/propel/propel1/runtime/lib/Propel.php");
require_once(CONFIG_PATH . 'conf.php');
require_once(dirname(dirname( __DIR__)) . "/application/models/airtime/CcPref.php");
require_once(dirname(dirname( __DIR__)) . "/application/models/airtime/CcPrefPeer.php");
require_once(dirname(dirname( __DIR__)) . "/application/models/airtime/CcPrefQuery.php");
require_once(dirname(dirname( __DIR__)) . "/application/models/airtime/map/CcPrefTableMap.php");
require_once(dirname(dirname( __DIR__)) . "/application/models/airtime/om/BaseCcPref.php");
require_once(dirname(dirname( __DIR__)) . "/application/models/airtime/om/BaseCcPrefPeer.php");
require_once(dirname(dirname( __DIR__)) . "/application/models/airtime/om/BaseCcPrefQuery.php");


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

    // Array of key->value pairs for airtime.conf
    protected static $_properties;

    // Constant form field names for passing errors back to the front-end
    const GENERAL_PORT = "generalPort",
        GENERAL_HOST = "generalHost";
    const CORS_URL = "corsUrl";

    static $cors_url;

    // Message and error fields to return to the front-end
    static $message = null;
    static $errors = array();

    function __construct($settings) {

        self::$_properties = array(
            "api_key" => $this->generateRandomString(),
            "base_url" => $settings[self::GENERAL_HOST],
            "base_port" => $settings[self::GENERAL_PORT],
            "cors_url" => $settings[self::CORS_URL]
        );
        self::$cors_url = $settings[self::CORS_URL];

    }

    /**
     * @return array associative array containing a display message and fields with errors
     */
    function runSetup() {
        // TODO Do we need to validate these settings?

        if (count(self::$errors) <= 0) {
            $this->writeToTemp();
        }
        if (strlen(self::$cors_url) == 0) {
        }
        else {
            $this->setupCorsUrl();
        }
        return array(
            "message" => self::$message,
            "errors" => self::$errors
        );
    }
    /**
     * If the user entered a CORS Url then add it to the system preferences
     * TODO Make sure we check for existing CORS URLs and display them on initial form
     */
    function setupCorsUrl() {
        try {
            $_SERVER['AIRTIME_CONF'] = AIRTIME_CONF_TEMP_PATH;
            Propel::init(CONFIG_PATH . "airtime-conf-production.php");
            $con = Propel::getConnection();
        } catch(Exception $e) {
            self::$message = "Failed to insert Cors URL; database isn't configured properly!";
            self::$errors[] = self::CORS_URL;
            return;
        }

        $this->runCorsUrlQuery($con);
    }

    function runCorsUrlQuery($con) {
        try {
        Application_Model_Preference::SetAllowedCorsUrls(self::$cors_url);
                Propel::close();
                //unset($_SERVER['AIRTIME_CONF']);
        } catch (Exception $e) {
            self::$message = "Failed to insert " . self::$cors_url . " into cc_pref" . $e;
            self::$errors[] = self::CORS_URL;
        }

    }
}