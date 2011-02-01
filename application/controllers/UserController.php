<?php

class UserController extends Zend_Controller_Action
{

    public function init()
    {
    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('get-hosts', 'json')
                    ->initContext();
    }

    public function indexAction()
    {

    }

    public function addUserAction()
    {	
        $request = $this->getRequest();
        $form = new Application_Form_AddUser();
 
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {  
    
				$formdata = $form->getValues();
				User::addUser($formdata);
				$form->reset();
            }
        }
 
        $this->view->form = $form;
    }

    public function getHostsAction()
    {
		$search = $this->_getParam('term');

        $this->view->hosts = User::getHosts($search);
    }

}





