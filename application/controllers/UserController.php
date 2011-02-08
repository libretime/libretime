<?php

class UserController extends Zend_Controller_Action
{

    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
                $ajaxContext->addActionContext('get-hosts', 'json')
                            ->addActionContext('get-user-data-table-info', 'json')
                            ->initContext();
    }

    public function indexAction()
    {
    }

    public function addUserAction()
    {
        $this->view->headScript()->appendFile('/js/datatables/js/jquery.dataTables.js','text/javascript');
        $this->view->headScript()->appendFile('/js/airtime/user/user.js','text/javascript');
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

    public function getUserDataTableInfoAction()
    {
        $post = $this->getRequest()->getPost();
        $users = User::getUsersDataTablesInfo($post);
        
        die(json_encode($users));
    }
}







