<?php

class AuthController extends Zend_Controller_Action
{
    public function init()
    {
    
    }
    
    public function passwordRestoreAction()
    {
    	//uses separate layout without a navigation.
        $this->_helper->layout->setLayout('bare');
    	
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
        $this->_helper->layout->setLayout('bare');
    }
    
    public function passwordChangeAction()
    {
    	//uses separate layout without a navigation.
        $this->_helper->layout->setLayout('bare');
        
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