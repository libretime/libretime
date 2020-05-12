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
    static $corsUrl;
    // airtime.conf section header
    protected static $_section = "[general]";

    // Constant form field names for passing errors back to the front-end
    const GENERAL_PORT = "generalPort",
        GENERAL_HOST = "generalHost";
    const CORS_URL = "corsURL";

    // Array of key->value pairs for airtime.conf
    protected static $_properties;

    // Message and error fields to return to the front-end
    static $message = null;
    static $errors = array();

    function __construct($settings) {
        self::$corsUrl = $settings[self::CORS_URL];
        self::$_properties = array(
            "api_key" => $this->generateRandomString(),
            "base_url" => $settings[self::GENERAL_HOST],
            "base_port" => $settings[self::GENERAL_PORT],
            "cors_url" => $settings[self::CORS_URL]
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
        $this->setupCorsUrl();

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
            self::$errors[] = self::MEDIA_FOLDER;
            return;
        }

        $this->runCorsUrlQuery($con);
    }

    function runCorsUrlQuery($con) {
        try {
            //Check if key already exists
            $sql = "SELECT valstr FROM cc_pref"
                ." WHERE keystr = 'allowed_cors_urls'";

            $paramMap = array();
            $paramMap[':key'] = 'allowed_cors_urls';

            $sql .= " FOR UPDATE";

            $result = Application_Common_Database::prepareAndExecute($sql,
                $paramMap,
                Application_Common_Database::ROW_COUNT,
                PDO::FETCH_ASSOC,
                $con);

            if ($result > 1) {

            }
            else {
                $pref = new CcPref();
                //if (self::$corsUrl != '') {
                $pref->setKeyStr('allowed_cors_urls')
                    ->setValStr(self::$corsUrl)
                    ->save();
                //$pref::setValue('allowed_cors_urls', self::CORS_URL);
                self::$message = "Saved cors_url";
                //}
                Propel::close();
                //unset($_SERVER['AIRTIME_CONF']);
            }
        } catch (Exception $e) {
            self::$message = "Failed to insert " . self::$corsUrl . " into cc_pref" . $e;
            self::$errors[] = self::CORS_URL;
        }

    }
}