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
					->addActionContext('contents', 'json')
					->addActionContext('plupload', 'html')
					->addActionContext('upload', 'json')
					->addActionContext('delete', 'json')
					->addActionContext('context-menu', 'json')
					->addActionContext('quick-search', 'json')
                    ->initContext();

		$this->pl_sess = new Zend_Session_Namespace(UI_PLAYLIST_SESSNAME);
		$this->search_sess = new Zend_Session_Namespace("search");
    }

    public function indexAction()
    {
        $this->view->headScript()->appendFile('/js/airtime/onready/library.js','text/javascript');
		$this->view->headScript()->appendFile('/js/contextmenu/jjmenu.js','text/javascript');
        $this->view->headScript()->appendFile('/js/playlist/helperfunctions.js','text/javascript');
		$this->view->headScript()->appendFile('/js/playlist/playlist.js','text/javascript');
        $this->view->headScript()->appendFile('/js/jplayer/jquery.jplayer.min.js');
        
		$this->view->headLink()->appendStylesheet('/css/contextmenu.css');
		$this->view->headLink()->appendStylesheet('/css/pro_dropdown_3.css');
		$this->view->headLink()->appendStylesheet('/css/styles.css');
	
		$this->_helper->layout->setLayout('library');

		unset($this->search_sess->page);
		unset($this->search_sess->md);
		
		$this->_helper->actionStack('index', 'playlist');
		$this->_helper->actionStack('contents', 'library');
		//$this->_helper->actionStack('quick-search', 'library');
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

			$menu[] = array('action' => array('type' => 'gourl', 'url' => '/Library/edit-file-md/id/#id#'), 
							'title' => 'Edit Metadata');

		}
		else if($type === "pl") {

			if(!isset($pl_sess->id) || $pl_sess->id !== $id) {
				$menu[] = array('action' => 
									array('type' => 'ajax', 
									'url' => '/Playlist/edit'.$params, 
									'callback' => 'window["openDiffSPL"]'), 
								'title' => 'Edit');
			}
			else if(isset($pl_sess->id) && $pl_sess->id === $id) {
				$menu[] = array('action' => 
									array('type' => 'ajax', 
									'url' => '/Playlist/close'.$params, 
									'callback' => 'window["noOpenPL"]'), 
								'title' => 'Close');
			}

			$menu[] = array('action' => array('type' => 'ajax', 'url' => '/Playlist/delete'.$params, 'callback' => 'window["deletePlaylist"]'), 
							'title' => 'Delete');

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
		$this->view->headScript()->appendFile('/js/datatables/js/jquery.dataTables.js','text/javascript');
        $this->view->headScript()->appendFile('/js/airtime/library/library.js','text/javascript');

		$this->view->headLink()->appendStylesheet('/css/media_library.css');
		
		$this->_helper->viewRenderer->setResponseSegment('library'); 

		$format = $this->_getParam('format');
		
		$post = $this->getRequest()->getPost();

		if($format == "json") {
		
			$datatables = StoredFile::searchFilesForPlaylistBuilder($post);

			die(json_encode($datatables));
		}
    }

    public function editFileMdAction()
    {
        $request = $this->getRequest();
        $form = new Application_Form_EditAudioMD();

		$file_id = $this->_getParam('id', null);

		$file = StoredFile::Recall($file_id);
		$form->populate($file->md);  
 
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {  
    
				$formdata = $form->getValues();
				$file->replaceDbMetadata($formdata);

				$this->_helper->redirector('index');
            }
        }
 
        $this->view->form = $form;
    }

    public function quickSearchAction()
    {
		$this->view->headScript()->appendFile('/js/airtime/library/quicksearch.js','text/javascript');

        $this->_helper->viewRenderer->setResponseSegment('quick_search');  

		$this->view->qs_value = $this->search_sess->quick_string;

		$format = $this->_getParam('format', 'layout');

		if($format !== 'json')
			return;

		$search = $this->_getParam('search', null);
		$this->search_sess->quick_string = $search;

		$categories = array("dc:title", "dc:creator", "dc:source");
		$keywords = explode(" ", $search);

		$md = array();

		for($group_id=1; $group_id <= count($keywords); $group_id++) {
			
			for($row_id=1; $row_id <= count($categories); $row_id++) {

				$md["group_".$group_id]["row_".$row_id]["metadata"] = $categories[$row_id-1];
				$md["group_".$group_id]["row_".$row_id]["match"] = "0";
				$md["group_".$group_id]["row_".$row_id]["search"] = $keywords[$group_id-1];
			}
		}

		$this->search_sess->quick = $md;
		
		$currpage = isset($this->search_sess->page) ? $this->search_sess->page : null;
		$order = isset($this->search_sess->order) ? $this->search_sess->order : null;
		$count = StoredFile::searchFiles($md, $order, true, null, null, true);

		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Null($count));
		$paginator->setCurrentPageNumber($currpage);
		$this->view->paginator = $paginator;
		$this->view->files = StoredFile::searchFiles($md, $order, false, $paginator->getCurrentPageNumber(), $paginator->getItemCountPerPage(), true);

		$this->view->html = $this->view->render('library/contents.phtml');
		unset($this->view->files);
		unset($this->view->paginator);
    }
}

















