<?php

class SideplaylistController extends Zend_Controller_Action
{

    protected $pl_sess = null;

    public function init()
    {
        if(!Zend_Auth::getInstance()->hasIdentity())
        {
            $this->_redirect('login/index');
		}

		$this->pl_sess = new Zend_Session_Namespace(UI_PLAYLIST_SESSNAME);
    }

    public function indexAction()
    {
        $this->_helper->layout->setLayout('sidebar');
		$this->_helper->viewRenderer->setResponseSegment('spl'); 

        $pl_sess = $this->pl_sess;
                        
		if(isset($pl_sess->id)) {

			$pl = Playlist::Recall($pl_sess->id);
			if($pl === FALSE) {
				unset($pl_sess->id);
				return;
			}
			
			$this->view->pl = $pl;
		}
    }
}



