<?php

/**
 * User: sourcefabric
 * Date: 08/12/14.
 *
 * Class GeneralSetup
 *
 * Wrapper class for validating and setting up general settings during the installation process
 */
class GeneralSetup extends Setup
{
    // config file section header
    protected static $_section = '[general]';

    // Array of key->value pairs for the config file
    protected static $_properties;

    // Constant form field names for passing errors back to the front-end
    public const GENERAL_PORT = 'generalPort';
    public const GENERAL_HOST = 'generalHost';
    public const CORS_URL = 'corsUrl';

    public static $cors_url;

    // Message and error fields to return to the front-end
    public static $message;
    public static $errors = [];

    public function __construct($settings)
    {
        self::$_properties = [
            'api_key' => $this->generateRandomString(),
            'base_url' => $settings[self::GENERAL_HOST],
            'base_port' => $settings[self::GENERAL_PORT],
            'cors_url' => $settings[self::CORS_URL],
        ];
        self::$cors_url = $settings[self::CORS_URL];
    }

    /**
     * @return array associative array containing a display message and fields with errors
     */
    public function runSetup()
    {
        // TODO Do we need to validate these settings?

        if (count(self::$errors) <= 0) {
            $this->writeToTemp();
        }
        if (strlen(self::$cors_url) == 0) {
        } else {
            $this->setupCorsUrl();
        }

        return [
            'message' => self::$message,
            'errors' => self::$errors,
        ];
    }

    /**
     * If the user entered a CORS Url then add it to the system preferences
     * TODO Make sure we check for existing CORS URLs and display them on initial form.
     */
    public function setupCorsUrl()
    {
        try {
            $_SERVER['LIBRETIME_CONFIG_FILEPATH'] = INSTALLER_CONFIG_FILEPATH;
            Propel::init(PROPEL_CONFIG_FILEPATH);
            $con = Propel::getConnection();
        } catch (Exception $e) {
            self::$message = "Failed to insert Cors URL; database isn't configured properly!";
            self::$errors[] = self::CORS_URL;

            return;
        }

        $this->runCorsUrlQuery($con);
    }

    public function runCorsUrlQuery($con)
    {
        try {
            Application_Model_Preference::SetAllowedCorsUrls(self::$cors_url);
            Propel::close();
        } catch (Exception $e) {
            self::$message = 'Failed to insert ' . self::$cors_url . ' into cc_pref' . $e;
            self::$errors[] = self::CORS_URL;
        }
    }
}
