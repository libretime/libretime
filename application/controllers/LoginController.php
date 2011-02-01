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
        $form = new Application_Form_Login();

		$message = "Please enter your user name and password";

        if($request->isPost())
        {
            if($form->isValid($request->getPost()))
            {

                $authAdapter = $this->getAuthAdapter();

                //get the username and password from the form
                $username = $form->getValue('username');
                $password = $form->getValue('password');

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

                    $this->_redirect('Nowplaying');
                }
                else
                {
                    $message = "Wrong username or password provided. Please try again.";
                }
            }
        }

		$this->view->message = $message;
		$this->view->form = $form;

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



