<?php
require_once "AirtimeInstall.php";

class TestHelper
{
    public static function loginUser()
    {
        $authAdapter = Application_Model_Auth::getAuthAdapter();

        //pass to the adapter the submitted username and password
        $authAdapter->setIdentity('admin')
                    ->setCredential('admin');

        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($authAdapter);
        if ($result->isValid()) {
            //all info about this user from the login table omit only the password
            $userInfo = $authAdapter->getResultRowObject(null, 'password');

            //the default storage is a session with namespace Zend_Auth
            $authStorage = $auth->getStorage();
            $authStorage->write($userInfo);
        }
    }

    public static function getDbZendConfig()
    {
        return new Zend_Config(
            array(
                'host'     => '127.0.0.1',
                'dbname'   => 'airtime_test',
                'username' => 'airtime',
                'password' => 'airtime'
            )
        );
    }

    public static function installTestDatabase()
    {
        //We need to load the config before our app bootstrap runs. The config
        //is normally
        $CC_CONFIG = Config::getConfig();
        
        $dbuser = $CC_CONFIG['dsn']['username'];
        $dbpasswd = $CC_CONFIG['dsn']['password'];
        $dbname = $CC_CONFIG['dsn']['database'];
        $dbhost = $CC_CONFIG['dsn']['hostspec'];
    
        $databaseAlreadyExists = AirtimeInstall::createDatabase();
        if ($databaseAlreadyExists)
        {
            //Truncate all the tables
            $con = Propel::getConnection();
            $sql = "select * from pg_tables where tableowner = 'airtime'";
            try {
                $rows = $con->query($sql)->fetchAll();
            } catch (Exception $e) {
                $rows = array();
            }
            
            //Add any tables that shouldn't be cleared here.
            //   cc_subjs - Most of Airtime requires an admin account to work, which has id=1,
            //              so don't clear it.
            //   cc_music_dirs - Has foreign key constraints against cc_files, so we clear cc_files 
            //                   first and clear cc_music_dirs after
            $tablesToNotClear = array("cc_subjs", "cc_music_dirs");

            $con->beginTransaction();
            foreach ($rows as $row) {
                $tablename = $row["tablename"];
                if (in_array($tablename, $tablesToNotClear))
                {
                    continue;
                }
                //echo "   * Clearing database table $tablename...";

                //TRUNCATE is actually slower than DELETE in many cases:
                //http://stackoverflow.com/questions/11419536/postgresql-truncation-speed
                //$sql = "TRUNCATE TABLE $tablename CASCADE";
                $sql = "DELETE FROM $tablename";
                AirtimeInstall::InstallQuery($sql, false);
            }

            //Now that cc_files is empty, clearing cc_music_dirs should work
            $sql = "DELETE FROM cc_music_dirs";
            AirtimeInstall::InstallQuery($sql, false);
            
            //  Because files are stored relative to their watch directory,
            //  we need to set the "stor" path before we can successfully
            //  create a fake file in the database.
            //Copy paste from airtime-db-install.php:
            $stor_dir = "/tmp";
            $con = Propel::getConnection();
            $sql = "INSERT INTO cc_music_dirs (directory, type) VALUES ('$stor_dir', 'stor')";
            try {
                $con->exec($sql);
            } catch (Exception $e) {
                echo "  * Failed inserting {$stor_dir} in cc_music_dirs".PHP_EOL;
                echo "  * Message {$e->getMessage()}".PHP_EOL;
                return false;
            }

            $con->commit();
            
            //Because we're DELETEing all the rows instead of using TRUNCATE (for speed),
            //we have to reset the sequences so the auto-increment columns (like primary keys)
            //all start at 1 again. This is hacky but it still lets all of this execute fast.
            $sql = "SELECT c.relname FROM pg_class c WHERE c.relkind = 'S'";
            try {
                $rows = $con->query($sql)->fetchAll();
            } catch (Exception $e) {
                $rows = array();
            }
            $con->beginTransaction();
            foreach ($rows as $row) {
                $seqrelname= $row["relname"];
                $sql = "ALTER SEQUENCE ${seqrelname} RESTART WITH 1";
                AirtimeInstall::InstallQuery($sql, false);
            }
            $con->commit();
        }
        else
        {
            //Create all the database tables
            AirtimeInstall::createDatabaseTables($dbuser, $dbpasswd, $dbname, $dbhost);
        }
    }

    public static function setupZendBootstrap()
    {
        $application = new Zend_Application(APPLICATION_ENV, CONFIG_PATH . 'application.ini');
        $application->bootstrap();
        return $application;
    }
}
