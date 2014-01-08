<?php
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

            //Application_Model_LoginAttempts::resetAttempts($_SERVER['REMOTE_ADDR']);
            //Application_Model_Subjects::resetLoginAttempts($username);

            //$tempSess = new Zend_Session_Namespace("referrer");
            //$tempSess->referrer = 'login';
        }
    }
}