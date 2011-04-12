<?php

require_once(dirname(__FILE__).'/../../library/pear/DB.php');
require_once(dirname(__FILE__).'/../../application/configs/conf.php');

class AirtimeInstall {
    /**
     * Ensures that the user is running this PHP script with root
     * permissions. If not running with root permissions, causes the
     * script to exit.
     */
    static function ExitIfNotRoot()
    {
        // Need to check that we are superuser before running this.
        if(exec("whoami") != "root"){
            echo "Must be root user.\n";
            exit(1);
        }
    }


    static function DbTableExists($p_name)
    {
        global $CC_DBC;
        $sql = "SELECT * FROM ".$p_name;
        $result = $CC_DBC->GetOne($sql);
        if (PEAR::isError($result)) {
            return false;
        }
        return true;
    }

    static function InstallQuery($sql, $verbose = true)
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

    static function DbConnect($p_exitOnError = true)
    {
        global $CC_DBC, $CC_CONFIG;
        $CC_DBC = DB::connect($CC_CONFIG['dsn'], TRUE);
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
            echo "* Connected to database".PHP_EOL;
            $CC_DBC->setFetchMode(DB_FETCHMODE_ASSOC);
        }
    }

    static function ChangeDirOwnerToWebserver($filePath)
    {
        global $CC_CONFIG;
        $success = chgrp($filePath, $CC_CONFIG["webServerUser"]);
        $fileperms=@fileperms($filePath);
        $fileperms = $fileperms | 0x0010; // group write bit
        $fileperms = $fileperms | 0x0400; // group sticky bit
        chmod($filePath, $fileperms);
    }

    public static function SetupStorageDirectory($CC_CONFIG)
    {
        global $CC_CONFIG, $CC_DBC;

        echo PHP_EOL."*** Directory Setup ***".PHP_EOL;
        foreach (array('baseFilesDir', 'storageDir') as $d) {
            if ( !file_exists($CC_CONFIG[$d]) ) {
                @mkdir($CC_CONFIG[$d], 02775, true);
                if (file_exists($CC_CONFIG[$d])) {
                    $rp = realpath($CC_CONFIG[$d]);
                    echo "* Directory $rp created".PHP_EOL;
                } else {
                    echo "* Failed creating {$CC_CONFIG[$d]}".PHP_EOL;
                    exit(1);
                }
            } elseif (is_writable($CC_CONFIG[$d])) {
                $rp = realpath($CC_CONFIG[$d]);
                echo "* Skipping directory already exists: $rp".PHP_EOL;
            } else {
                $rp = realpath($CC_CONFIG[$d]);
                echo "* WARNING: Directory already exists, but is not writable: $rp".PHP_EOL;
            }
            $CC_CONFIG[$d] = $rp;
        }
    }

    public static function CreateDatabaseUser()
    {
        global $CC_CONFIG;
        // Create the database user
        $command = "sudo -u postgres psql postgres --command \"CREATE USER {$CC_CONFIG['dsn']['username']} "
        ." ENCRYPTED PASSWORD '{$CC_CONFIG['dsn']['password']}' LOGIN CREATEDB NOCREATEUSER;\" 2>/dev/null";

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

        $command = "sudo -u postgres createdb {$CC_CONFIG['dsn']['database']} --owner {$CC_CONFIG['dsn']['username']} 2> /dev/null";
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
        // Put Propel sql files in Database
        $command = __DIR__."/../../library/propel/generator/bin/propel-gen ../build/ insert-sql 2>propel-error.log";
        @exec($command, $output, $results);
    }

    public static function MigrateTables($dir)
    {
        $command = "php $dir/../library/doctrine/migrations/doctrine-migrations.phar --configuration=$dir/DoctrineMigrations/migrations.xml --db-configuration=$dir/../library/doctrine/migrations/migrations-db.php --no-interaction migrations:migrate";
        system($command);
    }

    public static function DeleteFilesRecursive($p_path)
    {
        $command = "rm -rf $p_path";
        exec($command);
    }

    public static function CreateSymlinks(){
        AirtimeInstall::RemoveSymlinks();

        $dir = realpath(__DIR__."/../../utils/airtime-import");
        exec("ln -s $dir /usr/bin/airtime-import");

        $dir = realpath(__DIR__."/../../utils/airtime-clean-storage");
        exec("ln -s $dir /usr/bin/airtime-clean-storage");

        $dir = realpath(__DIR__."/../../utils/airtime-update-db-settings");
        exec("ln -s $dir /usr/bin/airtime-update-db-settings");
    }

    public static function RemoveSymlinks(){
        exec("rm -f /usr/bin/airtime-import");
        exec("rm -f /usr/bin/airtime-clean-storage");
        exec("rm -f /usr/bin/airtime-update-db-settings");
    }
}
