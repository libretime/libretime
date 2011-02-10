<?php

class ScheduleController extends Zend_Controller_Action
{

    protected $sched_sess = null;

    public function init()
    {
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
                    ->addActionContext('edit-show', 'json')
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
        $user = new User($userInfo->id);
        if($user->isAdmin())
            $editable = true;
        else
            $editable = false;

		$this->view->events = Show::getFullCalendarEvents($start, $end, $editable);
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
				$show->addShow($data);
			    $this->_redirect('Schedule');
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
        $user = new User($userInfo->id);

        if($user->isAdmin()) {
		    $show = new ShowInstance($showInstanceId);
		    $error = $show->moveShow($deltaDay, $deltaMin);
        }

		if(isset($error))
			$this->view->error = $error;
    }

    public function resizeShowAction()
    {
        $deltaDay = $this->_getParam('day');
		$deltaMin = $this->_getParam('min');
		$showInstanceId = $this->_getParam('showInstanceId');

        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $user = new User($userInfo->id);

        if($user->isAdmin()) {
		    $show = new ShowInstance($showInstanceId);
		    $error = $show->resizeShow($deltaDay, $deltaMin);
        }

		if(isset($error))
			$this->view->error = $error;
    }

    public function deleteShowAction()
    {
        $showInstanceId = $this->_getParam('id');
        		                                       
		$userInfo = Zend_Auth::getInstance()->getStorage()->read();
		$user = new User($userInfo->id);

        if($user->isAdmin()) {
		    $show = new ShowInstance($showInstanceId);
		    $show->deleteShow();
        }
    }

    public function makeContextMenuAction()
    {
        $id = $this->_getParam('id');
        $today_timestamp = date("Y-m-d H:i:s");

        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $user = new User($userInfo->id);

        $show = new ShowInstance($id);

		$params = '/format/json/id/#id#';

		if(strtotime($today_timestamp) < strtotime($show->getShowStart())) {

            if($user->isAdmin()) {

                $menu[] = array('action' => array('type' => 'ajax', 'url' => '/Schedule/delete-show'.$params, 'callback' => 'window["scheduleRefetchEvents"]'), 'title' => 'Delete');
            }
            if($user->isHost($show->getShowId()) || $user->isAdmin()) {
	      
			    $menu[] = array('action' => array('type' => 'ajax', 'url' => '/Schedule/clear-show'.$params, 'callback' => 'window["scheduleRefetchEvents"]'), 'title' => 'Clear');

                $menu[] = array('action' => array('type' => 'ajax', 'url' => '/Schedule/schedule-show-dialog'.$params, 'callback' => 'window["buildScheduleDialog"]'), 'title' => 'Schedule');
            }
		}

        $menu[] = array('action' => array('type' => 'ajax', 'url' => '/Schedule/show-content-dialog'.$params, 'callback' => 'window["buildContentDialog"]'), 
							'title' => 'Show Contents');
		
		//returns format jjmenu is looking for.
		die(json_encode($menu));
    }

    public function scheduleShowAction()
    {
        $showInstanceId = $this->sched_sess->showInstanceId;
		$search = $this->_getParam('search', null);
		$plId = $this->_getParam('plId');

		if($search == "") {
			$search = null;
		}

		$userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $user = new User($userInfo->id);
		$show = new ShowInstance($showInstanceId);

        if($user->isHost($show->getShowId())) {
		    $show->scheduleShow(array($plId));
        }

		$this->view->showContent = $show->getShowContent();
		$this->view->timeFilled = $show->getTimeScheduled();
		$this->view->percentFilled = $show->getPercentScheduledInRange();

		$this->view->chosen = $this->view->render('schedule/scheduled-content.phtml');	
		unset($this->view->showContent);
    }

    public function clearShowAction()
    {
        $showInstanceId = $this->_getParam('id');
        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $user = new User($userInfo->id);
        $show = new ShowInstance($showInstanceId);

        if($user->isHost($show->getShowId()))
            $show->clearShow();
    }

    public function getCurrentPlaylistAction()
    {
        $this->view->entries = Schedule::GetPlayOrderRange();
    }

    public function findPlaylistsAction()
    {
        $post = $this->getRequest()->getPost();
        
		$show = new ShowInstance($this->sched_sess->showInstanceId);
		$playlists = $show->searchPlaylistsForShow($post);

		//for datatables
		die(json_encode($playlists));
    }

    public function removeGroupAction()
    {
        $showInstanceId = $this->sched_sess->showInstanceId;
        $group_id = $this->_getParam('groupId');
		$search = $this->_getParam('search', null);

		$userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $user = new User($userInfo->id);
        $show = new ShowInstance($showInstanceId);

        if($user->isHost($show->getShowId())) {
		    $show->removeGroupFromShow($group_id);
        }

		$this->view->showContent = $show->getShowContent();
		$this->view->timeFilled = $show->getTimeScheduled();
		$this->view->percentFilled = $show->getPercentScheduledInRange();
		$this->view->chosen = $this->view->render('schedule/scheduled-content.phtml');	
		unset($this->view->showContent);
    }

    public function scheduleShowDialogAction()
    {
        $showInstanceId = $this->_getParam('id');
        $this->sched_sess->showInstanceId = $showInstanceId;
        
        $show = new ShowInstance($showInstanceId);
        $start_timestamp = $show->getShowStart();
		$end_timestamp = $show->getShowEnd();

        //check to make sure show doesn't overlap.
        if(Show::getShows($start_timestamp, $end_timestamp, array($showInstanceId))) {
            $this->view->error = "cannot schedule an overlapping show.";
            return;
        }
		
        $start = explode(" ", $start_timestamp);
        $end = explode(" ", $end_timestamp);
        $startTime = explode(":", $start[1]);
        $endTime = explode(":", $end[1]);
        $dateInfo_s = getDate(strtotime($start_timestamp));
        $dateInfo_e = getDate(strtotime($end_timestamp));
		
		$this->view->showContent = $show->getShowContent();
		$this->view->timeFilled = $show->getTimeScheduled();
        $this->view->showName = $show->getName();
		$this->view->showLength = $show->getShowLength();
		$this->view->percentFilled = $show->getPercentScheduledInRange();

        $this->view->s_wday = $dateInfo_s['weekday'];
        $this->view->s_month = $dateInfo_s['month'];
        $this->view->s_day = $dateInfo_s['mday'];
        $this->view->e_wday = $dateInfo_e['weekday'];
        $this->view->e_month = $dateInfo_e['month'];
        $this->view->e_day = $dateInfo_e['mday'];
        $this->view->startTime = sprintf("%d:%02d", $startTime[0], $startTime[1]);
        $this->view->endTime = sprintf("%d:%02d", $endTime[0], $endTime[1]);

		$this->view->chosen = $this->view->render('schedule/scheduled-content.phtml');	
		$this->view->dialog = $this->view->render('schedule/schedule-show-dialog.phtml');
		unset($this->view->showContent);
    }

    public function showContentDialogAction()
    {
        $showInstanceId = $this->_getParam('id');
		$show = new ShowInstance($showInstanceId);

		$this->view->showContent = $show->getShowListContent();
        $this->view->dialog = $this->view->render('schedule/show-content-dialog.phtml');
        unset($this->view->showContent);
    }

    public function editShowAction()
    {
        $showInstanceId = $this->_getParam('id');
        $showInstance = new ShowInstance($showInstanceId);

        $show = new Show($showInstance->getShowId());
    }


}




