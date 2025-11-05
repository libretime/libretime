<?php

set_include_path(__DIR__ . '/../../legacy/library' . PATH_SEPARATOR . get_include_path());

// require_once('Zend/Loader/Autoloader.php');
class AirtimeInstall
{
    public const CONF_DIR_BINARIES = '/usr/lib/airtime';
    public const CONF_DIR_WWW = '/usr/share/airtime';
    public const CONF_DIR_LOG = LIBRETIME_LOG_DIR;
    public static $databaseTablesCreated = false;

    public static function GetAirtimeSrcDir()
    {
        return __DIR__ . '/../../..';
    }

    public static function GetUtilsSrcDir()
    {
        return __DIR__ . '/../../../../utils';
    }

    /**
     * Ensures that the user is running this PHP script with root
     * permissions. If not running with root permissions, causes the
     * script to exit.
     */
    public static function ExitIfNotRoot()
    {
        // Need to check that we are superuser before running this.
        if (posix_geteuid() != 0) {
            echo "Must be root user.\n";

            exit(1);
        }
    }

    /**
     * Return the version of Airtime currently installed.
     * If not installed, return null.
     *
     * @return null|string
     */
    public static function GetVersionInstalled()
    {
        try {
            $con = Propel::getConnection();
        } catch (PropelException $e) {
            return null;
        }
        if (file_exists('/etc/libretime/config.yml')) {
            $values = parse_ini_file('/etc/libretime/config.yml', true);
        } else {
            return null;
        }
        $sql = "SELECT valstr FROM cc_pref WHERE keystr = 'system_version' LIMIT 1";

        try {
            $version = $con->query($sql)->fetchColumn(0);
        } catch (PDOException $e) {
            // no pref table therefore Airtime is not installed.
            // We only get here if airtime database exists, but the table doesn't
            // This state sometimes happens if a previous Airtime uninstall couldn't remove
            // the database because it was busy, so it just removed the tables instead.
            return null;
        }
        // if version is empty string, then version is older than version 1.8.0
        if ($version == '') {
            try {
                // If this table exists, then it's version 1.7.0
                $sql = 'SELECT * FROM cc_show_rebroadcast LIMIT 1';
                $result = $con->query($sql)->fetchColumn(0);
                $version = '1.7.0';
            } catch (Exception $e) {
                $version = null;
            }
        }

        return $version;
    }

    public static function DbTableExists($p_name)
    {
        $con = Propel::getConnection();

        try {
            $sql = 'SELECT * FROM ' . $p_name . ' LIMIT 1';
            $con->query($sql);
        } catch (PDOException $e) {
            return false;
        }

        return true;
    }

    public static function InstallQuery($sql, $verbose = true)
    {
        $con = Propel::getConnection();

        try {
            $con->exec($sql);
            if ($verbose) {
                echo "done.\n";
            }
        } catch (Exception $e) {
            echo "Error!\n" . $e->getMessage() . "\n";
            echo "   SQL statement was:\n";
            echo '   ' . $sql . "\n\n";
        }
    }

    public static function DropSequence($p_sequenceName)
    {
        AirtimeInstall::InstallQuery("DROP SEQUENCE IF EXISTS {$p_sequenceName}", false);
    }

    /**
     * Try to connect to the database.  Return true on success, false on failure.
     *
     * @param bool $p_exitOnError
     *                            Exit the program on failure
     *
     * @return bool
     */
    public static function DbConnect($p_exitOnError = true)
    {
        $CC_CONFIG = Config::getConfig();

        try {
            $con = Propel::getConnection();
        } catch (Exception $e) {
            echo $e->getMessage() . PHP_EOL;
            echo 'Database connection problem.' . PHP_EOL;
            echo "Check if database '{$CC_CONFIG['dsn']['database']}' exists"
                . ' with corresponding permissions.' . PHP_EOL;
            if ($p_exitOnError) {
                exit(1);
            }

            return false;
        }

        return true;
    }

