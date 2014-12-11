<?php

define("CONFIG_PATH", dirname(dirname( __DIR__)) . "/application/configs/");
define("DEFAULT_STOR_DIR", "/srv/airtime/stor/");

require_once(dirname(dirname( __DIR__)) . "/library/propel/runtime/lib/Propel.php");
require_once(CONFIG_PATH . 'conf.php');

require_once(dirname(dirname( __DIR__)) . "/application/models/airtime/map/CcMusicDirsTableMap.php");
require_once(dirname(dirname( __DIR__)) . "/application/models/airtime/om/BaseCcMusicDirsQuery.php");
require_once(dirname(dirname( __DIR__)) . "/application/models/airtime/CcMusicDirsQuery.php");
require_once(dirname(dirname( __DIR__)) . "/application/models/airtime/om/BaseCcMusicDirs.php");
require_once(dirname(dirname( __DIR__)) . "/application/models/airtime/CcMusicDirs.php");
require_once(dirname(dirname( __DIR__)) . "/application/models/airtime/om/BaseCcMusicDirsPeer.php");
require_once(dirname(dirname( __DIR__)) . "/application/models/airtime/CcMusicDirsPeer.php");

/**
 * User: sourcefabric
 * Date: 08/12/14
 *
 * Class MediaSetup
 *
 * Wrapper class for validating and setting up media folder during the installation process
 */
class MediaSetup extends Setup {

    const MEDIA_FOLDER = "mediaFolder";

    static $path;
    static $message = null;
    static $errors = array();

    function __construct($settings) {
        self::$path = $settings[self::MEDIA_FOLDER];
    }

    /**
     * @return array associative array containing a display message and fields with errors
     */
    function runSetup() {
        // If the path passed in is empty, set it to the default
        if (strlen(self::$path) == 0) {
            self::$path = DEFAULT_STOR_DIR;
            if (!file_exists(DEFAULT_STOR_DIR)) {
                mkdir(DEFAULT_STOR_DIR, 0755, true);
            }
        }

        // Append a trailing / if they didn't
        if (!(substr(self::$path, -1) == "/")) {
            self::$path .= "/";
        }

        if (file_exists(self::$path)) {
            $this->setupMusicDirectory();
        } else {
            self::$message = "Invalid path!";
            self::$errors[] = self::MEDIA_FOLDER;
        }

        return array(
            "message" => self::$message,
            "errors" => self::$errors
        );
    }

    /**
     * Add the given directory to cc_music_dirs
     * TODO Should we check for an existing entry in cc_music_dirs?
     */
    function setupMusicDirectory() {
        try {
            $_SERVER['AIRTIME_CONF'] = AIRTIME_CONF_TEMP_PATH;
            Propel::init(CONFIG_PATH . "airtime-conf-production.php");
            $con = Propel::getConnection();
        } catch(Exception $e) {
            self::$message = "Failed to insert media folder; database isn't configured properly!";
            self::$errors[] = self::MEDIA_FOLDER;
            return;
        }

        $this->runMusicDirsQuery($con);
    }

    function runMusicDirsQuery($con) {
        try {
            if ($this->checkMusicDirectoryExists($con)) {
                $dir = CcMusicDirsQuery::create()->findPk(1, $con);
            } else {
                $dir = new CcMusicDirs();
            }

            $dir->setDirectory(self::$path)
                ->setType("stor")
                ->save();
            self::$message = "Successfully set up media folder!";
            Propel::close();
            unset($_SERVER['AIRTIME_CONF']);
        } catch (Exception $e) {
            self::$message = "Failed to insert " . self::$path . " into cc_music_dirs";
            self::$errors[] = self::MEDIA_FOLDER;
        }

    }

    function checkMusicDirectoryExists($con) {
        $entry = CcMusicDirsQuery::create()->findPk(1, $con);
        return isset($entry) && $entry;
    }

}