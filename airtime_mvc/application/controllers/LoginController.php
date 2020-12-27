<?php

class LoginController extends Zend_Controller_Action
{

    public function init()
    {
        $CC_CONFIG = Config::getConfig();
        $baseUrl = Application_Common_OsPath::getBaseDir();

        $this->view->headLink(array('rel' => 'icon', 'href' => $baseUrl . 'favicon.ico?' . $CC_CONFIG['airtime_version'], 'type' => 'image/x-icon'), 'PREPEND')
            ->appendStylesheet($baseUrl . 'css/bootstrap.css?' . $CC_CONFIG['airtime_version'])
            ->appendStylesheet($baseUrl . 'css/redmond/jquery-ui-1.8.8.custom.css?' . $CC_CONFIG['airtime_version'])
            ->appendStylesheet($baseUrl . 'css/styles.css?' . $CC_CONFIG['airtime_version']);

    }

    public function indexAction()
    {
        $CC_CONFIG = Config::getConfig();
        
        $request = $this->getRequest();
        $response = $this->getResponse();
        $stationLocale = Application_Model_Preference::GetDefaultLocale();
        
        //Enable AJAX requests from www.airtime.pro for the sign-in process.
        CORSHelper::enableCrossOriginRequests($request, $response);

        
        Application_Model_Locale::configureLocalization($request->getcookie('airtime_locale', $stationLocale));

        if (Zend_Session::isStarted()) {

            //Open the session for writing, because we close it for writing by default in Bootstrap.php as an optimization.
            SessionHelper::reopenSessionForWriting();

            $auth = Zend_Auth::getInstance();
            $auth->getStorage();

            if ($auth->hasIdentity()) {
                $this->_redirect('showbuilder');
            }
        }

        //uses separate layout without a navigation.
        $this->_helper->layout->setLayout('login');

        $this->view->error = false;
        
        $baseUrl = Application_Common_OsPath::getBaseDir();

        $form = new Application_Form_Login();

        $message = _("Please enter your username and password.");

        if ($request->isPost()) {

            //Open the session for writing, because we close it for writing by default in Bootstrap.php as an optimization.
            //session_start();


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

                    $this->_redirect('showbuilder');
                } else {
                    $form = $this->loginError($username);
                }
            }
        }

        $this->view->form = $form;
        $this->view->airtimeVersion = $CC_CONFIG['airtime_version'];
        $this->view->airtimeCopyright = AIRTIME_COPYRIGHT_DATE;
    }

    public function logoutAction()
    {
        //Open the session for writing, because we close it for writing by default in Bootstrap.php as an optimization.
        SessionHelper::reopenSessionForWriting();

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

        $this->view->headScript()->appendFile($baseUrl . 'js/airtime/login/password-restore.js?' . $CC_CONFIG['airtime_version'], 'text/javascript');

        $request = $this->getRequest();
        $stationLocale = Application_Model_Preference::GetDefaultLocale();

        Application_Model_Locale::configureLocalization($request->getcookie('airtime_locale', $stationLocale));

        //uses separate layout without a navigation.
        $this->_helper->layout->setLayout('login');

        $form = new Application_Form_PasswordRestore();

        $request = $this->getRequest();
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {
                $query = CcSubjsQuery::create();
                $username = $form->username->getValue();
                $email = $form->email->getValue();

                if (empty($username)) {
                    $query->filterByDbEmail($email);
                } else if (empty($email)) {
                    $query->filterByDbLogin($username);
                } else {
                    $query->filterByDbEmail($email)
                        ->filterByDbLogin($username);
                }
                $user = $query->findOne();

                if (!empty($user)) {
                    $auth = new Application_Model_Auth();

                    $success = $auth->sendPasswordRestoreLink($user, $this->view);
                    if ($success) {
                        $this->_helper->redirector('password-restore-after', 'login');
                    } else {
                        $form->email->addError($this->view->translate(_("Email could not be sent. Check your mail server settings and ensure it has been configured properly.")));
                    }
                } else {
                    $form->email->addError($this->view->translate(_("That username or email address could not be found.")));
                }
            } else { //Form is not valid
                $form->email->addError($this->view->translate(_("There was a problem with the username or email address you entered.")));
            }
        }

        $this->view->form = $form;
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

    /**
     * populates view with results from a login error and adds a new form
     *
     * @param  String $username user that failed to login
     * @return new form
     */
    private function loginError($username)
    {
        $this->view->message = _("Wrong username or password provided. Please try again.");
        Application_Model_Subjects::increaseLoginAttempts($username);
        Application_Model_LoginAttempts::increaseAttempts($_SERVER['REMOTE_ADDR']);
        $form = new Application_Form_Login();                            
        $this->view->error = true;
        return $form;
    }
}
