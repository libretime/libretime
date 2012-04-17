<?php

class LoginController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        global $CC_CONFIG;
        
        if (Zend_Auth::getInstance()->hasIdentity())
        {
        	$this->_redirect('Showbuilder');
        }
        
        //uses separate layout without a navigation.
        $this->_helper->layout->setLayout('login');
        
        $error = false;
        $request = $this->getRequest();
        $baseUrl = $request->getBaseUrl();
        
        $this->view->headScript()->appendFile($baseUrl.'/js/airtime/login/login.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        
        $form = new Application_Form_Login();
        
        $message = "Please enter your user name and password";

        if($request->isPost())
        {
            // if the post contains recaptcha field, which means form had recaptcha field.
            // Hence add the element for validation.
            if(array_key_exists('recaptcha_response_field', $request->getPost())){
                $form->addRecaptcha();
            }
            if($form->isValid($request->getPost()))
            {
                //get the username and password from the form
                $username = $form->getValue('username');
                $password = $form->getValue('password');
                if(Application_Model_Subjects::getLoginAttempts($username) >= 3 && $form->getElement('captcha') == NULL){
                    $form->addRecaptcha();
                }else{
                    $authAdapter = Application_Model_Auth::getAuthAdapter();

                    //pass to the adapter the submitted username and password
                    $authAdapter->setIdentity($username)
                                ->setCredential($password);
    
                    $auth = Zend_Auth::getInstance();
                    $result = $auth->authenticate($authAdapter);
                    if($result->isValid())
                    {
                        //all info about this user from the login table omit only the password
                        $userInfo = $authAdapter->getResultRowObject(null, 'password');
    
                        //the default storage is a session with namespace Zend_Auth
                        $authStorage = $auth->getStorage();
                        $authStorage->write($userInfo);
                        
                        Application_Model_LoginAttempts::resetAttempts($_SERVER['REMOTE_ADDR']);
                        Application_Model_Subjects::resetLoginAttempts($username);
                        
                        $tempSess = new Zend_Session_Namespace("referrer");
                        $tempSess->referrer = 'login';
                        
                        $this->_redirect('Showbuilder');
                    }
                    else
                    {
                        $message = "Wrong username or password provided. Please try again.";
                        Application_Model_Subjects::increaseLoginAttempts($username);
                        Application_Model_LoginAttempts::increaseAttempts($_SERVER['REMOTE_ADDR']);
                        $form = new Application_Form_Login();
                        $error = true;
                    }
                }
            }
        }
        
        $this->view->message = $message;
        $this->view->error = $error;
        $this->view->form = $form;
        $this->view->airtimeVersion = Application_Model_Preference::GetAirtimeVersion();
        $this->view->airtimeCopyright = AIRTIME_COPYRIGHT_DATE;
        if(isset($CC_CONFIG['demo'])){
            $this->view->demo = $CC_CONFIG['demo'];
        }
    }

    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_redirect('showbuilder/index');
    }
    
    public function passwordRestoreAction()
    {
        //uses separate layout without a navigation.
        $this->_helper->layout->setLayout('login');
         
        $form = new Application_Form_PasswordRestore();
    
        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost())) {
            $user = CcSubjsQuery::create()
                ->filterByDbEmail($form->email->getValue())
                ->findOne();
    
            if (!empty($user)) {
                $auth = new Application_Model_Auth();
    
                $auth->sendPasswordRestoreLink($user, $this->view);
                $this->_helper->redirector('password-restore-after', 'auth');
            }
            else {
                $form->email->addError($this->view->translate("Given email not found."));
            }
        }
        
        $this->view->form = $form;
    }
    
    public function passwordRestoreAfterAction()
    {
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
    
        //check validity of token
        if (!$auth->checkToken($user_id, $token, 'password.restore')) {
            echo "token not valid";
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
    
            $result = $zend_auth->authenticate($authAdapter);
    
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



