<?php

class EmbeddablePlayerController extends Zend_Controller_Action
{
    public function init()
    {

    }
    
    public function indexAction()
    {
        $CC_CONFIG = Config::getConfig();
        $baseUrl = Application_Common_OsPath::getBaseDir();
        $this->view->headLink()->appendStylesheet($baseUrl.'css/embed-player.css?'.$CC_CONFIG['airtime_version']);

        $form = new Application_Form_EmbeddablePlayer();

        $this->view->form = $form;
    }
}