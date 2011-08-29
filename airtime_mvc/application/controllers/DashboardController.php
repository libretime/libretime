<?php

class DashboardController extends Zend_Controller_Action
{

    public function init()
    {

    }

    public function indexAction()
    {
        // action body
    }

    public function streamPlayerAction()
    {
        $request = $this->getRequest();
        $baseUrl = $request->getBaseUrl();
        $this->view->headLink()->appendStylesheet($baseUrl.'/js/jplayer/skin/jplayer.blue.monday.css');
        $this->_helper->layout->setLayout('bare');
    }

    public function helpAction()
    {
        // action body
    }

}

