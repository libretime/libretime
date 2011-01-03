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

			$menu[] = array('action' => array('type' => 'gourl', 'url' => '/Library/edit-file-md/id/#id#'), 
							'title' => 'Edit Metadata');

		}
		else if($type === "pl") {

			if(!isset($pl_sess->id) || $pl_sess->id !== $id) {
				$menu[] = array('action' => 
									array('type' => 'ajax', 
									'url' => '/Playlist/edit/view/spl'.$params, 
									'callback' => 'window["openDiffSPL"]'), 
								'title' => 'Edit');
			}
			else if(isset($pl_sess->id) && $pl_sess->id === $id) {
				$menu[] = array('action' => 
									array('type' => 'ajax', 
									'url' => '/Playlist/close/view/spl'.$params, 
									'callback' => 'window["noOpenPL"]'), 
								'title' => 'Close');
			}

			$menu[] = array('action' => array('type' => 'gourl', 'url' => '/Playlist/metadata'.$params), 
							'title' => 'Description');

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
        $this->view->headScript()->appendFile('/js/campcaster/library/library.js','text/javascript');
        
		$this->_helper->viewRenderer->setResponseSegment('library'); 

        $cat = $this->_getParam('ob', null);
		$or = $this->_getParam('order', null);
		$page = $this->_getParam('page', null);

		if(!is_null($cat) && !is_null($or)) {
			$order["category"] = $cat;
			$order["order"] = $or;
			$this->search_sess->order = $order;
		}
		else if(isset($this->search_sess->order)){
			$order = $this->search_sess->order;
		}
		else{
			$order = null;
		}

		if (isset($this->search_sess->page)) {
			$last_page = $this->search_sess->page;
		}
		else{
			$last_page = null;
		}
		
		$currpage = isset($page) ? $page : $last_page;
		$this->search_sess->page = $currpage;

		$md = isset($this->search_sess->md) ? $this->search_sess->md : array();

		$count = StoredFile::searchFiles($md, $order, true);

		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_Null($count));
		$paginator->setCurrentPageNumber($currpage);
		$this->view->paginator = $paginator;
		$this->view->files = StoredFile::searchFiles($md, $order, false, $paginator->getCurrentPageNumber(), $paginator->getItemCountPerPage());
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


}















