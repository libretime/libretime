<?php
//Pear classes.
set_include_path(__DIR__.'/../../airtime_mvc/library/pear' . PATH_SEPARATOR . get_include_path());
require_once('DB.php');

class AirtimeInstall
{
    const CONF_DIR_BINARIES = "/usr/lib/airtime";
    const CONF_DIR_WWW = "/var/www/airtime";
    const CONF_DIR_LOG = "/var/log/airtime";

    public static $databaseTablesCreated = false;

    public static function GetAirtimeSrcDir()
    {
        return __DIR__."/../../airtime_mvc";
    }

    public static function GetUtilsSrcDir()
    {
        return __DIR__."/../../utils";
    }

    /**
     * Ensures that the user is running this PHP script with root
     * permissions. If not running with root permissions, causes the
     * script to exit.
     */
    public static function ExitIfNotRoot()
    {
        // Need to check that we are superuser before running this.
        if(exec("whoami") != "root"){
            echo "Must be root user.\n";
            exit(1);
        }
    }

    public static function GetVersionInstalled()
    {
        global $CC_DBC, $CC_CONFIG;

        if(file_exists('/etc/airtime/airtime.conf')) {
            $values = parse_ini_file('/etc/airtime/airtime.conf', true);
        }
        else {
            echo "New Airtime Install.".PHP_EOL;
            return null;
        }

	    // Database config
        $CC_CONFIG['dsn']['username'] = $values['database']['dbuser'];
        $CC_CONFIG['dsn']['password'] = $values['database']['dbpass'];
        $CC_CONFIG['dsn']['hostspec'] = $values['database']['host'];
        $CC_CONFIG['dsn']['phptype'] = 'pgsql';
        $CC_CONFIG['dsn']['database'] = $values['database']['dbname'];

        $CC_DBC = DB::connect($CC_CONFIG['dsn'], FALSE);
        if (PEAR::isError($CC_DBC)) {
            echo "New Airtime Install.".PHP_EOL;
            return null;
        }
        else {
            $CC_DBC->setFetchMode(DB_FETCHMODE_ASSOC);

            $sql = "SELECT valstr FROM cc_pref WHERE keystr = 'system_version'";
            $version = $CC_DBC->GetOne($sql);

            if (PEAR::isError($version)) {
                return null;
            }
            return $version;
        }
    }

    public static function DbTableExists($p_name)
    {
        global $CC_DBC;
        $sql = "SELECT * FROM ".$p_name;
        $result = $CC_DBC->GetOne($sql);
        if (PEAR::isError($result)) {
            return false;
        }
        return true;
    }

    public static function InstallQuery($sql, $verbose = true)
    {
        global $CC_DBC;
        $result = $CC_DBC->query($sql);
        if (PEAR::isError($result)) {
            echo "Error! ".$result->getMessage()."\n";
            echo "   SQL statement was:\n";
            echo "   ".$sql."\n\n";
        } else {
            if ($verbose) {
                echo "done.\n";
            }
        }
    }

    public static function DbConnect($p_exitOnError = true)
    {
        global $CC_DBC, $CC_CONFIG;
        $CC_DBC = DB::connect($CC_CONFIG['dsn'], FALSE);
        if (PEAR::isError($CC_DBC)) {
            echo $CC_DBC->getMessage().PHP_EOL;
            echo $CC_DBC->getUserInfo().PHP_EOL;
            echo "Database connection problem.".PHP_EOL;
            echo "Check if database '{$CC_CONFIG['dsn']['database']}' exists".
                 " with corresponding permissions.".PHP_EOL;
            if ($p_exitOnError) {
                exit(1);
            }
        } else {
            $CC_DBC->setFetchMode(DB_FETCHMODE_ASSOC);
        }
    }

    
    /* TODO: This function should be moved to the media-monitor
     * install script. */
    public static function InstallStorageDirectory()
    {
        global $CC_CONFIG, $CC_DBC;
        echo "* Storage directory setup".PHP_EOL;
        
        $ini = parse_ini_file(__DIR__."/airtime-install.ini");
        $stor_dir = $ini["storage_dir"];
        
        $dirs = array($stor_dir, $stor_dir."/organize");
        
        foreach ($dirs as $dir){
            if (!file_exists($dir)) {
                @mkdir($dir, 02777, true);
                if (file_exists($dir)) {
                    $rp = realpath($dir);
                    echo "* Directory $rp created".PHP_EOL;
                } else {
                    echo "* Failed creating {$dir}".PHP_EOL;
                    exit(1);
                }
            }
            else if (is_writable($dir)) {
                $rp = realpath($dir);
                echo "* Skipping directory already exists: $rp".PHP_EOL;
            }
            else {
                $rp = realpath($dir);
                echo "* Error: Directory already exists, but is not writable: $rp".PHP_EOL;
                exit(1);
            }

            echo "* Giving Apache permission to access $rp".PHP_EOL;
            $success = chgrp($rp, $CC_CONFIG["webServerUser"]);
            $success = chown($rp, "pypo");
            $success = chmod($rp, 02777);
        }
    }