    public static function CreateDatabaseUser()
    {
        $CC_CONFIG = Config::getConfig();
        echo ' * Creating Airtime database user' . PHP_EOL;
        $username = $CC_CONFIG['dsn']['username'];
        $password = $CC_CONFIG['dsn']['password'];
        $command = "echo \"CREATE USER {$username} ENCRYPTED PASSWORD '{$password}' LOGIN CREATEDB NOCREATEUSER;\" | su postgres -c /usr/bin/psql 2>/dev/null";
        @exec($command, $output, $results);
        if ($results == 0) {
            echo "  * Database user '{$CC_CONFIG['dsn']['username']}' created." . PHP_EOL;
        } else {
            if (count($output) > 0) {
                echo "  * Could not create user '{$CC_CONFIG['dsn']['username']}': " . PHP_EOL;
                echo implode(PHP_EOL, $output);
            } else {
                echo "  * Database user '{$CC_CONFIG['dsn']['username']}' already exists." . PHP_EOL;
            }
        }
    }

    public static function CreateDatabase()
    {
        $CC_CONFIG = Config::getConfig();
        $host = $CC_CONFIG['dsn']['host'];
        $port = $CC_CONFIG['dsn']['port'];
        $database = $CC_CONFIG['dsn']['database'];
        $username = $CC_CONFIG['dsn']['username'];
        $password = $CC_CONFIG['dsn']['password'];

        echo ' * Creating Airtime database: ' . $database . PHP_EOL;

        $dbExists = false;

        try {
            $con = pg_connect("host={$host} port={$port} user={$username} password={$password}");

            pg_query($con, 'CREATE DATABASE ' . $database . ' WITH ENCODING \'UTF8\' TEMPLATE template0 OWNER ' . $username . ';');
        } catch (Exception $e) {
            // rethrow if not a "database already exists" error
            if ($e->getCode() != 2 && strpos($e->getMessage(), 'already exists') !== false) {
                throw $e;
            }
            echo '  * Database already exists.' . PHP_EOL;
            $dbExists = true;
        }

        if (!$dbExists) {
            echo "  * Database {$database} created." . PHP_EOL;
        }

        return $dbExists;
    }

    public static function InstallPostgresScriptingLanguage()
    {
        $con = Propel::getConnection();
        // Install postgres scripting language
        $sql = 'SELECT COUNT(*) FROM pg_language WHERE lanname = \'plpgsql\'';
        $langIsInstalled = $con->query($sql)->fetchColumn(0);
        if ($langIsInstalled == '0') {
            echo ' * Installing Postgres scripting language' . PHP_EOL;
            $sql = "CREATE LANGUAGE 'plpgsql'";
            AirtimeInstall::InstallQuery($sql, false);
        } else {
            echo '  * Postgres scripting language already installed' . PHP_EOL;
        }
    }

    public static function CreateDatabaseTables($dbuser, $dbpasswd, $dbname, $dbhost, $dbport)
    {
        echo ' * Creating database tables' . PHP_EOL;
        $con = Propel::getConnection();
        $sqlDir = dirname(ROOT_PATH) . '/api/libretime_api/legacy/migrations/sql/';
        $files = ['schema.sql', 'data.sql'];
        foreach ($files as $file) {
            try {
                $sql = file_get_contents($sqlDir . $file);
                $con->exec($sql);
            } catch (PDOException $e) {
                echo $e->getMessage();

                throw $e;
            }
        }
        AirtimeInstall::$databaseTablesCreated = true;
    }

    public static function SetAirtimeVersion($p_version)
    {
        $con = Propel::getConnection();
        $sql = "DELETE FROM cc_pref WHERE keystr = 'system_version'";
        $con->exec($sql);
        Application_Model_Preference::SetAirtimeVersion($p_version);
    }

    public static function SetUniqueId()
    {
        $uniqueId = md5(uniqid('', true));
        Application_Model_Preference::SetUniqueId($uniqueId);
    }

    public static function GetAirtimeVersion()
    {
        $config = Config::getConfig();

        return $config['airtime_version'];
    }

