<?php

require_once('WhmcsLoginController.php');
require_once('CORSHelper.php');

class LoginController extends Zend_Controller_Action
{

    public function init()
    {
        //Open the session for writing, because we close it for writing by default in Bootstrap.php as an optimization.
        session_start();
    }

    public function indexAction()
    {
        $CC_CONFIG = Config::getConfig();
        
        $request = $this->getRequest();
        $response = $this->getResponse();
        $stationLocale = Application_Model_Preference::GetDefaultLocale();
        
        //Enable AJAX requests from www.airtime.pro for the sign-in process.
        CORSHelper::enableATProCrossOriginRequests($request, $response);
                
        
        Application_Model_Locale::configureLocalization($request->getcookie('airtime_locale', $stationLocale));
        $auth = Zend_Auth::getInstance();
        
        if ($auth->hasIdentity()) {
            $this->_redirect('Showbuilder');
        }

        //uses separate layout without a navigation.
        $this->_helper->layout->setLayout('login');

        $error = false;
        
        $baseUrl = Application_Common_OsPath::getBaseDir();

        $this->view->headScript()->appendFile($baseUrl.'js/airtime/login/login.js?'.$CC_CONFIG['airtime_version'],'text/javascript');

        $form = new Application_Form_Login();

        $message = _("Please enter your user name and password");

        if ($request->isPost()) {
            // if the post contains recaptcha field, which means form had recaptcha field.
            // Hence add the element for validation.
            if (array_key_exists('recaptcha_response_field', $request->getPost())) {
                $form->addRecaptcha();
            }
            if ($form->isValid($request->getPost())) {
                //get the username and password from the form
                $username = $form->getValue('username');
                $password = $form->getValue('password');
                $locale = $form->getValue('locale');

                $authAdapter = Application_Model_Auth::getAuthAdapter();

                //pass to the adapter the submitted username and password
                $authAdapter->setIdentity($username)
                            ->setCredential($password);
                
                $result = $auth->authenticate($authAdapter);
                if ($result->isValid()) {
                    Zend_Session::regenerateId();
                    //all info about this user from the login table omit only the password
                    $userInfo = $authAdapter->getResultRowObject(null, 'password');

                    //the default storage is a session with namespace Zend_Auth
                    $authStorage = $auth->getStorage();
                    $authStorage->write($userInfo);

                    Application_Model_LoginAttempts::resetAttempts($_SERVER['REMOTE_ADDR']);
                    Application_Model_Subjects::resetLoginAttempts($username);

                    //set the user locale in case user changed it in when logging in
                    Application_Model_Preference::SetUserLocale($locale);

                    $this->_redirect('Showbuilder');
                } else {
                    $email = $form->getValue('username');
                    $authAdapter = new WHMCS_Auth_Adapter("admin", $email, $password);
                    $auth = Zend_Auth::getInstance();
                    $result = $auth->authenticate($authAdapter);
                    if ($result->isValid()) {
                        Zend_Session::regenerateId();
                        //set the user locale in case user changed it in when logging in
                        Application_Model_Preference::SetUserLocale($locale);
                        
                        $this->_redirect('Showbuilder');
                    }
                    else {
                        $message = _("Wrong username or password provided. Please try again.");
                        Application_Model_Subjects::increaseLoginAttempts($username);
                        Application_Model_LoginAttempts::increaseAttempts($_SERVER['REMOTE_ADDR']);
                        $form = new Application_Form_Login();                            
                        $error = true;
                        //Only show the captcha if you get your login wrong 4 times in a row.
                        if (Application_Model_Subjects::getLoginAttempts($username) > 3)
                        {
                            $form->addRecaptcha();
                        }
                    }
                }
            }
        }

        $this->view->message = $message;
        $this->view->error = $error;
        $this->view->form = $form;
        $this->view->airtimeVersion = Application_Model_Preference::GetAirtimeVersion();
        $this->view->airtimeCopyright = AIRTIME_COPYRIGHT_DATE;
        if (isset($CC_CONFIG['demo'])) {
            $this->view->demo = $CC_CONFIG['demo'];
        }
    }

