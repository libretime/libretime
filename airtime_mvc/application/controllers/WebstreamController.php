<?php

class WebstreamController extends Zend_Controller_Action
{
    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('new', 'json')
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
} 
