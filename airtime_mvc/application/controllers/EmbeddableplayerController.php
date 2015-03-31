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
        $this->view->headLink()->appendStylesheet($baseUrl.'css/embeddable-player-form.css?'.$CC_CONFIG['airtime_version']);
        $this->view->headScript()->appendFile($baseUrl.'js/airtime/embeddableplayer/mrp.js?'.$CC_CONFIG['airtime_version']);
        $this->view->headScript()->appendFile($baseUrl.'js/airtime/embeddableplayer/embeddableplayer.js?'.$CC_CONFIG['airtime_version']);

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

        $this->view->css = Application_Common_HTTPHelper::getStationUrl() . "css/embeddable-player.css";
        $this->view->mrp_js = Application_Common_HTTPHelper::getStationUrl() . "js/airtime/embeddableplayer/mrp.js";
        $this->view->jquery = Application_Common_HTTPHelper::getStationUrl() . "js/libs/jquery-1.10.2.js";
        $this->view->muses_swf = Application_Common_HTTPHelper::getStationUrl() . "js/airtime/embeddableplayer/muses.swf";
        $this->view->metadata_api_url = Application_Common_HTTPHelper::getStationUrl() . "api/live-info";
        $this->view->station_name = Application_Model_Preference::GetStationName();
        $stream = $request->getParam('stream');
        $streamData = Application_Model_StreamSetting::getEnabledStreamData();
        $availableMobileStreams = array();
        $availableDesktopStreams = array();

        if ($stream == "auto") {
            $this->view->playerMode = "auto";
            foreach ($streamData as $s) {
                if ($s["mobile"]) {
                    array_push($availableMobileStreams, $s);
                } else if (!$s["mobile"]) {
                    array_push($availableDesktopStreams, $s);
                }
            }
        } else {
            $this->view->playerMode = "manual";
            $selectedStreamData = $streamData[$stream];
            $this->view->streamURL = $selectedStreamData["url"];
            $this->view->codec = $selectedStreamData["codec"];
        }
        $this->view->availableMobileStreams = json_encode($availableMobileStreams);
        $this->view->availableDesktopStreams = json_encode($availableDesktopStreams);
        //$this->view->displayMetadata = $request->getParam('display_metadata');
    }
}