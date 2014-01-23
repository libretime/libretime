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

        AirtimeInstall::createDatabase();
        AirtimeInstall::createDatabaseTables($dbuser, $dbpasswd, $dbname, $dbhost);
        AirtimeInstall::SetDefaultTimezone();
    }
}