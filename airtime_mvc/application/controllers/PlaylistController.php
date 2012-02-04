<?php

class PlaylistController extends Zend_Controller_Action
{
    protected $pl_sess = null;

    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('add-items', 'json')
                    ->addActionContext('move-items', 'json')
                    ->addActionContext('delete-items', 'json')
                    ->addActionContext('set-fade', 'json')
                    ->addActionContext('set-cue', 'json')
                    ->addActionContext('new', 'json')
                    ->addActionContext('edit', 'json')
                    ->addActionContext('delete', 'json')
                    ->addActionContext('set-playlist-fades', 'json')
                    ->addActionContext('set-playlist-name', 'json')
                    ->addActionContext('set-playlist-description', 'json')
                    ->initContext();

        $this->pl_sess = new Zend_Session_Namespace(UI_PLAYLIST_SESSNAME);
    }

    private function getPlaylist()
    {
        $pl = null;

    	if (isset($this->pl_sess->id)) {
            $pl = new Application_Model_Playlist($this->pl_sess->id);
        }
        return $pl;
    }

    private function changePlaylist($pl_id)
    {
		$this->pl_sess->id = intval($pl_id);
    }

    private function createUpdateResponse($pl)
    {
        $this->view->pl = $pl;
        $this->view->html = $this->view->render('playlist/update.phtml');
        $this->view->name = $pl->getName();
        $this->view->length = $pl->getLength();
        $this->view->description = $pl->getDescription();

        unset($this->view->pl);
    }

    private function createFullResponse($pl = null)
    {
        if (isset($pl)) {
            $this->view->pl = $pl;
            $this->view->id = $pl->getId();
            $this->view->html = $this->view->render('playlist/index.phtml');
            unset($this->view->pl);
        }
        else {
            $this->view->html = $this->view->render('playlist/index.phtml');
        }
    }

    public function indexAction()
    {
        $request = $this->getRequest();
        $baseUrl = $request->getBaseUrl();

        $this->view->headScript()->appendFile($baseUrl.'/js/airtime/library/spl.js','text/javascript');
		$this->view->headLink()->appendStylesheet($baseUrl.'/css/playlist_builder.css');

		$this->_helper->viewRenderer->setResponseSegment('spl');

        try {
            $pl = $this->getPlaylist();

            if (isset($pl)) {
              $this->view->pl = $pl;
            }
        }
        catch (PlaylistNotFoundException $e) {
            Logging::log("Playlist not found");
            $this->changePlaylist(null);
        }
        catch (Exception $e) {
            Logging::log("{$e->getMessage()}");
        }
    }

    public function newAction()
    {
        $pl_sess = $this->pl_sess;
		$userInfo = Zend_Auth::getInstance()->getStorage()->read();

        $pl = new Application_Model_Playlist();
        $pl->setName("Untitled Playlist");
		$pl->setPLMetaData('dc:creator', $userInfo->id);

		$this->changePlaylist($pl->getId());
		$this->createFullResponse($pl);
    }

    public function editAction()
    {
        $pl_id = $this->_getParam('id', null);

		if (!is_null($pl_id)) {
			$this->changePlaylist($pl_id);
		}

		try {
            $pl = $this->getPlaylist();
		}
		catch (PlaylistNotFoundException $e) {
		    Logging::log("Playlist {$pl_id} not found");
            $this->changePlaylist(null);
		}
		catch (Exception $e) {
		    Logging::log("{$e->getFile()}");
            Logging::log("{$e->getLine()}");
            Logging::log("{$e->getMessage()}");
		    $this->changePlaylist(null);
		}

		$this->createFullResponse($pl);
    }

    public function deleteAction()
    {
        $ids = $this->_getParam('ids');
        $ids = (!is_array($ids)) ? array($ids) : $ids;
        $pl = null;

        try {

            Logging::log("Currently active playlist {$this->pl_sess->id}");
            if (in_array($this->pl_sess->id, $ids)) {
                Logging::log("Deleting currently active playlist");
                $this->changePlaylist(null);
            }
            else {
                $pl = $this->getPlaylist();
                Logging::log("Not deleting currently active playlist");
            }

            Application_Model_Playlist::DeletePlaylists($ids);
        }
        catch(PlaylistNotFoundException $e) {
            Logging::log("Playlist not found");
            $this->changePlaylist(null);
            $pl = null;
        }
        catch(Exception $e) {
            Logging::log("{$e->getFile()}");
            Logging::log("{$e->getLine()}");
            Logging::log("{$e->getMessage()}");
        }

        $this->createFullResponse($pl);
    }

    public function addItemsAction()
    {
        $ids = $this->_getParam('ids');
        $ids = (!is_array($ids)) ? array($ids) : $ids;
    	$afterItem = $this->_getParam('afterItem', null);
    	$addType = $this->_getParam('type', 'after');

    	Logging::log("type is ".$addType);

        try {
            $pl = $this->getPlaylist();
            $pl->addAudioClips($ids, $afterItem, $addType);
        }
        catch (PlaylistNotFoundException $e) {
            Logging::log("Playlist not found");
            $this->changePlaylist(null);
            $this->createFullResponse(null);
        }
        catch (Exception $e) {
            Logging::log("{$e->getFile()}");
            Logging::log("{$e->getLine()}");
            Logging::log("{$e->getMessage()}");
        }

	    $this->createUpdateResponse($pl);
    }

    public function moveItemsAction()
    {
        $ids = $this->_getParam('ids');
        $ids = (!is_array($ids)) ? array($ids) : $ids;
        $afterItem = $this->_getParam('afterItem', null);

        try {
            $pl = $this->getPlaylist();
            $pl->moveAudioClips($ids, $afterItem);
        }
        catch (PlaylistNotFoundException $e) {
            Logging::log("Playlist not found");
            $this->changePlaylist(null);
            $this->createFullResponse(null);
        }
        catch (Exception $e) {
            Logging::log("{$e->getFile()}");
            Logging::log("{$e->getLine()}");
            Logging::log("{$e->getMessage()}");
        }

		$this->createUpdateResponse($pl);
    }

    public function deleteItemsAction()
    {
        $ids = $this->_getParam('ids');
        $ids = (!is_array($ids)) ? array($ids) : $ids;

        try {
            $pl = $this->getPlaylist();
            $pl->delAudioClips($ids);
        }
        catch (PlaylistNotFoundException $e) {
            Logging::log("Playlist not found");
            $this->changePlaylist(null);
            $this->createFullResponse(null);
        }
        catch (Exception $e) {
            Logging::log("{$e->getFile()}");
            Logging::log("{$e->getLine()}");
            Logging::log("{$e->getMessage()}");
        }

		$this->createUpdateResponse($pl);
    }

    public function setCueAction()
    {
		$id = $this->_getParam('id');
		$cueIn = $this->_getParam('cueIn', null);
		$cueOut = $this->_getParam('cueOut', null);

        try {
            $pl = $this->getPlaylist();
            $response = $pl->changeClipLength($id, $cueIn, $cueOut);

            $this->view->response = $response;

            if(!isset($response["error"])) {
                $this->createUpdateResponse($pl);
            }
        }
        catch (PlaylistNotFoundException $e) {
            Logging::log("Playlist not found");
            $this->changePlaylist(null);
            $this->createFullResponse(null);
        }
        catch (Exception $e) {
            Logging::log("{$e->getFile()}");
            Logging::log("{$e->getLine()}");
            Logging::log("{$e->getMessage()}");
        }
    }

    public function setFadeAction()
    {
		$pos = $this->_getParam('pos');
		$pl = $this->getPlaylist();
        if($pl === false){
            $this->view->playlist_error = true;
            return false;
        }

		$fadeIn = $this->_getParam('fadeIn', null);
		$fadeOut = $this->_getParam('fadeOut', null);

		$response = $pl->changeFadeInfo($pos, $fadeIn, $fadeOut);

		$this->view->response = $response;

        if(!isset($response["error"])) {
            $this->createUpdateResponse($pl);
        }
    }

    public function setPlaylistFadesAction()
    {
        $request = $this->getRequest();
		$pl = $this->getPlaylist();
        if($pl === false){
            $this->view->playlist_error = true;
            return false;
        }

		if($request->isPost()) {
			$fadeIn = $this->_getParam('fadeIn', null);
			$fadeOut = $this->_getParam('fadeOut', null);

            if($fadeIn)
			    $response = $pl->changeFadeInfo(0, $fadeIn, $fadeOut);
            else if($fadeOut)
                 $response = $pl->changeFadeInfo($pl->getSize(), $fadeIn, $fadeOut);

			$this->view->response = $response;
			return;
		}

		$fades = $pl->getFadeInfo(0);
		$this->view->fadeIn = $fades[0];

		$fades = $pl->getFadeInfo($pl->getSize());
		$this->view->fadeOut = $fades[1];
    }

    public function setPlaylistNameAction()
    {
        $name = $this->_getParam('name', 'Unknown Playlist');

        $pl = $this->getPlaylist();
        if($pl === false){
            $this->view->playlist_error = true;
            return false;
        }
        $pl->setName($name);

        $this->view->playlistName = $name;
    }

    public function setPlaylistDescriptionAction()
    {
        $description = $this->_getParam('description', false);
        $pl = $this->getPlaylist();
        if($pl === false){
            $this->view->playlist_error = true;
            return false;
        }

        if($description != false) {
            $pl->setDescription($description);
        }
        else {
            $description = $pl->getDescription();
        }

        $this->view->playlistDescription = $description;
    }
}

