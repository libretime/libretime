<?php

declare(strict_types=1);

class TestHelper
{
    public static function loginUser()
    {
        $authAdapter = Application_Model_Auth::getAuthAdapter();

        // pass to the adapter the submitted username and password
        $authAdapter->setIdentity('admin')
            ->setCredential('admin');

        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($authAdapter);
        if ($result->isValid()) {
            // all info about this user from the login table omit only the password
            $userInfo = $authAdapter->getResultRowObject(null, 'password');

            // the default storage is a session with namespace Zend_Auth
            $authStorage = $auth->getStorage();
            $authStorage->write($userInfo);
        }
    }

    public static function getDbZendConfig()
    {
        $config = Config::getConfig();

        return new Zend_Config(
            [
                'host' => $config['dsn']['host'],
                'port' => $config['dsn']['port'],
                'dbname' => $config['dsn']['database'],
                'username' => $config['dsn']['username'],
                'password' => $config['dsn']['password'],
            ]
        );
    }

    public static function installTestDatabase()
    {
        // We need to load the config before our app bootstrap runs. The config
        // is normally
        $CC_CONFIG = Config::getConfig();

        $dbhost = $CC_CONFIG['dsn']['host'];
        $dbport = $CC_CONFIG['dsn']['port'];
        $dbname = $CC_CONFIG['dsn']['database'];
        $dbuser = $CC_CONFIG['dsn']['username'];
        $dbpasswd = $CC_CONFIG['dsn']['password'];

        AirtimeInstall::createDatabase();
        AirtimeInstall::CreateDatabaseTables($dbuser, $dbpasswd, $dbname, $dbhost, $dbport);
    }

    public static function setupZendBootstrap()
    {
        $application = new Zend_Application(APPLICATION_ENV, CONFIG_PATH . '/application.ini');
        $application->bootstrap();

        return $application;
    }
}