    public static function DeleteFilesRecursive($p_path)
    {
        $command = "rm -rf \"{$p_path}\"";
        exec($command);
    }

    public static function InstallPhpCode()
    {
        $CC_CONFIG = Config::getConfig();
        echo '* Installing PHP code to ' . AirtimeInstall::CONF_DIR_WWW . PHP_EOL;
        exec('mkdir -p ' . AirtimeInstall::CONF_DIR_WWW);
        exec('cp -R ' . AirtimeInstall::GetAirtimeSrcDir() . '/* ' . AirtimeInstall::CONF_DIR_WWW);
    }

    public static function UninstallPhpCode()
    {
        echo '* Removing PHP code from ' . AirtimeInstall::CONF_DIR_WWW . PHP_EOL;
        exec('rm -rf "' . AirtimeInstall::CONF_DIR_WWW . '"');
    }

    public static function DirCheck()
    {
        echo 'Legend: "+" means the dir/file exists, "-" means that it does not.' . PHP_EOL;
        $dirs = [
            AirtimeInstall::CONF_DIR_BINARIES,
            AirtimeInstall::CONF_DIR_WWW,
            AirtimeIni::CONF_FILE_AIRTIME,
            AirtimeIni::CONF_FILE_LIQUIDSOAP,
            AirtimeIni::CONF_FILE_PYPO,
            AirtimeIni::CONF_FILE_RECORDER,
            '/usr/lib/airtime/pypo',
            '/var/log/airtime',
            '/var/log/airtime/pypo',
            '/var/tmp/airtime/pypo',
        ];
        foreach ($dirs as $f) {
            if (file_exists($f)) {
                echo "+ {$f}" . PHP_EOL;
            } else {
                echo "- {$f}" . PHP_EOL;
            }
        }
    }

    public static function CreateZendPhpLogFile()
    {
        $CC_CONFIG = Config::getConfig();
        $path = AirtimeInstall::CONF_DIR_LOG;
        $file = $path . '/legacy.log';
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }
        touch($file);
        chmod($file, 0644);
        chown($file, $CC_CONFIG['webServerUser']);
        chgrp($file, $CC_CONFIG['webServerUser']);
    }

    public static function RemoveLogDirectories()
    {
        $path = AirtimeInstall::CONF_DIR_LOG;
        echo '* Removing logs directory ' . $path . PHP_EOL;
        exec("rm -rf \"{$path}\"");
    }

    public static function removeVirtualEnvDistributeFile()
    {
        echo '* Removing distribute-0.6.10.tar.gz' . PHP_EOL;
        if (file_exists('/usr/share/python-virtualenv/distribute-0.6.10.tar.gz')) {
            exec('rm -f /usr/share/python-virtualenv/distribute-0.6.10.tar.gz');
        }
    }

    public static function printUsage($opts)
    {
        $msg = $opts->getUsageMessage();
        echo PHP_EOL . 'Usage: airtime-install [options]';
        echo substr($msg, strpos($msg, "\n")) . PHP_EOL;
    }

    public static function getOpts()
    {
        try {
            $autoloader = Zend_Loader_Autoloader::getInstance();
            $opts = new Zend_Console_Getopt(
                [
                    'help|h' => 'Displays usage information.',
                    'overwrite|o' => 'Overwrite any existing config files.',
                    'preserve|p' => 'Keep any existing config files.',
                    'no-db|n' => 'Turn off database install.',
                    'reinstall|r' => 'Force a fresh install of this Airtime Version',
                    'webonly|w' => 'Install only web files',
                ]
            );
            $opts->parse();
        } catch (Zend_Console_Getopt_Exception $e) {
            echo $e->getMessage() . PHP_EOL;
            AirtimeInstall::printUsage($opts);

            return null;
        }

        return $opts;
    }

    public static function checkPHPVersion()
    {
        if (PHP_VERSION_ID < 50300) {
            echo 'Error: Airtime requires PHP 5.3 or greater.';

            return false;
        }

        return true;
    }
}
