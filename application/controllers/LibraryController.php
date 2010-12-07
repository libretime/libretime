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
        $this->view->headScript()->appendFile('/js/contextmenu/jquery.contextMenu.js','text/javascript');
		$this->view->headScript()->appendFile('/js/campcaster/library/library.js','text/javascript');

		$this->view->headLink()->appendStylesheet('/css/jquery.contextMenu.css');
	
		$this->_helper->actionStack('context-menu', 'library');
    }

    public function contextMenuAction()
    {
        $pl_sess = $this->pl_sess;
		$contextMenu;

		$contextMenu[] = array('action' => '/Library/delete', 'text' => 'Delete');
  
		if(isset($pl_sess->id))
			$contextMenu[] = array('action' => '/Playlist/add-item', 'text' => 'Add To Playlist');

		$this->view->menu = $contextMenu;

		$this->_helper->actionStack('contents', 'library');
    }

    public function deleteAction()
    {
        $id = $this->_getParam('id');
        	
		if (!is_null($id)) {
    		$file = StoredFile::Recall($id);
			
			if (PEAR::isError($file)) {
				die('{"jsonrpc" : "2.0", "error" : {"message": ' + $file->getMessage() + '}}');
			}
			else if(is_null($file)) {
				die('{"jsonrpc" : "2.0", "error" : {"message": "file doesn\'t exist"}}');
			}	

			$res = $file->delete();
			
			if (PEAR::isError($res)) {
				die('{"jsonrpc" : "2.0", "error" : {"message": ' + $res->getMessage() + '}}');
			}
		}
		else {
			die('{"jsonrpc" : "2.0", "error" : {"message": "file doesn\'t exist"}}');
		}

		die('{"jsonrpc" : "2.0"}');
    }

    public function contentsAction()
    {
        $query["category"] = $this->_getParam('ob', "dc:creator");
		$query["order"] = $this->_getParam('order', "asc");
	
		$this->view->files = StoredFile::getFiles($query);
    }

    public function searchAction()
    {
        // action body
    }


}













