<?php

class EmbeddablePlayerController extends Zend_Controller_Action
{
    public function init()
    {

    }
    
    public function indexAction()
    {
        $form = new Application_Form_EmbeddablePlayer();

        $this->view->form = $form;
    }

    public function embedCodeAction()
    {
        $this->view->layout()->disableLayout();

        $request = $this->getRequest();

        $this->view->mrp_js = Application_Common_HTTPHelper::getStationUrl() . "/js/airtime/embeddableplayer/mrp.js";
        $this->view->muses_swf = Application_Common_HTTPHelper::getStationUrl() . "/js/airtime/embeddableplayer/muses.swf";
        $this->view->skin = Application_Common_HTTPHelper::getStationUrl() . "/js/airtime/embeddableplayer/ffmp3-mcclean.xml";
        $this->view->codec = $request->getParam('codec');
        $this->view->streamURL = $request->getParam('url');
        $this->view->displayMetadata = $request->getParam('display_metadata');
    }
}