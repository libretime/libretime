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

        $this->view->headScript()->appendFile('/js/playlist/helperfunctions.js','text/javascript');
		$this->view->headScript()->appendFile('/js/playlist/playlist.js','text/javascript');
        
		$this->view->headLink()->appendStylesheet('/css/pro_dropdown_3.css');
		$this->view->headLink()->appendStylesheet('/css/styles.css');
		
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