    public static function CreateDatabaseUser()
    {
        global $CC_CONFIG;

        echo "* Creating Airtime database user".PHP_EOL;

        $username = $CC_CONFIG['dsn']['username'];
        $password = $CC_CONFIG['dsn']['password'];
        $command = "echo \"CREATE USER $username ENCRYPTED PASSWORD '$password' LOGIN CREATEDB NOCREATEUSER;\" | su postgres -c psql 2>/dev/null";

        @exec($command, $output, $results);
        if ($results == 0) {
            echo "* Database user '{$CC_CONFIG['dsn']['username']}' created.".PHP_EOL;
        } else {
            if (count($output) > 0) {
                echo "* Could not create user '{$CC_CONFIG['dsn']['username']}': ".PHP_EOL;
                echo implode(PHP_EOL, $output);
            }
            else {
                echo "* Database user '{$CC_CONFIG['dsn']['username']}' already exists.".PHP_EOL;
            }
        }
    }


    public static function CreateDatabase()
    {
        global $CC_CONFIG;

        echo "* Creating Airtime database".PHP_EOL;

        $database = $CC_CONFIG['dsn']['database'];
        $username = $CC_CONFIG['dsn']['username'];
        #$command = "echo \"CREATE DATABASE $database OWNER $username\" | su postgres -c psql  2>/dev/null";
        $command = "su postgres -c \"createdb $database --owner $username\"";

        @exec($command, $output, $results);
        if ($results == 0) {
            echo "* Database '{$CC_CONFIG['dsn']['database']}' created.".PHP_EOL;
        } else {
            if (count($output) > 0) {
                echo "* Could not create database '{$CC_CONFIG['dsn']['database']}': ".PHP_EOL;
                echo implode(PHP_EOL, $output);
            }
            else {
                echo "* Database '{$CC_CONFIG['dsn']['database']}' already exists.".PHP_EOL;
            }
        }

        $databaseExisted = ($results != 0);

        return $databaseExisted;
    }

    public static function InstallPostgresScriptingLanguage()
    {
        global $CC_DBC;

        // Install postgres scripting language
        $langIsInstalled = $CC_DBC->GetOne('SELECT COUNT(*) FROM pg_language WHERE lanname = \'plpgsql\'');
        if ($langIsInstalled == '0') {
            echo "* Installing Postgres scripting language".PHP_EOL;
            $sql = "CREATE LANGUAGE 'plpgsql'";
            AirtimeInstall::InstallQuery($sql, false);
        } else {
            echo "* Postgres scripting language already installed".PHP_EOL;
        }
    }

    public static function CreateDatabaseTables()
    {
        echo "* Creating database tables".PHP_EOL;

        // Put Propel sql files in Database
        //$command = AirtimeInstall::CONF_DIR_WWW."/library/propel/generator/bin/propel-gen ".AirtimeInstall::CONF_DIR_WWW."/build/ insert-sql";
        $command = AirtimeInstall::CONF_DIR_WWW."/library/propel/generator/bin/propel-gen ".AirtimeInstall::CONF_DIR_WWW."/build/ insert-sql 2>/dev/null";
        @exec($command, $output, $results);
        AirtimeInstall::$databaseTablesCreated = true;
    }

    public static function BypassMigrations($dir, $version)
    {
        $appDir = AirtimeInstall::GetAirtimeSrcDir();
        $command = "php $appDir/library/doctrine/migrations/doctrine-migrations.phar ".
                    "--configuration=$dir/../../DoctrineMigrations/migrations.xml ".
                    "--db-configuration=$appDir/library/doctrine/migrations/migrations-db.php ".
                    "--no-interaction --add migrations:version $version";
        system($command);
    }

    public static function MigrateTablesToVersion($dir, $version)
    {
        $appDir = AirtimeInstall::GetAirtimeSrcDir();
        $command = "php $appDir/library/doctrine/migrations/doctrine-migrations.phar ".
                    "--configuration=$dir/../../DoctrineMigrations/migrations.xml ".
                    "--db-configuration=$appDir/library/doctrine/migrations/migrations-db.php ".
                    "--no-interaction migrations:migrate $version";
        system($command);
    }

    public static function SetAirtimeVersion($p_version)
    {
        global $CC_DBC;
        $sql = "DELETE FROM cc_pref WHERE keystr = 'system_version'";
        $CC_DBC->query($sql);

        $sql = "INSERT INTO cc_pref (keystr, valstr) VALUES ('system_version', '$p_version')";
        $result = $CC_DBC->query($sql);
        if (PEAR::isError($result)) {
            return false;
        }
        return true;
    }
    
    public static function SetUniqueId()
    {
        global $CC_DBC;

        $uniqueId = md5(uniqid("", true));

        $sql = "INSERT INTO cc_pref (keystr, valstr) VALUES ('uniqueId', '$uniqueId')";
        $result = $CC_DBC->query($sql);
        if (PEAR::isError($result)) {
            return false;
        }
        return true;
    }

