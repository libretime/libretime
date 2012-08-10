<?php

class WebstreamController extends Zend_Controller_Action
{
    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('new', 'json')
                    ->addActionContext('save', 'json')
                    ->addActionContext('edit', 'json')
                    ->initContext();
        //TODO
        //$this->pl_sess = new Zend_Session_Namespace(UI_PLAYLIST_SESSNAME);
    }

    public function newAction()
    {
        $this->view->ws = new Application_Model_Webstream();
        $this->view->html = $this->view->render('webstream/webstream.phtml');
        /*
        $pl_sess = $this->pl_sess;
        $userInfo = Zend_Auth::getInstance()->getStorage()->read();

        $pl = new Application_Model_Playlist();
        $pl->setName("Untitled Playlist");
        $pl->setPLMetaData('dc:creator', $userInfo->id);

        $this->changePlaylist($pl->getId());
        $this->createFullResponse($pl);
        */
    }

    public function editAction()
    {
        $request = $this->getRequest();

        $id = $request->getParam("id");
        if (is_null($id)) {
            throw new Exception("Missing parameter 'id'"); 
        }

        $this->view->ws = new Application_Model_Webstream($id);
        $this->view->html = $this->view->render('webstream/webstream.phtml');
    }

    public function saveAction()
    {
        $request = $this->getRequest();

        
        $user = Application_Model_User::getCurrentUser();
        $hasPermission = $user->isUserType(array(UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER, UTYPE_HOST));
        $id = $request->getParam("id");

        if ($id != -1) {
            $webstream = CcWebstreamQuery::create()->findPK($id);
            //we are updating a playlist. Ensure that if the user is a host/dj, that he has the correct permission. 
            $user = Application_Model_User::getCurrentUser();
            if ($webstream->getDbCreatorId() != $user->getId()) {
                header("Status: 401 Not Authorized");
                return;
            } 
        } 

        if (!$hasPermission) {
            header("Status: 401 Not Authorized");
            return;
        }

        $analysis = Application_Model_Webstream::analyzeFormData($request);
        try { 
            if (Application_Model_Webstream::isValid($analysis)) {
                Application_Model_Webstream::save($request, $id);
                $this->view->statusMessage = "<div class='success'>Webstream saved.</div>";
            } else {
                throw new Exception();
            }
        } catch (Exception $e) {
            $this->view->statusMessage = "<div class='errors'>Invalid form values.</div>"; 
            $this->view->analysis = $analysis;
        }
    }
}
