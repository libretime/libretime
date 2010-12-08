<?php

class PlaylistController extends Zend_Controller_Action
{

    protected $pl_sess = null;

    public function init()
    {
		if(!Zend_Auth::getInstance()->hasIdentity())
        {
            $this->_redirect('login/index');
        }

        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('add-item', 'json')
					->addActionContext('delete-item', 'html')
					->addActionContext('set-fade', 'json')
					->addActionContext('set-cue', 'json')
					->addActionContext('move-item', 'html')
                    ->initContext();

        $this->pl_sess = new Zend_Session_Namespace(UI_PLAYLIST_SESSNAME);
    }

    public function indexAction()
    {
    
    }

    public function newAction()
    {
		$pl_sess = $this->pl_sess;
		$userInfo = Zend_Auth::getInstance()->getStorage()->read();

        $pl = new Playlist();
        $pl_id = $pl->create("Test Zend Auth");
		$pl->setPLMetaData('dc:creator', $userInfo->login);
		$pl->lock($userInfo->id);

		//set this playlist as active id.
		$pl_sess->id = $pl_id;

		$this->_helper->redirector('metadata');
    }

    public function metadataAction()
    {
        $pl_sess = $this->pl_sess;
                                                        
        $request = $this->getRequest();
        $form = new Application_Form_PlaylistMetadata();
 
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {  
    
				$formdata = $form->getValues();

				$pl = Playlist::Recall($pl_sess->id);
				$pl->setPLMetaData(UI_MDATA_KEY_TITLE, $formdata["title"]);
				
				if(isset($formdata["description"]))
					$pl->setPLMetaData(UI_MDATA_KEY_DESCRIPTION, $formdata["description"]);

				$this->_helper->redirector('edit');
            }
        }
 
        $this->view->form = $form;
    }

    public function editAction()
    {
        $this->view->headScript()->appendFile('/js/campcaster/playlist/playlist.js','text/javascript');
                                
        $pl_sess = $this->pl_sess;
                        
		if(isset($pl_sess->id)) {

			$pl = Playlist::Recall($pl_sess->id);

			$this->view->playlistcontents = $pl->getContents();
			return;
		}
		
		$this->_helper->redirector('index');       		
    }

    public function addItemAction()
    {
        $pl_sess = $this->pl_sess;        
		$id = $this->_getParam('id');

		if (!is_null($id)) {
			if(isset($pl_sess->id)) {
				$pl = Playlist::Recall($pl_sess->id);
				$res = $pl->addAudioClip($id);

				if (PEAR::isError($res)) {
					die('{"jsonrpc" : "2.0", "error" : {"message": ' + $res->getMessage() + '}}');
				}

				die('{"jsonrpc" : "2.0"}');
			}
			die('{"jsonrpc" : "2.0", "error" : {"message": "no open playlist"}}');
		}

		die('{"jsonrpc" : "2.0", "error" : {"message": "a file is not chosen"}}');
    }

    public function moveItemAction()
    {
        $pl_sess = $this->pl_sess;
                                                        
		if(isset($pl_sess->id)) {

			$oldPos = $this->_getParam('oldPos');
			$newPos = $this->_getParam('newPos');

			$pl = Playlist::Recall($pl_sess->id);

			$pl->moveAudioClip($oldPos, $newPos);

			$this->view->playlistcontents = $pl->getContents();
			return;
		}
		
		$this->_helper->redirector('index');      		
    }

    public function deleteItemAction()
    {
        $pl_sess = $this->pl_sess;
                                                        
		if(isset($pl_sess->id)) {        

			$positions = $this->_getParam('pos', array());

			$pl = Playlist::Recall($pl_sess->id);

			if (!is_array($positions))
		        $positions = array($positions);

		    //so the automatic updating of playlist positioning doesn't affect removal.
		    sort($positions);
		    $positions = array_reverse($positions);

		    foreach ($positions as $pos) {
		    	$pl->delAudioClip($pos);        
		    }

			$this->view->playlistcontents = $pl->getContents();
			return;
		}
		
		$this->_helper->redirector('index');
    }

    public function setCueAction()
    {
        $pl_sess = $this->pl_sess;
                                                        
		if(isset($pl_sess->id)) {

			$pos = $this->_getParam('pos');
			$cueIn = $this->_getParam('cueIn', null);
			$cueOut = $this->_getParam('cueOut', null);

			$pl = Playlist::Recall($pl_sess->id);

			$response = $pl->changeClipLength($pos, $cueIn, $cueOut);

			die(json_encode($response));
		}

		$this->_helper->redirector('index');
    }

    public function setFadeAction()
    {
        $pl_sess = $this->pl_sess;
                                                        
		if(isset($pl_sess->id)) {

			$pos = $this->_getParam('pos');
			$fadeIn = $this->_getParam('fadeIn', null);
			$fadeOut = $this->_getParam('fadeOut', null);

			$pl = Playlist::Recall($pl_sess->id);
			
			$response = $pl->changeFadeInfo($pos, $fadeIn, $fadeOut);

			die(json_encode($response));
		}

		$this->_helper->redirector('index');
    }

    public function deleteAction()
    {
        $id = $this->_getParam('id', null);
        
		if (!is_null($id)) {

			$this->closePlaylist();
			Playlist::Delete($id);
		}
    }

    public function deleteActiveAction()
    {
        $pl_sess = $this->pl_sess;
                                                        
		if(isset($pl_sess->id)) {

			$pl = Playlist::Recall($pl_sess->id);
			$this->closePlaylist($pl);
			
			Playlist::Delete($pl_sess->id);

			unset($pl_sess->id);
		}

		$this->_helper->redirector('index');
    }

    public function closePlaylist($pl)
    {
        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $res = $pl->unlock($userInfo->id);
        return $res;
    }

}





