    public static function GetAirtimeVersion()
    {
        global $CC_DBC;
        $sql = "SELECT valstr FROM cc_pref WHERE keystr = 'system_version'";
        $version = $CC_DBC->GetOne($sql);

        if (PEAR::isError($version)) {
            return false;
        }
        return $version;
    }

    public static function DeleteFilesRecursive($p_path)
    {
        $command = "rm -rf \"$p_path\"";
        exec($command);
    }

    public static function CreateSymlinksToUtils()
    {
        echo "* Creating /usr/bin symlinks".PHP_EOL;
        AirtimeInstall::RemoveSymlinks();

        echo "* Installing airtime-import".PHP_EOL;
        $dir = AirtimeInstall::CONF_DIR_BINARIES."/utils/airtime-import";
        exec("ln -s $dir /usr/bin/airtime-import");

        echo "* Installing airtime-update-db-settings".PHP_EOL;
        $dir = AirtimeInstall::CONF_DIR_BINARIES."/utils/airtime-update-db-settings";
        exec("ln -s $dir /usr/bin/airtime-update-db-settings");

        echo "* Installing airtime-check-system".PHP_EOL;
        $dir = AirtimeInstall::CONF_DIR_BINARIES."/utils/airtime-check-system";
        exec("ln -s $dir /usr/bin/airtime-check-system");
    }

    public static function RemoveSymlinks()
    {
        exec("rm -f /usr/bin/airtime-import");
        exec("rm -f /usr/bin/airtime-update-db-settings");
        exec("rm -f /usr/bin/airtime-check-system");
    }

    public static function InstallPhpCode()
    {
        global $CC_CONFIG;
        echo "* Installing PHP code to ".AirtimeInstall::CONF_DIR_WWW.PHP_EOL;
        exec("mkdir -p ".AirtimeInstall::CONF_DIR_WWW);
        exec("cp -R ".AirtimeInstall::GetAirtimeSrcDir()."/* ".AirtimeInstall::CONF_DIR_WWW);

    }

    public static function UninstallPhpCode()
    {
        echo "* Removing PHP code from ".AirtimeInstall::CONF_DIR_WWW.PHP_EOL;
        exec('rm -rf "'.AirtimeInstall::CONF_DIR_WWW.'"');
    }

    public static function InstallBinaries()
    {
        echo "* Installing binaries to ".AirtimeInstall::CONF_DIR_BINARIES.PHP_EOL;
        exec("mkdir -p ".AirtimeInstall::CONF_DIR_BINARIES);
        exec("cp -R ".AirtimeInstall::GetUtilsSrcDir()." ".AirtimeInstall::CONF_DIR_BINARIES);
    }

    public static function UninstallBinaries()
    {
        echo "* Removing Airtime binaries from ".AirtimeInstall::CONF_DIR_BINARIES.PHP_EOL;
        exec('rm -rf "'.AirtimeInstall::CONF_DIR_BINARIES.'"');
    }

    public static function DirCheck()
    {
        echo "Legend: \"+\" means the dir/file exists, \"-\" means that it does not.".PHP_EOL;
        $dirs = array(AirtimeInstall::CONF_DIR_BINARIES,
                      AirtimeInstall::CONF_DIR_WWW,
                      AirtimeIni::CONF_FILE_AIRTIME,
                      AirtimeIni::CONF_FILE_LIQUIDSOAP,
                      AirtimeIni::CONF_FILE_PYPO,
                      AirtimeIni::CONF_FILE_RECORDER,
                      "/usr/lib/airtime/pypo",
                      "/usr/lib/airtime/show-recorder",
                      "/var/log/airtime",
                      "/var/log/airtime/pypo",
                      "/var/log/airtime/show-recorder",
                      "/var/tmp/airtime/pypo",
                      "/var/tmp/airtime/show-recorder");
         foreach ($dirs as $f) {
             if (file_exists($f)) {
                 echo "+ $f".PHP_EOL;
             } else {
                 echo "- $f".PHP_EOL;
             }
         }
    }

    public static function CreateZendPhpLogFile(){
        global $CC_CONFIG;

        echo "* Creating logs directory ".AirtimeInstall::CONF_DIR_LOG.PHP_EOL;

        $path = AirtimeInstall::CONF_DIR_LOG;
        $file = $path.'/zendphp.log';
        if (!file_exists($path)){
            mkdir($path, 0755, true);
        }

        touch($file);
        chmod($file, 0755);
        chown($file, $CC_CONFIG['webServerUser']);
        chgrp($file, $CC_CONFIG['webServerUser']);
    }

    public static function RemoveLogDirectories(){
        $path = AirtimeInstall::CONF_DIR_LOG;
        echo "* Removing logs directory ".$path.PHP_EOL;

        exec("rm -rf \"$path\"");
    }
}
