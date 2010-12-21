<?php

class ScheduleController extends Zend_Controller_Action
{

    public function init()
    {
        if(!Zend_Auth::getInstance()->hasIdentity())
        {
            $this->_redirect('login/index');
		}

		$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('event-feed', 'json')
					->addActionContext('add-show-dialog', 'json')
					->addActionContext('add-show', 'json')
					->addActionContext('move-show', 'json')
					->addActionContext('resize-show', 'json')
					->addActionContext('delete-show', 'json')
					->addActionContext('schedule-show', 'json')
					->addActionContext('clear-show', 'json')
                    ->addActionContext('get-current-playlist', 'json')	
                    ->initContext();
    }

    public function indexAction()
    {
        $this->view->headScript()->appendFile('/js/fullcalendar/fullcalendar.min.js','text/javascript');
		$this->view->headScript()->appendFile('/js/contextmenu/jquery.contextMenu.js','text/javascript');
		$this->view->headScript()->appendFile('/js/qtip/jquery.qtip-1.0.0.min.js','text/javascript');

    	$this->view->headScript()->appendFile('/js/campcaster/schedule/schedule.js','text/javascript');

		$this->view->headLink()->appendStylesheet('/css/jquery.contextMenu.css');
		$this->view->headLink()->appendStylesheet('/css/fullcalendar.css');
		$this->view->headLink()->appendStylesheet('/css/schedule.css');


		$eventDefaultMenu = array();
		//$eventDefaultMenu[] = array('action' => '/Schedule/delete-show', 'text' => 'Delete');
  
		$this->view->eventDefaultMenu = $eventDefaultMenu;

		$eventHostMenu[] = array('action' => '/Schedule/delete-show', 'text' => 'Delete');
		$eventHostMenu[] = array('action' => '/Schedule/schedule-show', 'text' => 'Schedule');
		$eventHostMenu[] = array('action' => '/Schedule/clear-show', 'text' => 'Clear');
  
		$this->view->eventHostMenu = $eventHostMenu;
    }

    public function eventFeedAction()
    {
        $start = $this->_getParam('start', null);
		$end = $this->_getParam('end', null);
		$weekday = $this->_getParam('weekday', null);

		if(!is_null($weekday)) {
			$weekday = array($weekday);
		}

		$userInfo = Zend_Auth::getInstance()->getStorage()->read();

		$show = new Show(new User($userInfo->id, $userInfo->type));

		$this->view->events = $show->getFullCalendarEvents($start, $end, $weekday);
    }

    public function addShowDialogAction()
    {
        $request = $this->getRequest();
        $form = new Application_Form_AddShow();
 
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {  
    
				$userInfo = Zend_Auth::getInstance()->getStorage()->read();

				$show = new Show(new User($userInfo->id, $userInfo->type));
				$overlap = $show->addShow($form->getValues());

				if(isset($overlap)) {
					$this->view->overlap = $overlap;
					$this->view->form = $form->__toString();
				}

				return;
			}     
        }
		$this->view->form = $form->__toString();
    }

    public function moveShowAction()
    {
        $deltaDay = $this->_getParam('day');
		$deltaMin = $this->_getParam('min');
		$showId = $this->_getParam('showId');

		$userInfo = Zend_Auth::getInstance()->getStorage()->read();

		$show = new Show(new User($userInfo->id, $userInfo->type));

		$overlap = $show->moveShow($showId, $deltaDay, $deltaMin);

		if(isset($overlap))
			$this->view->overlap = $overlap;
    }

    public function resizeShowAction()
    {
        $deltaDay = $this->_getParam('day');
		$deltaMin = $this->_getParam('min');
		$showId = $this->_getParam('showId');

		$userInfo = Zend_Auth::getInstance()->getStorage()->read();

		$show = new Show(new User($userInfo->id, $userInfo->type));

		$overlap = $show->resizeShow($showId, $deltaDay, $deltaMin);

		if(isset($overlap))
			$this->view->overlap = $overlap;
    }

    public function deleteShowAction()
    {
        $showId = $this->_getParam('showId');
                        
		$userInfo = Zend_Auth::getInstance()->getStorage()->read();

		$show = new Show(new User($userInfo->id, $userInfo->type));
		$show->deleteShow($showId);
    }

    public function makeContextMenuAction()
    {
        // action body
    }

    public function scheduleShowAction()
    {
        $request = $this->getRequest();
        
		if($request->isPost()) {
			$plId = $this->_getParam('plId');
			$start = $this->_getParam('start');
			$showId = $this->_getParam('showId');

			$userInfo = Zend_Auth::getInstance()->getStorage()->read();

			$user = new User($userInfo->id, $userInfo->type);
			$show = new Show($user, $showId);
			$show->scheduleShow($start, $plId);

		}
		else {
			$length = $this->_getParam('length');

			$this->view->playlists = Playlist::findPlaylistMaxLength($length);
		}
    }

    public function clearShowAction()
    {
        $start = $this->_getParam('start');
		$showId = $this->_getParam('showId');

		$userInfo = Zend_Auth::getInstance()->getStorage()->read();
		$user = new User($userInfo->id, $userInfo->type);

		if($user->isHost($showId)) {

			$sched = new ScheduleGroup();
			$this->view->res = $sched->removeAtTime($start);
		}
    }

    public function getSchedulerTimeAction()
    {
        $this->view->headScript()->appendFile('/js/progressbar/jquery.progressbar.min.js','text/javascript');
    }

    public function getCurrentPlaylistAction()
    {
        $this->view->entries = Schedule::GetPlayOrderRange();
    }

}



















