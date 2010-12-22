<?php

class LibraryController extends Zend_Controller_Action
{

    protected $pl_sess = null;

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
                    ->initContext();

		$this->pl_sess = new Zend_Session_Namespace(UI_PLAYLIST_SESSNAME);
    }

    public function indexAction()
    {
		$this->view->headScript()->appendFile('/js/campcaster/library/library.js','text/javascript');
	
		$this->_helper->layout->setLayout('library');

		$this->_helper->actionStack('context-menu', 'library');
		$this->_helper->actionStack('contents', 'library');
		$this->_helper->actionStack('index', 'sideplaylist');
    }

    public function contextMenuAction()
    {
		$this->_helper->viewRenderer->setResponseSegment('library');

		$this->view->headScript()->appendFile('/js/campcaster/library/context-menu.js','text/javascript');
		$this->view->headScript()->appendFile('/js/contextmenu/jquery.contextMenu.js','text/javascript');
		$this->view->headLink()->appendStylesheet('/css/jquery.contextMenu.css');

        $pl_sess = $this->pl_sess;
		$contextMenu;

		$contextMenu[] = array('action' => '/Library/delete', 'text' => 'Delete');
  
		if(isset($pl_sess->id))
			$contextMenu[] = array('action' => '/Playlist/add-item', 'text' => 'Add To Playlist');

		$this->view->menu = $contextMenu;
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
				$this->view->message = "file doesn\'t exist";
				return;
			}	

			$res = $file->delete();
			
			if (PEAR::isError($res)) {
				$this->view->message = $res->getMessage();
				return;
			}
		}

		$this->view->message = "file doesn\'t exist";
		
    }

    public function contentsAction()
    {
		$this->_helper->viewRenderer->setResponseSegment('library'); 

        $query["category"] = $this->_getParam('ob', "dc:creator");
		$query["order"] = $this->_getParam('order', "asc");
	
		$this->view->files = StoredFile::getFiles($query);
    }

    public function searchAction()
    {
        // action body
    }


}













