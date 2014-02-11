<?php

class MediaController extends Zend_Controller_Action
{

    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext
        	->addActionContext('audio-feed', 'json')
        	->addActionContext('webstream-feed', 'json')
        	->addActionContext('playlist-feed', 'json')
            ->initContext();

    }

    public function audioFeedAction()
    {
    	$params = $this->getRequest()->getParams();
    	
    	$mediaService = new Application_Service_MediaService();
    	$r = $mediaService->getDatatablesAudioFiles($params);
    	
    	$this->view->sEcho = intval($params["sEcho"]);
    	$this->view->iTotalDisplayRecords = $r["count"];
    	$this->view->iTotalRecords = $r["totalCount"];
    	$this->view->media = $r["records"];
    }
    
    public function webstreamFeedAction()
    {
    	$params = $this->getRequest()->getParams();
    	
    	$mediaService = new Application_Service_MediaService();
    	$r = $mediaService->getDatatablesWebstreams($params);
    	 
    	$this->view->sEcho = intval($params["sEcho"]);
    	$this->view->iTotalDisplayRecords = $r["count"];
    	$this->view->iTotalRecords = $r["totalCount"];
    	$this->view->media = $r["records"];
    }
    
    public function playlistFeedAction()
    {
    	$params = $this->getRequest()->getParams();
    	 
    	$mediaService = new Application_Service_MediaService();
    	$r = $mediaService->getDatatablesPlaylists($params);
    	 
    	$this->view->sEcho = intval($params["sEcho"]);
    	$this->view->iTotalDisplayRecords = $r["count"];
    	$this->view->iTotalRecords = $r["totalCount"];
    	$this->view->media = $r["records"];
    }
}