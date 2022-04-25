<?php

/**
 * Author: sourcefabric
 * Date: 08/12/14.
 *
 * Class MediaSetup
 *
 * Wrapper class for validating and setting up media folder during the installation process
 */
class MediaSetup extends Setup
{
    public const MEDIA_FOLDER = 'mediaFolder';

    public static $path;
    public static $message;
    public static $errors = [];

    public function __construct($settings)
    {
        self::$path = $settings[self::MEDIA_FOLDER];
    }

    /**
     * @return array associative array containing a display message and fields with errors
     */
    public function runSetup()
    {
        // If the path passed in is empty, set it to the default
        if (strlen(self::$path) == 0) {
            self::$path = INSTALLER_DEFAULT_STORAGE_PATH;
            if (!file_exists(INSTALLER_DEFAULT_STORAGE_PATH)) {
                mkdir(INSTALLER_DEFAULT_STORAGE_PATH, 0755, true);
            }
        }

        // Append a trailing / if they didn't
        if (!(substr(self::$path, -1) == '/')) {
            self::$path .= '/';
        }

        if (file_exists(self::$path)) {
            $this->setupMusicDirectory();
        } else {
            self::$message = 'Invalid path!';
            self::$errors[] = self::MEDIA_FOLDER;
        }

        // Finalize and move installer config file to libretime config file
        if (file_exists(LIBRETIME_CONFIG_DIR)) {
            if (!$this->moveAirtimeConfig()) {
                self::$message = 'Error moving or deleting the installer config!';
                self::$errors[] = 'ERR';
            }

            /*
             * If we're upgrading from an old Airtime instance (pre-2.5.2) we rename their old
             * airtime.conf to airtime.conf.tmp during the setup process. Now that we're done,
             * we can rename it to airtime.conf.bak to avoid confusion.
             */
            $fileName = LIBRETIME_CONFIG_FILEPATH;
            $tmpFile = $fileName . '.tmp';
            $bakFile = $fileName . '.bak';
            if (file_exists($tmpFile)) {
                rename($tmpFile, $bakFile);
            }
        } else {
            self::$message = "Failed to move airtime.conf; /etc/airtime doesn't exist!";
            self::$errors[] = 'ERR';
        }

        return [
            'message' => self::$message,
            'errors' => self::$errors,
        ];
    }

    /**
     * Moves /tmp/airtime.temp.conf to /etc/airtime.conf and then removes it to complete setup.
     *
     * @return bool false if either of the copy or removal operations fail
     */
    public function moveAirtimeConfig()
    {
        return copy(INSTALLER_CONFIG_FILEPATH, LIBRETIME_CONFIG_FILEPATH)
            && unlink(INSTALLER_CONFIG_FILEPATH);
    }

    /**
     * Add the given directory to cc_music_dirs
     * TODO Should we check for an existing entry in cc_music_dirs?
     */
    public function setupMusicDirectory()
    {
        try {
            $_SERVER['LIBRETIME_CONFIG_FILEPATH'] = INSTALLER_CONFIG_FILEPATH;
            Propel::init(PROPEL_CONFIG_FILEPATH);
            $con = Propel::getConnection();
        } catch (Exception $e) {
            self::$message = "Failed to insert media folder; database isn't configured properly!";
            self::$errors[] = self::MEDIA_FOLDER;

            return;
        }

        $this->runMusicDirsQuery($con);
    }

    public function runMusicDirsQuery($con)
    {
        try {
            if ($this->checkMusicDirectoryExists($con)) {
                $dir = CcMusicDirsQuery::create()->findPk(1, $con);
            } else {
                $dir = new CcMusicDirs();
            }

            $dir->setDirectory(self::$path)
                ->setType('stor')
                ->save();
            self::$message = 'Successfully set up media folder!';
            Propel::close();
            unset($_SERVER['AIRTIME_CONF']);
        } catch (Exception $e) {
            self::$message = 'Failed to insert ' . self::$path . ' into cc_music_dirs';
            self::$errors[] = self::MEDIA_FOLDER;
        }
    }

    public function checkMusicDirectoryExists($con)
    {
        $entry = CcMusicDirsQuery::create()->findPk(1, $con);

        return isset($entry) && $entry;
    }
}
