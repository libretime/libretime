<?php

class UserController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
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

}



