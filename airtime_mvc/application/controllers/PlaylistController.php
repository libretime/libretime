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
       
    }

    public function addItemsAction()
    {
    	$ids = $this->_getParam('ids');
    	
    	Logging::info("adding items");
    	Logging::info($ids);
    	
    	try {
    		$mediaService = new Application_Service_MediaService();
    		$playlist = $mediaService->getSessionMediaObject();
    		 
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
        
    }
    
    public function saveAction()
    {
    	$info = $this->_getParam('serialized');
    	
    	Logging::info($info);
    } 
}
