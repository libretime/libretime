<?php

class WebstreamController extends Zend_Controller_Action
{
    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('new', 'json')
                    ->addActionContext('save', 'json')
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

    public function saveAction()
    {
        $request = $this->getRequest();

        $analysis = Application_Model_Webstream::analyzeFormData($request);
        
        if (Application_Model_Webstream::isValid($analysis)) {
            Application_Model_Webstream::save($request);
            $this->view->statusMessage = "<div class='success'>Webstream saved.</div>";
        } else {
            $this->view->statusMessage = "<div class='errors'>Invalid form values.</div>"; 
            $this->view->analysis = $analysis;
        }
    }
}
