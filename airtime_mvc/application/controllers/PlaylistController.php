<?php

use Airtime\MediaItem\Playlist;

class PlaylistController extends Zend_Controller_Action
{

    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext
        	->addActionContext('add-items', 'json')
            ->addActionContext('new', 'json')
            ->addActionContext('edit', 'json')
            ->addActionContext('delete', 'json')
            ->addActionContext('close-playlist', 'json')
            ->addActionContext('save', 'json')
            ->addActionContext('shuffle', 'json')
            ->addActionContext('clear', 'json')
            ->initContext();

    }
    
    private function getPlaylist() {
    	$mediaService = new Application_Service_MediaService();
    	$playlist = $mediaService->getSessionMediaObject();
    	
    	return $playlist;
    }

    private function createUpdateResponse($obj)
    {
        $formatter = new Format_HHMMSSULength($obj->getLength());
        $this->view->length = $formatter->format();

        $this->view->obj = $obj;
        $this->view->contents = $obj->getContents();
        $this->view->html = $this->view->render('playlist/update.phtml');
        $this->view->name = $obj->getName();
        $this->view->description = $obj->getDescription();
        $this->view->modified = $obj->getUpdatedAt("U");

        unset($this->view->obj);
        unset($this->view->contents);
    }
    
    private function createFullResponse($obj)
    {
    	$formatter = new Format_HHMMSSULength($obj->getLength());
    	$this->view->length = $formatter->format();
    
    	$this->view->obj = $obj;
    	$this->view->html = $this->view->render('playlist/playlist.phtml');
    	
    	unset($this->view->length);
    	unset($this->view->obj);
    }

    public function newAction()
    {
    	$playlist = new Playlist();
    	$playlist->save();
    	
    	$mediaService = new Application_Service_MediaService();
    	$mediaService->setSessionMediaObject($playlist);
    	
    	$this->createFullResponse($playlist);
    }

    public function editAction()
    {
       
    }

    public function deleteAction()
    {
    	try {
    		$playlist = $this->getPlaylist();
    		 
    		$playlistService = new Application_Service_PlaylistService();
    		$playlistService->deletePlaylist($playlist);
    		
    		$mediaService->setSessionMediaObject(null);
    		 
    		$this->view->html = $this->view->render('playlist/none.phtml');
    	}
    	catch (Exception $e) {
    		$this->view->error = $e->getMessage();
    	}
    }

    public function addItemsAction()
    {
    	$ids = $this->_getParam('ids');
    	
    	Logging::info("adding items");
    	Logging::info($ids);
    	
    	try {
    		$playlist = $this->getPlaylist();
    		 
    		$playlistService = new Application_Service_PlaylistService();
    		$playlistService->addMedia($playlist, $ids, true);
    		
    		$this->createUpdateResponse($playlist);
    	}
    	catch (Exception $e) {
    		$this->view->error = $e->getMessage();
    	}
    }
    
    public function clearAction()
    {
    	try {
    		$playlist = $this->getPlaylist();
    		 
    		$playlistService = new Application_Service_PlaylistService();
    		$playlistService->clearPlaylist($playlist);
    		 
    		$this->createUpdateResponse($playlist);
    	}
    	catch (Exception $e) {
    		$this->view->error = $e->getMessage();
    	}
    }
    
    public function shuffleAction()
    {
    	try {
    		$playlist = $this->getPlaylist();
    		 
    		$playlistService = new Application_Service_PlaylistService();
    		$playlistService->shufflePlaylist($playlist);
    		 
    		$this->createUpdateResponse($playlist);
    	}
    	catch (Exception $e) {
    		$this->view->error = $e->getMessage();
    	}
    }
    
    public function saveAction()
    {
    	$info = $this->_getParam('serialized');
    	
    	Logging::info($info);
    	
    	try {
    		$playlist = $this->getPlaylist();
    		 
    		$playlistService = new Application_Service_PlaylistService();
    		$playlistService->savePlaylist($playlist, $info);
    	
    		$this->createUpdateResponse($playlist);
    	}
    	catch (Exception $e) {
    		$this->view->error = $e->getMessage();
    	}
    } 
}
