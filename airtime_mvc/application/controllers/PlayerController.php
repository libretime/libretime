<?php

class PlayerController extends Zend_Controller_Action
{
    public function init()
    {

    }
    
    public function customizeAction()
    {
        $CC_CONFIG = Config::getConfig();
        $baseUrl = Application_Common_OsPath::getBaseDir();
        $this->view->headLink()->appendStylesheet($baseUrl.'css/player-form.css?'.$CC_CONFIG['airtime_version']);
        $this->view->headScript()->appendFile($baseUrl.'js/airtime/player/player.js?'.$CC_CONFIG['airtime_version']);

        $form = new Application_Form_Player();

        $apiEnabled = Application_Model_Preference::GetAllow3rdPartyApi();
        $numEnabledStreams = $form->getElement('player_stream_url')->getAttrib('numberOfEnabledStreams');

        if ($numEnabledStreams > 0 && $apiEnabled) {
            $this->view->form = $form;
        } else {
            $this->view->errorMsg = "To configure and use the embeddable player you must:<br><br>
            1. Enable at least one MP3, AAC, or OGG stream under System -> Streams<br>
            2. Enable the Public Airtime API under System -> Preferences";
        }

    }

    /**
     * This is the action that is called to insert the player onto a web page.
     * It passes all the js and css files to the view, as well as all the
     * stream customization information.
     *
     * The view for this action contains all the inline javascript needed to
     * create the player.
     */
    public function indexAction()
    {
        $this->view->layout()->disableLayout();

        $CC_CONFIG = Config::getConfig();


        $request = $this->getRequest();

        $this->view->css = Application_Common_HTTPHelper::getStationUrl() . "css/player.css?".$CC_CONFIG['airtime_version'];
        $this->view->mrp_js = Application_Common_HTTPHelper::getStationUrl() . "js/airtime/player/mrp.js?".$CC_CONFIG['airtime_version'];
        $this->view->jquery = Application_Common_HTTPHelper::getStationUrl() . "js/libs/jquery-1.10.2.js";
        $this->view->muses_swf = Application_Common_HTTPHelper::getStationUrl() . "js/airtime/player/muses.swf";
        $this->view->metadata_api_url = Application_Common_HTTPHelper::getStationUrl() . "api/live-info";
        $this->view->station_name = addslashes(Application_Model_Preference::GetStationName());

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