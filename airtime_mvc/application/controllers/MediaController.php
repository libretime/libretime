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
        	->addActionContext('delete', 'json')
            ->initContext();

    }

    public function audioFeedAction()
    {
    	$params = $this->getRequest()->getParams();
    	
    	$datatablesService = new Application_Service_DatatableAudioFileService();
    	$r = $datatablesService->getDatatables($params);
    	
    	$this->view->sEcho = intval($params["sEcho"]);
    	$this->view->iTotalDisplayRecords = $r["count"];
    	$this->view->iTotalRecords = $r["totalCount"];
    	$this->view->media = $r["records"];
    }
    
    public function webstreamFeedAction()
    {
    	$params = $this->getRequest()->getParams();
    	
    	$datatablesService = new Application_Service_DatatableWebstreamService();
    	$r = $datatablesService->getDatatables($params);
    	 
    	$this->view->sEcho = intval($params["sEcho"]);
    	$this->view->iTotalDisplayRecords = $r["count"];
    	$this->view->iTotalRecords = $r["totalCount"];
    	$this->view->media = $r["records"];
    }
    
    public function playlistFeedAction()
    {
    	$params = $this->getRequest()->getParams();
    	 
    	$datatablesService = new Application_Service_DatatablePlaylistService();
    	$r = $datatablesService->getDatatables($params);
    	 
    	$this->view->sEcho = intval($params["sEcho"]);
    	$this->view->iTotalDisplayRecords = $r["count"];
    	$this->view->iTotalRecords = $r["totalCount"];
    	$this->view->media = $r["records"];
    }
    
    public function deleteAction()
    {
    	$ids = $this->_getParam('ids');
    	
    	try {
	    	$mediaService = new Application_Service_MediaService();
	    	$r = $mediaService->delete($ids);
    	}
    	catch (Exception $e) {
    		$this->view->error = $e->getMessage();
    	}
    }
}