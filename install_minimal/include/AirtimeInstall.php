<?php
set_include_path(__DIR__.'/../../airtime_mvc/library' . PATH_SEPARATOR . get_include_path());
//Zend framework
if (file_exists('/usr/share/php/libzend-framework-php')){
    set_include_path('/usr/share/php/libzend-framework-php' . PATH_SEPARATOR . get_include_path());
}
#require_once('Zend/Loader/Autoloader.php');

class AirtimeInstall
{
    const CONF_DIR_BINARIES = "/usr/lib/airtime";
    const CONF_DIR_WWW = "/usr/share/airtime";
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
        if(posix_geteuid() != 0){
            echo "Must be root user.\n";
            exit(1);
        }
    }

    /**
     * Return the version of Airtime currently installed.
     * If not installed, return null.
     *
     * @return NULL|string
     */
    public static function GetVersionInstalled()
    {
        try {
            $con = Propel::getConnection();
        } catch (PropelException $e) {
            return null;
        }

        if (file_exists('/etc/airtime/airtime.conf')) {
            $values = parse_ini_file('/etc/airtime/airtime.conf', true);
        }
        else {
            return null;
        }

        $sql = "SELECT valstr FROM cc_pref WHERE keystr = 'system_version' LIMIT 1";
        
        try {
            $version = $con->query($sql)->fetchColumn(0);
        } catch (PDOException $e){
            // no pref table therefore Airtime is not installed.
            //We only get here if airtime database exists, but the table doesn't
            //This state sometimes happens if a previous Airtime uninstall couldn't remove
            //the database because it was busy, so it just removed the tables instead.
            return null;
        }

        //if version is empty string, then version is older than version 1.8.0
        if ($version == '') {
            try {
                // If this table exists, then it's version 1.7.0
                $sql = "SELECT * FROM cc_show_rebroadcast LIMIT 1";
                $result = $con->query($sql)->fetchColumn(0);
                $version = "1.7.0";
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
            $sql = "SELECT * FROM ".$p_name." LIMIT 1";
            $con->query($sql);
        } catch (PDOException $e){
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
            echo "Error!\n".$e->getMessage()."\n";
            echo "   SQL statement was:\n";
            echo "   ".$sql."\n\n";
        }
    }

    public static function DropSequence($p_sequenceName)
    {
        AirtimeInstall::InstallQuery("DROP SEQUENCE IF EXISTS $p_sequenceName", false);
    }

    /**
     * Try to connect to the database.  Return true on success, false on failure.
     * @param boolean $p_exitOnError
     *     Exit the program on failure.
     * @return boolean
     */
    public static function DbConnect($p_exitOnError = true)
    {
        $CC_CONFIG = Config::getConfig();
        try {
            $con = Propel::getConnection();
        } catch (Exception $e) {
            echo $e->getMessage().PHP_EOL;
            echo "Database connection problem.".PHP_EOL;
            echo "Check if database '{$CC_CONFIG['dsn']['database']}' exists".
                " with corresponding permissions.".PHP_EOL;
            if ($p_exitOnError) {
                exit(1);
            }
            return false;
        }
        return true;
    }


    /* TODO: This function should be moved to the media-monitor
     * install script. */
    public static function InstallStorageDirectory()
    {
        $CC_CONFIG = Config::getConfig();
        echo "* Storage directory setup".PHP_EOL;

        $ini = parse_ini_file(__DIR__."/airtime-install.ini");
        $stor_dir = $ini["storage_dir"];

        $dirs = array($stor_dir, $stor_dir."/organize");

        foreach ($dirs as $dir){
            if (!file_exists($dir)) {
                if (mkdir($dir, 02775, true)){
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
            $success = chmod($rp, 02775);
        }
    }

    public static function CreateDatabaseUser()
    {
        $CC_CONFIG = Config::getConfig();

        echo " * Creating Airtime database user".PHP_EOL;

        $username = $CC_CONFIG['dsn']['username'];
        $password = $CC_CONFIG['dsn']['password'];
        $command = "echo \"CREATE USER $username ENCRYPTED PASSWORD '$password' LOGIN CREATEDB NOCREATEUSER;\" | su postgres -c psql 2>/dev/null";

        @exec($command, $output, $results);
        if ($results == 0) {
            echo "  * Database user '{$CC_CONFIG['dsn']['username']}' created.".PHP_EOL;
        } else {
            if (count($output) > 0) {
                echo "  * Could not create user '{$CC_CONFIG['dsn']['username']}': ".PHP_EOL;
                echo implode(PHP_EOL, $output);
            }
            else {
                echo "  * Database user '{$CC_CONFIG['dsn']['username']}' already exists.".PHP_EOL;
            }
        }
    }


    public static function CreateDatabase()
    {
        $CC_CONFIG = Config::getConfig();

        echo " * Creating Airtime database".PHP_EOL;

        $database = $CC_CONFIG['dsn']['database'];
        $username = $CC_CONFIG['dsn']['username'];
        #$command = "echo \"CREATE DATABASE $database OWNER $username\" | su postgres -c psql  2>/dev/null";

        $command = "su postgres -c \"psql -l | cut -f2 -d' ' | grep -w 'airtime'\";";
        exec($command, $output, $rv);

        if ($rv == 0) {
            //database already exists
            return true;
        }

        $command = "su postgres -c \"createdb $database --encoding UTF8 --owner $username\"";

        @exec($command, $output, $results);
        if ($results == 0) {
            echo "  * Database '{$CC_CONFIG['dsn']['database']}' created.".PHP_EOL;
        } else {
            if (count($output) > 0) {
                echo "  * Could not create database '{$CC_CONFIG['dsn']['database']}': ".PHP_EOL;
                echo implode(PHP_EOL, $output);
            }
            else {
                echo "  * Database '{$CC_CONFIG['dsn']['database']}' already exists.".PHP_EOL;
            }
        }

        $databaseExisted = ($results != 0);

        return $databaseExisted;
    }

    public static function InstallPostgresScriptingLanguage()
    {
        $con = Propel::getConnection();

        // Install postgres scripting language
        $sql = 'SELECT COUNT(*) FROM pg_language WHERE lanname = \'plpgsql\'';
        $langIsInstalled = $con->query($sql)->fetchColumn(0);
        if ($langIsInstalled == '0') {
            echo " * Installing Postgres scripting language".PHP_EOL;
            $sql = "CREATE LANGUAGE 'plpgsql'";
            AirtimeInstall::InstallQuery($sql, false);
        } else {
            echo "  * Postgres scripting language already installed".PHP_EOL;
        }
    }

    public static function CreateDatabaseTables($p_dbuser, $p_dbpasswd, $p_dbname, $p_dbhost)
    {
        echo " * Creating database tables".PHP_EOL;

        // Put Propel sql files in Database
        //$command = AirtimeInstall::CONF_DIR_WWW."/library/propel/generator/bin/propel-gen ".AirtimeInstall::CONF_DIR_WWW."/build/ insert-sql 2>/dev/null";

        $dir = self::GetAirtimeSrcDir()."/build/sql/";
        $files = array("schema.sql", "sequences.sql", "views.sql", "triggers.sql", "defaultdata.sql");

        foreach ($files as $f){
            $command = "export PGPASSWORD=$p_dbpasswd && psql --username $p_dbuser --dbname $p_dbname --host $p_dbhost --file $dir$f 2>/dev/null";
            @exec($command, $output, $results);
        }

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
        $con = Propel::getConnection();
        $sql = "DELETE FROM cc_pref WHERE keystr = 'system_version'";
        $con->exec($sql);

        $sql = "INSERT INTO cc_pref (keystr, valstr) VALUES ('system_version', '$p_version')";
        $result = $con->exec($sql);
        if ($result < 1) {
            return false;
        }
        return true;
    }

    public static function SetUniqueId()
    {
        $con = Propel::getConnection();
        $uniqueId = md5(uniqid("", true));

        $sql = "INSERT INTO cc_pref (keystr, valstr) VALUES ('uniqueId', '$uniqueId')";
        $result = $con->exec($sql);
        if ($result < 1) {
            return false;
        }
        return true;
    }

    public static function SetDefaultTimezone()
    {
        $con = Propel::getConnection();
        // we need to run php as commandline because we want to get the timezone in cli php.ini file
        //$defaultTimezone = exec("php -r 'echo date_default_timezone_get().PHP_EOL;'");
        $defaultTimezone = exec("cat /etc/timezone");
        $defaultTimezone = trim($defaultTimezone);
        if((!in_array($defaultTimezone, DateTimeZone::listIdentifiers()))){
        	$defaultTimezone = "UTC";
        }
        $sql = "INSERT INTO cc_pref (keystr, valstr) VALUES ('timezone', '$defaultTimezone')";
        $result = $con->exec($sql);
        if ($result < 1) {
            return false;
        }
        $sql = "INSERT INTO cc_pref (subjid, keystr, valstr) VALUES (1, 'user_timezone', '$defaultTimezone')";
        $result = $con->exec($sql);
        if ($result < 1) {
            return false;
        }
        return true;
    }

    public static function SetImportTimestamp()
    {
        $con = Propel::getConnection();
        $sql = "INSERT INTO cc_pref (keystr, valstr) VALUES ('import_timestamp', '0')";
        $result = $con->exec($sql);
        if ($result < 1) {
            return false;
        }
        return true;
    }


    public static function GetAirtimeVersion()
    {
        $con = Propel::getConnection();
        $sql = "SELECT valstr FROM cc_pref WHERE keystr = 'system_version' LIMIT 1";
        $version = $con->query($sql)->fetchColumn(0);
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
        $dir = AirtimeInstall::CONF_DIR_BINARIES."/utils/airtime-import/airtime-import";
        exec("ln -s $dir /usr/bin/airtime-import");

        echo "* Installing airtime-update-db-settings".PHP_EOL;
        $dir = AirtimeInstall::CONF_DIR_BINARIES."/utils/airtime-update-db-settings";
        exec("ln -s $dir /usr/bin/airtime-update-db-settings");

        echo "* Installing airtime-check-system".PHP_EOL;
        $dir = AirtimeInstall::CONF_DIR_BINARIES."/utils/airtime-check-system";
        exec("ln -s $dir /usr/bin/airtime-check-system");

        echo "* Installing airtime-user".PHP_EOL;
        $dir = AirtimeInstall::CONF_DIR_BINARIES."/utils/airtime-user";
        exec("ln -s $dir /usr/bin/airtime-user");

        echo "* Installing airtime-log".PHP_EOL;
        $dir = AirtimeInstall::CONF_DIR_BINARIES."/utils/airtime-log";
        exec("ln -s $dir /usr/bin/airtime-log");
    }

    public static function RemoveSymlinks()
    {
        exec("rm -f /usr/bin/airtime-import");
        exec("rm -f /usr/bin/airtime-update-db-settings");
        exec("rm -f /usr/bin/airtime-check-system");
        exec("rm -f /usr/bin/airtime-user");
        exec("rm -f /usr/bin/airtime-log");
        exec("rm -f /usr/bin/airtime-clean-storage");
    }

    public static function InstallPhpCode()
    {
        $CC_CONFIG = Config::getConfig();
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
                      "/var/log/airtime",
                      "/var/log/airtime/pypo",
                      "/var/tmp/airtime/pypo");
         foreach ($dirs as $f) {
             if (file_exists($f)) {
                 echo "+ $f".PHP_EOL;
             } else {
                 echo "- $f".PHP_EOL;
             }
         }
    }

    public static function CreateZendPhpLogFile(){
        $CC_CONFIG = Config::getConfig();

        $path = AirtimeInstall::CONF_DIR_LOG;
        $file = $path.'/zendphp.log';
        if (!file_exists($path)){
            mkdir($path, 0755, true);
        }

        touch($file);
        chmod($file, 0644);
        chown($file, $CC_CONFIG['webServerUser']);
        chgrp($file, $CC_CONFIG['webServerUser']);
    }

    public static function RemoveLogDirectories(){
        $path = AirtimeInstall::CONF_DIR_LOG;
        echo "* Removing logs directory ".$path.PHP_EOL;

        exec("rm -rf \"$path\"");
    }

    public static function CreateCronFile(){
        echo "* Creating Cron File".PHP_EOL;
        // Create CRON task to run every day.  Time of day is initialized to a random time.
        $hour = rand(0,23);
        $minute = rand(0,59);

        $fp = fopen('/etc/cron.d/airtime-crons','w');
        fwrite($fp, "$minute $hour * * * root /usr/lib/airtime/utils/phone_home_stat\n");
        fclose($fp);
    }

    public static function removeVirtualEnvDistributeFile(){
        echo "* Removing distribute-0.6.10.tar.gz".PHP_EOL;
        if(file_exists('/usr/share/python-virtualenv/distribute-0.6.10.tar.gz')){
            exec("rm -f /usr/share/python-virtualenv/distribute-0.6.10.tar.gz");
        }
    }

    public static function printUsage($opts)
    {
        $msg = $opts->getUsageMessage();
        echo PHP_EOL."Usage: airtime-install [options]";
        echo substr($msg, strpos($msg, "\n")).PHP_EOL;
    }

    public static function getOpts()
    {
        try {
            $autoloader = Zend_Loader_Autoloader::getInstance();
            $opts = new Zend_Console_Getopt(
                array(
                    'help|h' => 'Displays usage information.',
                    'overwrite|o' => 'Overwrite any existing config files.',
                    'preserve|p' => 'Keep any existing config files.',
                    'no-db|n' => 'Turn off database install.',
                    'reinstall|r' => 'Force a fresh install of this Airtime Version',
                    'webonly|w' => 'Install only web files'
                )
            );
            $opts->parse();
        } catch (Zend_Console_Getopt_Exception $e) {
            print $e->getMessage() .PHP_EOL;
            AirtimeInstall::printUsage($opts);
            return NULL;
        }
        return $opts;
    }

    public static function checkPHPVersion()
    {
        if (PHP_VERSION_ID < 50400)
        {
            echo "Error: Airtime requires PHP 5.4 or greater.";
            return false;
        }
        return true;
    }
}
