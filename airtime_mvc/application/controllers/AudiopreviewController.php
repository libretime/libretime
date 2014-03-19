<?php

use Airtime\CcWebstreamQuery;

class AudiopreviewController extends Zend_Controller_Action
{
    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('show-preview', 'json')
                    ->addActionContext('media-preview', 'json')
                    ->initContext();
    }
    
    public function mediaPreviewAction()
    {
    	$mediaId = $this->_getParam('id');
    	
    	$mediaService = new Application_Service_MediaService();
    	$jPlayerMaker = $mediaService->getJPlayerPreviewPlaylist($mediaId);
    	
    	$this->view->playlist = $jPlayerMaker->getJPlayerPlaylist();
    }

    /**
     * Simply sets up the view to play the required show track.
     *  Gets the parameters from the request and sets them to the view.
     */
    public function showPreviewAction()
    {
        $CC_CONFIG = Config::getConfig();

        $showID = $this->_getParam('showID');
        $showIndex = $this->_getParam('showIndex');

        $baseUrl = Application_Common_OsPath::getBaseDir();

        $this->view->headScript()->appendFile($baseUrl.'js/airtime/audiopreview/preview_jplayer.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/jplayer/jplayer.playlist.min.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headLink()->appendStylesheet($baseUrl.'js/jplayer/skin/jplayer.airtime.audio.preview.css?'.$CC_CONFIG['airtime_version']);
        $this->_helper->layout->setLayout('audioPlayer');

        $logo = Application_Model_Preference::GetStationLogo();
        if ($logo) {
            $this->view->logo = "data:image/png;base64,$logo";
        } else {
            $this->view->logo = $baseUrl."css/images/airtime_logo_jp.png";
        }

        $this->view->showID = $showID;
        $this->view->showIndex = $showIndex;

        $this->_helper->viewRenderer->setRender('audio-preview');
    }

    /**
     *Function will load and return the contents of the requested show.
     */
    public function getShowAction()
    {
        $baseUrl = Application_Common_OsPath::getBaseDir();
        // disable the view and the layout
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $showID = $this->_getParam('showID');

        if (!isset($showID)) {
            return;
        }

        $showInstance = new Application_Model_ShowInstance($showID);
        $result = array();
        $position = 0;
        foreach ($showInstance->getShowListContent() as $track) {

            $elementMap = array(
                'element_title' => isset($track['track_title']) ? $track['track_title'] : "",
                'element_artist' => isset($track['creator']) ? $track['creator'] : "",
                'element_position' => $position,
                'element_id' => ++$position,
                'mime' => isset($track['mime'])?$track['mime']:""
            );

            $elementMap['type'] = $track['type'];
            if ($track['type'] == 0) {
                $mime = $track['mime'];
                if (strtolower($mime) === 'audio/mp3') {
                    $elementMap['element_mp3'] = $track['item_id'];
                } elseif (strtolower($mime) === 'audio/ogg') {
                    $elementMap['element_oga'] = $track['item_id'];
                } elseif (strtolower($mime) === 'audio/mp4') {
                    $elementMap['element_m4a'] = $track['item_id'];
                } elseif (strtolower($mime) === 'audio/wav') {
                    $elementMap['element_wav'] = $track['item_id'];
                } elseif (strtolower($mime) === 'audio/x-flac') {
                    $elementMap['element_flac'] = $track['item_id'];
                } else {
                    throw new Exception("Unknown file type: $mime");
                }

                $elementMap['uri'] = $baseUrl."api/get-media/file/".$track['item_id'];
            } else {
                $elementMap['uri'] = $track['filepath'];
            }
            $result[] = $elementMap;
        }

        $this->_helper->json($result);

    }
}
