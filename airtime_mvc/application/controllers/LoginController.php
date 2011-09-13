<?php

class LoginController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        if(Zend_Auth::getInstance()->hasIdentity())
        {
        	$this->_redirect('Nowplaying');
        }
        
        //uses separate layout without a navigation.
        $this->_helper->layout->setLayout('login');
        
        $request = $this->getRequest();
        $baseUrl = $request->getBaseUrl();
        
        $this->view->headScript()->appendFile($baseUrl.'/js/airtime/login/login.js','text/javascript');
        
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
                    $authAdapter = $this->getAuthAdapter();

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
                        
                        $this->_redirect('Nowplaying');
                    }
                    else
                    {
                        $message = "Wrong username or password provided. Please try again.";
                        Application_Model_Subjects::increaseLoginAttempts($username);
                        Application_Model_LoginAttempts::increaseAttempts($_SERVER['REMOTE_ADDR']);
                        $form = new Application_Form_Login();
                    }
                }
            }
        }
        
		$this->view->message = $message;
		$this->view->form = $form;
		$this->view->airtimeVersion = AIRTIME_VERSION;
		$this->view->airtimeCopyright = AIRTIME_COPYRIGHT_DATE;
    }

    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_redirect('login/index');
    }

	 /**
     * Gets the adapter for authentication against a database table
     *
     * @return object
     */
    protected function getAuthAdapter()
    {
        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);

        $authAdapter->setTableName('cc_subjs')
                    ->setIdentityColumn('login')
                    ->setCredentialColumn('pass')
                    ->setCredentialTreatment('MD5(?)');
                    
        return $authAdapter;
    }

}



