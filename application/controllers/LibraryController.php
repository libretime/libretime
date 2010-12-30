<?php

class LibraryController extends Zend_Controller_Action
{

    protected $pl_sess = null;
	protected $search_sess = null;

    public function init()
    {
		if(!Zend_Auth::getInstance()->hasIdentity())
        {
            $this->_redirect('login/index');
        }

        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('contents', 'html')
					->addActionContext('plupload', 'html')
					->addActionContext('upload', 'json')
					->addActionContext('delete', 'json')
					->addActionContext('context-menu', 'json')
                    ->initContext();

		$this->pl_sess = new Zend_Session_Namespace(UI_PLAYLIST_SESSNAME);
		$this->search_sess = new Zend_Session_Namespace("search");
    }

    public function indexAction()
    {
		$this->view->headScript()->appendFile('/js/campcaster/onready/library.js','text/javascript');
		$this->view->headScript()->appendFile('/js/contextmenu/jjmenu.js','text/javascript');
		$this->view->headLink()->appendStylesheet('/css/contextmenu.css');
	
		$this->_helper->layout->setLayout('library');

		unset($this->search_sess->md);
		unset($this->search_sess->order);

		$this->_helper->actionStack('contents', 'library');
		$this->_helper->actionStack('index', 'sideplaylist');
    }

    public function contextMenuAction()
    {
		$id = $this->_getParam('id');
		$type = $this->_getParam('type');

		$params = '/format/json/id/#id#/type/#type#';

        $pl_sess = $this->pl_sess;

		if($type === "au") {

			$menu[] = array('action' => array('type' => 'ajax', 'url' => '/Library/delete'.$params, 'callback' => 'window["deleteAudioClip"]'), 
							'title' => 'Delete');
	  
			if(isset($pl_sess->id)) {
				$menu[] = array('action' => array('type' => 'ajax', 'url' => '/Playlist/add-item'.$params, 'callback' => 'window["setSPLContent"]'), 
							'title' => 'Add to Playlist');
			}

		}
		else if($type === "pl") {

			$menu[] = array('action' => array('type' => 'ajax', 'url' => '/Playlist/delete'.$params, 'callback' => 'window["deletePlaylist"]'), 
							'title' => 'Delete');

			if(!isset($pl_sess->id) || $pl_sess->id !== $id) {
				$menu[] = array('action' => 
									array('type' => 'ajax', 
									'url' => '/Playlist/edit/view/spl'.$params, 
									'callback' => 'window["openDiffSPL"]'), 
								'title' => 'Edit');
			}

		}

		//returns format jjmenu is looking for.
		die(json_encode($menu));

    }

    public function deleteAction()
    {
        $id = $this->_getParam('id');
        	
		if (!is_null($id)) {
    		$file = StoredFile::Recall($id);
			
			if (PEAR::isError($file)) {
				$this->view->message = $file->getMessage();
				return;
			}
			else if(is_null($file)) {
				$this->view->message = "file doesn't exist";
				return;
			}	

			$res = $file->delete();
			
			if (PEAR::isError($res)) {
				$this->view->message = $res->getMessage();
				return;
			}
		}
		
		$this->view->id = $id;
    }

    public function contentsAction()
    {
		$this->view->headScript()->appendFile('/js/campcaster/library/library.js','text/javascript');

		$this->_helper->viewRenderer->setResponseSegment('library'); 

        $order["category"] = $this->_getParam('ob', "dc:creator");
		$order["order"] = $this->_getParam('order', "asc");

		$this->search_sess->order = $order;
		$md = isset($this->search_sess->md) ? $this->search_sess->md : array();

		$this->view->files = StoredFile::searchFiles($md, $order);
    }

    public function searchAction()
    {
        // action body
    }


}













