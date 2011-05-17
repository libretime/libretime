<?php

class UserController extends Zend_Controller_Action
{

    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('get-hosts', 'json')
                    ->addActionContext('get-user-data-table-info', 'json')
                    ->addActionContext('get-user-data', 'json')
                    ->addActionContext('remove-user', 'json')
                    ->initContext();
    }

    public function indexAction()
    {
    }

    public function addUserAction()
    {
        $request = $this->getRequest();
        $baseUrl = $request->getBaseUrl();
        $this->view->headScript()->appendFile($baseUrl.'/js/datatables/js/jquery.dataTables.js','text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/datatables/plugin/dataTables.pluginAPI.js','text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/airtime/user/user.js','text/javascript');    

        $form = new Application_Form_AddUser();

        $this->view->successMessage = "";
 
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {  
    
				$formdata = $form->getValues();
                if ($form->validateLogin($formdata)){                                                                       
                    $user = new User($formdata['user_id']);
                    $user->setFirstName($formdata['first_name']);
                    $user->setLastName($formdata['last_name']);
                    $user->setLogin($formdata['login']);
                    if ($formdata['password'] != "xxxxxx")
                        $user->setPassword($formdata['password']);
                    $user->setType($formdata['type']);
                    $user->setEmail($formdata['email']);
                    $user->setSkype($formdata['skype']);
                    $user->setJabber($formdata['jabber']);
                    $user->save();
                    
                    $form->reset();

                    if (strlen($formdata['user_id']) == 0){
                        $this->view->successMessage = "<div class='success'>User added successfully!</div>";
                    } else {
                        $this->view->successMessage = "<div class='success'>User updated successfully!</div>";
                    }
                }
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

    public function getUserDataAction()
    {
        $id = $this->_getParam('id');
        $this->view->entries = User::GetUserData($id);
    }

    public function removeUserAction()
    {
        // action body
        $delId = $this->_getParam('id');

        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $userId = $userInfo->id;

        if ($delId != $userId){
            $user = new User($delId);
            $this->view->entries = $user->delete();
        }
            
    }


}











