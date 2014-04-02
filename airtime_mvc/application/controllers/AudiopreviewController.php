<?php

use Airtime\CcWebstreamQuery;

class AudiopreviewController extends Zend_Controller_Action
{
    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('media-preview', 'json')
                    ->initContext();
    }
    
    public function mediaPreviewAction()
    {
    	$mediaId = $this->_getParam('id');
    	
    	$mediaService = new Application_Service_MediaService();
    	$jPlayerMaker = $mediaService->getJPlayerPreviewPlaylist($mediaId);
    	
    	$this->view->playlist = $jPlayerMaker->getJPlayerPlaylist();
    }
}