    public function logoutAction()
    {
        $auth = Zend_Auth::getInstance();
        $auth->clearIdentity();
        // Unset all session variables relating to CSRF prevention on logout
        $csrf_namespace = new Zend_Session_Namespace('csrf_namespace');
        $csrf_namespace->unsetAll();
        $this->_redirect('showbuilder/index');
    }

    public function passwordRestoreAction()
    {
        $CC_CONFIG = Config::getConfig();

        $baseUrl = Application_Common_OsPath::getBaseDir();
        
        $this->view->headScript()->appendFile($baseUrl.'js/airtime/login/password-restore.js?'.$CC_CONFIG['airtime_version'],'text/javascript');

        $request = $this->getRequest();
        $stationLocale = Application_Model_Preference::GetDefaultLocale();
        
        Application_Model_Locale::configureLocalization($request->getcookie('airtime_locale', $stationLocale));

        if (!Application_Model_Preference::GetEnableSystemEmail()) {
            $this->_redirect('login');
        } else {
            //uses separate layout without a navigation.
            $this->_helper->layout->setLayout('login');

            $form = new Application_Form_PasswordRestore();

            $request = $this->getRequest();
            if ($request->isPost() && $form->isValid($request->getPost())) {
                if (is_null($form->username->getValue()) || $form->username->getValue() == '') {
                    $user = CcSubjsQuery::create()
                        ->filterByDbEmail($form->email->getValue())
                        ->findOne();
                } else {
                    $user = CcSubjsQuery::create()
                        ->filterByDbEmail($form->email->getValue())
                        ->filterByDbLogin($form->username->getValue())
                        ->findOne();
                }

                if (!empty($user)) {
                    $auth = new Application_Model_Auth();

                    $success = $auth->sendPasswordRestoreLink($user, $this->view);
                    if ($success) {
                        $this->_helper->redirector('password-restore-after', 'login');
                    } else {
                        $form->email->addError($this->view->translate(_("Email could not be sent. Check your mail server settings and ensure it has been configured properly.")));
                    }
                } else {
                    $form->email->addError($this->view->translate(_("Given email not found.")));
                }
            }

            $this->view->form = $form;
        }
    }

    public function passwordRestoreAfterAction()
    {
        $request = $this->getRequest();
        $stationLocale = Application_Model_Preference::GetDefaultLocale();
        
        Application_Model_Locale::configureLocalization($request->getcookie('airtime_locale', $stationLocale));

        //uses separate layout without a navigation.
        $this->_helper->layout->setLayout('login');
    }

    public function passwordChangeAction()
    {
        //uses separate layout without a navigation.
        $this->_helper->layout->setLayout('login');

        $request = $this->getRequest();
        $token = $request->getParam("token", false);
        $user_id = $request->getParam("user_id", 0);

        $form = new Application_Form_PasswordChange();
        $auth = new Application_Model_Auth();
        $user = CcSubjsQuery::create()->findPK($user_id);
        
        $stationLocale = Application_Model_Preference::GetDefaultLocale();

        Application_Model_Locale::configureLocalization($request->getcookie('airtime_locale', $stationLocale));

        //check validity of token
        if (!$auth->checkToken($user_id, $token, 'password.restore')) {
            Logging::debug("token not valid");
            $this->_helper->redirector('index', 'login');
        }

        if ($request->isPost() && $form->isValid($request->getPost())) {

            $user->setDbPass(md5($form->password->getValue()));
            $user->save();

            $auth->invalidateTokens($user, 'password.restore');

            $zend_auth = Zend_Auth::getInstance();
            $zend_auth->clearIdentity();

            $authAdapter = Application_Model_Auth::getAuthAdapter();
            $authAdapter->setIdentity($user->getDbLogin())
            ->setCredential($form->password->getValue());

            $zend_auth->authenticate($authAdapter);

            //all info about this user from the login table omit only the password
            $userInfo = $authAdapter->getResultRowObject(null, 'password');

            //the default storage is a session with namespace Zend_Auth
            $authStorage = $zend_auth->getStorage();
            $authStorage->write($userInfo);

            $this->_helper->redirector('index', 'showbuilder');
        }

        $this->view->form = $form;
    }
}
