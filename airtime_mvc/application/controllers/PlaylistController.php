<?php

use Airtime\MediaItem\PlaylistQuery;
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
        
	
        $this->mediaService = new Application_Service_MediaService();
        $this->playlistService = new Application_Service_PlaylistService();
    }
    
    private function getPlaylist() {
    	
    	return $this->mediaService->getSessionMediaObject();
    }

    private function createUpdateResponse($playlist)
    {
    	$obj = new Presentation_Playlist($playlist);
    	
        $this->view->length = $obj->getLength();
        $this->view->contents = $obj->getContent();
        $this->view->modified = $obj->getLastModifiedEpoch();
        $this->view->html = $this->view->render('playlist/update.phtml');

        unset($this->view->contents);
    }
    
    private function createFullResponse($obj)
    {
    	if (isset($obj)) {
    		$this->view->obj = new Presentation_Playlist($obj);
    	}
    	
    	$this->view->html = $this->view->render('playlist/playlist.phtml');
    	unset($this->view->obj);
    }

    public function newAction()
    {
    	$type = $this->_getParam('type');
    	
    	$playlist = $this->playlistService->createPlaylist($type);
    	$playlist->save();
    	
    	$this->mediaService->setSessionMediaObject($playlist);
    	$this->createFullResponse($playlist);
    }

    public function editAction()
    {
    	$id = $this->_getParam('id');
    	
    	$playlist = PlaylistQuery::create()->findPK($id);
    	
    	$this->mediaService->setSessionMediaObject($playlist);
    	$this->createFullResponse($playlist);
    }

    public function deleteAction()
    {
    	try {
    		$playlist = $this->getPlaylist();
    		$playlist->delete();
    		$this->mediaService->setSessionMediaObject(null);
    		 
    		$this->createFullResponse(null);
    	}
    	catch (Exception $e) {
    		$this->view->error = $e->getMessage();
    	}
    }
    
    public function clearAction()
    {
    	try {
    		$playlist = $this->getPlaylist(); 
    		$playlist->clear();
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
    		$playlist->shuffle();
    		$this->createUpdateResponse($playlist);
    	}
    	catch (Exception $e) {
    		$this->view->error = $e->getMessage();
    	}
    }
    
    public function addItemsAction()
    {
    	$content = $this->_getParam('content');
    	
    	try {
    		$playlist = $this->getPlaylist();
    		$playlist->savePlaylistContent($content);
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
    		
    		if (isset($info["name"])) {
    			$playlist->setName($info["name"]);
    		}
    		
    		if (isset($info["description"])) {
    			$playlist->setDescription($info["description"]);
    		}
    		
    		$form = new Application_Form_PlaylistRules();
    		$form->buildCriteriaOptions($info["rules"]["criteria"]);
    		
    		if (isset($info["rules"])) {
    			$playlist->setRules($info["rules"]);
    		}
    		
    		$content = isset($info["content"]) ? $info["content"] : array();
    		$playlist->savePlaylistContent($content, true);

    		$this->createUpdateResponse($playlist);
    	}
    	catch (Exception $e) {
    		$this->view->error = $e->getMessage();
    	}
    } 
}
