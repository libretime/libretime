<?php

class ScheduleController extends Zend_Controller_Action
{

    protected $sched_sess = null;

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
					->addActionContext('schedule-show-dialog', 'json')
					->addActionContext('clear-show', 'json')
                    ->addActionContext('get-current-playlist', 'json')	
					->addActionContext('find-playlists', 'json')
					->addActionContext('remove-group', 'json')	
                    ->initContext();

		$this->sched_sess = new Zend_Session_Namespace("schedule");
    }

    public function indexAction()
    {
        $this->view->headScript()->appendFile('/js/fullcalendar/fullcalendar.min.js','text/javascript');
		$this->view->headScript()->appendFile('/js/contextmenu/jquery.contextMenu.js','text/javascript');
		$this->view->headScript()->appendFile('/js/datatables/js/jquery.dataTables.js','text/javascript');
		$this->view->headScript()->appendFile('/js/airtime/schedule/full-calendar-functions.js','text/javascript');
    	$this->view->headScript()->appendFile('/js/airtime/schedule/schedule.js','text/javascript');

		$this->view->headLink()->appendStylesheet('/css/jquery.contextMenu.css');
		$this->view->headLink()->appendStylesheet('/css/fullcalendar.css');

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
        $this->view->headScript()->appendFile('/js/fullcalendar/fullcalendar.min.js','text/javascript');
        $this->view->headScript()->appendFile('/js/timepicker/jquery.ui.timepicker-0.0.6.js','text/javascript');
		$this->view->headScript()->appendFile('/js/colorpicker/js/colorpicker.js','text/javascript');
    	$this->view->headScript()->appendFile('/js/airtime/schedule/full-calendar-functions.js','text/javascript');
		$this->view->headScript()->appendFile('/js/airtime/schedule/add-show.js','text/javascript');

		$this->view->headLink()->appendStylesheet('/css/jquery-ui-timepicker.css');
        $this->view->headLink()->appendStylesheet('/css/fullcalendar.css');
		$this->view->headLink()->appendStylesheet('/css/colorpicker/css/colorpicker.css');
		$this->view->headLink()->appendStylesheet('/css/add-show.css');

        $request = $this->getRequest();
        $formWhat = new Application_Form_AddShowWhat();
		$formWhat->removeDecorator('DtDdWrapper');
		$formWho = new Application_Form_AddShowWho();
		$formWho->removeDecorator('DtDdWrapper');
		$formWhen = new Application_Form_AddShowWhen();
		$formWhen->removeDecorator('DtDdWrapper');
		$formRepeats = new Application_Form_AddShowRepeats();
		$formRepeats->removeDecorator('DtDdWrapper');
		$formStyle = new Application_Form_AddShowStyle();
		$formStyle->removeDecorator('DtDdWrapper');
 
        if ($request->isPost()) {

			$data = $request->getPost();

			$what = $formWhat->isValid($data);
			$when = $formWhen->isValid($data);
            if($when) {
                $when = $formWhen->checkReliantFields($data);
            }

            if($data["add_show_repeats"]) {
			    $repeats = $formRepeats->isValid($data);
                if($repeats) {
                    $when = $formRepeats->checkReliantFields($data);
                }
            }
            else {
                $repeats = 1; //make it valid, results don't matter anyways.
            }

			$who = $formWho->isValid($data);
			$style = $formStyle->isValid($data);

            if ($what && $when && $repeats && $who && $style) {  
			
				$userInfo = Zend_Auth::getInstance()->getStorage()->read();

				$show = new Show(new User($userInfo->id, $userInfo->type));
				$overlap = $show->addShow($data);

				if(isset($overlap)) {
					$this->view->overlap = $overlap;
				}
				else {
					$this->_redirect('Schedule');
				}
			}  
        }

		$this->view->what = $formWhat;
		$this->view->when = $formWhen;
		$this->view->repeats = $formRepeats;
		$this->view->who = $formWho;
		$this->view->style = $formStyle;
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
		$date = $this->_getParam('date');
                                                
		$userInfo = Zend_Auth::getInstance()->getStorage()->read();

		$user = new User($userInfo->id, $userInfo->type);
		$show = new Show($user, $showId);
		$show->deleteShow($date);
    }

    public function makeContextMenuAction()
    {
        // action body
    }

    public function scheduleShowAction()
    {  
		$start_timestamp = $this->sched_sess->showStart;
		$end_timestamp = $this->sched_sess->showEnd;
		$showId = $this->sched_sess->showId;
		$search = $this->_getParam('search', null);
		$plId = $this->_getParam('plId');

		if($search == "") {
			$search = null;
		}

		$userInfo = Zend_Auth::getInstance()->getStorage()->read();

		$user = new User($userInfo->id, $userInfo->type);
		$show = new Show($user, $showId);

		$show->scheduleShow($start_timestamp, array($plId));

		$this->view->showContent = $show->getShowContent($start_timestamp);
		$this->view->timeFilled = $show->getTimeScheduled($start_timestamp, $end_timestamp);
		$this->view->percentFilled = Schedule::getPercentScheduledInRange($start_timestamp, $end_timestamp);

		$this->view->chosen = $this->view->render('schedule/scheduled-content.phtml');	
		
		unset($this->view->showContent);
    }

    public function clearShowAction()
    {
        $start = $this->_getParam('start');
        $showId = $this->_getParam('showId');

        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $user = new User($userInfo->id, $userInfo->type);

        if($user->isHost($showId)) {

            $show = new Show($user, $showId);
            $show->clearShow($start);
        }
    }

    public function getCurrentPlaylistAction()
    {
        $this->view->entries = Schedule::GetPlayOrderRange();
    }

    public function findPlaylistsAction()
    {
		$show_id = $this->sched_sess->showId;
		$start_timestamp = $this->sched_sess->showStart;
		$post = $this->getRequest()->getPost();

		$userInfo = Zend_Auth::getInstance()->getStorage()->read();
		$show = new Show(new User($userInfo->id, $userInfo->type), $show_id);
		$playlists = $show->searchPlaylistsForShow($start_timestamp, $post);

		//for datatables
		die(json_encode($playlists));
    }

    public function removeGroupAction()
    {
        $group_id = $this->_getParam('groupId');
		$start_timestamp = $this->sched_sess->showStart;
		$end_timestamp = $this->sched_sess->showEnd;
		$show_id = $this->sched_sess->showId;
		$search = $this->_getParam('search', null);

		$userInfo = Zend_Auth::getInstance()->getStorage()->read();
		$show = new Show(new User($userInfo->id, $userInfo->type), $show_id);
		
		$show->removeGroupFromShow($start_timestamp, $group_id);

		$this->view->showContent = $show->getShowContent($start_timestamp);
		$this->view->timeFilled = $show->getTimeScheduled($start_timestamp, $end_timestamp);
		$this->view->percentFilled = Schedule::getPercentScheduledInRange($start_timestamp, $end_timestamp);

		$this->view->chosen = $this->view->render('schedule/scheduled-content.phtml');	
		
		unset($this->view->showContent);
    }

    public function scheduleShowDialogAction()
    {
        $start_timestamp = $this->_getParam('start');
		$end_timestamp = $this->_getParam('end');
		$showId = $this->_getParam('showId');

		$this->sched_sess->showId = $showId;
		$this->sched_sess->showStart = $start_timestamp;
		$this->sched_sess->showEnd = $end_timestamp;
		
		$userInfo = Zend_Auth::getInstance()->getStorage()->read();

		$user = new User($userInfo->id, $userInfo->type);
		$show = new Show($user, $showId);

		$this->view->showContent = $show->getShowContent($start_timestamp);

		$this->view->timeFilled = $show->getTimeScheduled($start_timestamp, $end_timestamp);
        $this->view->showName = $show->getName();
		$this->view->showLength = $show->getShowLength($start_timestamp, $end_timestamp);
		$this->view->percentFilled = Schedule::getPercentScheduledInRange($start_timestamp, $end_timestamp);

		$this->view->chosen = $this->view->render('schedule/scheduled-content.phtml');	
		$this->view->dialog = $this->view->render('schedule/schedule-show-dialog.phtml');

		unset($this->view->showContent);
    }

   /* Commented out for the 1.6 RC1 release.
    public function showListAction()
    {
        $this->view->headScript()->appendFile('/js/datatables/js/jquery.dataTables.min.js','text/javascript');
        $this->view->headScript()->appendFile('/js/contextmenu/jjmenu.js','text/javascript');
        $this->view->headScript()->appendFile('/js/playlist/showlistview.js','text/javascript');
        $this->view->headLink()->appendStylesheet('/css/contextmenu.css');
        $this->view->headLink()->appendStylesheet('/css/pro_dropdown_3.css');
        $this->view->headLink()->appendStylesheet('/css/styles.css');           
    }

    public function getShowDataAction()
    {
		$this->view->data = Show::getShows("2011-01-27");
		$this->view->showContent = $show->getShowContent($start_timestamp);
		$this->view->timeFilled = $show->getTimeScheduled($start_timestamp, $end_timestamp);
		$this->view->showLength = $show->getShowLength($start_timestamp, $end_timestamp);
		$this->view->percentFilled = Schedule::getPercentScheduledInRange($start_timestamp, $end_timestamp);

		$this->view->chosen = $this->view->render('schedule/scheduled-content.phtml');	
		$this->view->dialog = $this->view->render('schedule/schedule-show-dialog.phtml');

		unset($this->view->showContent);
    }
    */


}
