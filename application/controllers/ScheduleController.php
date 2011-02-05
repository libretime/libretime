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
                    ->addActionContext('make-context-menu', 'json')
					->addActionContext('add-show-dialog', 'json')
					->addActionContext('add-show', 'json')
					->addActionContext('move-show', 'json')
					->addActionContext('resize-show', 'json')
					->addActionContext('delete-show', 'json')
					->addActionContext('schedule-show', 'json')
					->addActionContext('schedule-show-dialog', 'json')
                    ->addActionContext('show-content-dialog', 'json')
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
        //$this->view->headScript()->appendFile('/js/qtip/jquery.qtip-1.0.0.min.js','text/javascript');
		$this->view->headScript()->appendFile('/js/contextmenu/jjmenu.js','text/javascript');
		$this->view->headScript()->appendFile('/js/datatables/js/jquery.dataTables.js','text/javascript');
		$this->view->headScript()->appendFile('/js/airtime/schedule/full-calendar-functions.js','text/javascript');
    	$this->view->headScript()->appendFile('/js/airtime/schedule/schedule.js','text/javascript');

		$this->view->headLink()->appendStylesheet('/css/contextmenu.css');
		$this->view->headLink()->appendStylesheet('/css/fullcalendar.css');
    }

    public function eventFeedAction()
    {
        $start = $this->_getParam('start', null);
		$end = $this->_getParam('end', null);
		
		$userInfo = Zend_Auth::getInstance()->getStorage()->read();
		$show = new Show(new User($userInfo->id, $userInfo->type));

		$this->view->events = $show->getFullCalendarEvents($start, $end);
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
		$showInstanceId = $this->_getParam('showInstanceId');

		$userInfo = Zend_Auth::getInstance()->getStorage()->read();
		$show = new Show(new User($userInfo->id, $userInfo->type));

		$overlap = $show->moveShow($showInstanceId, $deltaDay, $deltaMin);

		if(isset($overlap))
			$this->view->overlap = $overlap;
    }

    public function resizeShowAction()
    {
        $deltaDay = $this->_getParam('day');
		$deltaMin = $this->_getParam('min');
		$showInstanceId = $this->_getParam('showInstanceId');

		$userInfo = Zend_Auth::getInstance()->getStorage()->read();
		$show = new Show(new User($userInfo->id, $userInfo->type));

		$overlap = $show->resizeShow($showInstanceId, $deltaDay, $deltaMin);

		if(isset($overlap))
			$this->view->overlap = $overlap;
    }

    public function deleteShowAction()
    {
        $showId = $this->_getParam('id');
		$start_timestamp = $this->_getParam('start');
                                                
		$userInfo = Zend_Auth::getInstance()->getStorage()->read();

		$user = new User($userInfo->id, $userInfo->type);
		$show = new Show($user, $showId);
		$show->deleteShow($start_timestamp);
    }

    public function makeContextMenuAction()
    {
        $id = $this->_getParam('id');
		$start_timestamp = $this->_getParam('start');
        $today_timestamp = date("Y-m-d H:i:s");

        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $user = new User($userInfo->id, $userInfo->type);

		$params = '/format/json/id/#id#/start/#start#/end/#end#';

		if(strtotime($today_timestamp) < strtotime($start_timestamp)) {

            if($user->isHost($id)) {

			    $menu[] = array('action' => array('type' => 'ajax', 'url' => '/Schedule/delete-show'.$params, 'callback' => 'window["scheduleRefetchEvents"]'), 
							    'title' => 'Delete');
	      
			    $menu[] = array('action' => array('type' => 'ajax', 'url' => '/Schedule/clear-show'.$params, 'callback' => 'window["scheduleRefetchEvents"]'), 
							    'title' => 'Clear');

                $menu[] = array('action' => array('type' => 'ajax', 'url' => '/Schedule/schedule-show-dialog'.$params, 'callback' => 'window["buildScheduleDialog"]'), 
							    'title' => 'Schedule');
            }
		}

        $menu[] = array('action' => array('type' => 'ajax', 'url' => '/Schedule/show-content-dialog'.$params, 'callback' => 'window["buildContentDialog"]'), 
							'title' => 'Show Contents');
		
		//returns format jjmenu is looking for.
		die(json_encode($menu));
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
        $showId = $this->_getParam('id');

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
        $end_timestamp = $this->sched_sess->showEnd;
		$post = $this->getRequest()->getPost();

		$userInfo = Zend_Auth::getInstance()->getStorage()->read();
		$show = new Show(new User($userInfo->id, $userInfo->type), $show_id);
		$playlists = $show->searchPlaylistsForShow($start_timestamp, $end_timestamp, $post);

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
		$showId = $this->_getParam('id');

		$this->sched_sess->showId = $showId;
		$this->sched_sess->showStart = $start_timestamp;
		$this->sched_sess->showEnd = $end_timestamp;

        $start = explode(" ", $start_timestamp);
        $end = explode(" ", $end_timestamp);
        $startTime = explode(":", $start[1]);
        $endTime = explode(":", $end[1]);
        $dateInfo = getDate(strtotime($start_timestamp));
		
		$userInfo = Zend_Auth::getInstance()->getStorage()->read();

		$user = new User($userInfo->id, $userInfo->type);
		$show = new Show($user, $showId);

		$this->view->showContent = $show->getShowContent($start_timestamp);
		$this->view->timeFilled = $show->getTimeScheduled($start_timestamp, $end_timestamp);
        $this->view->showName = $show->getName();
		$this->view->showLength = $show->getShowLength($start_timestamp, $end_timestamp);
		$this->view->percentFilled = Schedule::getPercentScheduledInRange($start_timestamp, $end_timestamp);

        $this->view->wday = $dateInfo['weekday'];
        $this->view->month = $dateInfo['month'];
        $this->view->day = $dateInfo['mday'];
        $this->view->startTime = sprintf("%d:%02d", $startTime[0], $startTime[1]);
        $this->view->endTime = sprintf("%d:%02d", $endTime[0], $endTime[1]);

		$this->view->chosen = $this->view->render('schedule/scheduled-content.phtml');	
		$this->view->dialog = $this->view->render('schedule/schedule-show-dialog.phtml');

		unset($this->view->showContent);
    }

    public function showContentDialogAction()
    {
        $start_timestamp = $this->_getParam('start');
		$end_timestamp = $this->_getParam('end');
		$showId = $this->_getParam('id');

        $userInfo = Zend_Auth::getInstance()->getStorage()->read();

		$user = new User($userInfo->id, $userInfo->type);
		$show = new Show($user, $showId);

		$this->view->showContent = $show->getShowListContent($start_timestamp);
        $this->view->dialog = $this->view->render('schedule/show-content-dialog.phtml');

        unset($this->view->showContent);
    }


}


