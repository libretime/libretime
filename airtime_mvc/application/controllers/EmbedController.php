<?php

class EmbedController extends Zend_Controller_Action
{
    public function init()
    {

    }

    /**
     * This is the action that is called to insert the player onto a web page.
     * It passes all the js and css files to the view, as well as all the
     * stream customization information.
     *
     * The view for this action contains all the inline javascript needed to
     * create the player.
     */
    public function playerAction()
    {
        $this->view->layout()->disableLayout();

        $CC_CONFIG = Config::getConfig();

        $request = $this->getRequest();

        $this->view->css = Application_Common_HTTPHelper::getStationUrl() . "css/player.css?".$CC_CONFIG['airtime_version'];
        $this->view->mrp_js = Application_Common_HTTPHelper::getStationUrl() . "js/airtime/player/mrp.js?".$CC_CONFIG['airtime_version'];
        $this->view->jquery = Application_Common_HTTPHelper::getStationUrl() . "js/libs/jquery-1.10.2.js";
        $this->view->muses_swf = Application_Common_HTTPHelper::getStationUrl() . "js/airtime/player/muses.swf";
        $this->view->metadata_api_url = Application_Common_HTTPHelper::getStationUrl() . "api/live-info";
        $this->view->player_title = json_encode($request->getParam('title'));

        $stream = $request->getParam('stream');
        $streamData = Application_Model_StreamSetting::getEnabledStreamData();
        $availableMobileStreams = array();
        $availableDesktopStreams = array();

        if ($stream == "auto") {
            $this->view->playerMode = "auto";
            $this->view->streamURL = json_encode("");
            foreach ($streamData as $s) {
                if ($s["mobile"]) {
                    array_push($availableMobileStreams, $s);
                } else if (!$s["mobile"]) {
                    array_push($availableDesktopStreams, $s);
                }
            }
        } elseif (!empty($stream)) {
            $this->view->playerMode = "manual";
            $selectedStreamData = $streamData[$stream];
            $this->view->streamURL = json_encode($selectedStreamData["url"]);
            $this->view->codec = $selectedStreamData["codec"];
        }
        $this->view->availableMobileStreams = json_encode($availableMobileStreams);
        $this->view->availableDesktopStreams = json_encode($availableDesktopStreams);
    }
}
