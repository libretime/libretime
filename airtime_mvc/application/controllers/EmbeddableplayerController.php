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
        $this->view->headLink()->appendStylesheet($baseUrl.'css/embeddableplayer.css?'.$CC_CONFIG['airtime_version']);
        $form = new Application_Form_EmbeddablePlayer();

        if ($form->getElement('player_stream_url')->getAttrib('numberOfEnabledStreams') > 0) {
            $this->view->form = $form;
        } else {
            $this->view->errorMsg = "You need to enable at least one MP3, AAC, or OGG stream to use this feature.";
        }

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