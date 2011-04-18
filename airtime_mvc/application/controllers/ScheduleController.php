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
                    ->addActionContext('add-show', 'json')
                    ->addActionContext('cancel-show', 'json')
                    ->addActionContext('get-form', 'json')
                    ->addActionContext('upload-to-sound-cloud', 'json')
                    ->initContext();

		$this->sched_sess = new Zend_Session_Namespace("schedule");
    }

    public function indexAction()
    {
        $this->view->headScript()->appendFile('/js/contextmenu/jjmenu.js','text/javascript');
		$this->view->headScript()->appendFile('/js/datatables/js/jquery.dataTables.js','text/javascript');
        $this->view->headScript()->appendFile('/js/fullcalendar/fullcalendar.js','text/javascript');
        $this->view->headScript()->appendFile('/js/timepicker/jquery.ui.timepicker-0.0.6.js','text/javascript');
		$this->view->headScript()->appendFile('/js/colorpicker/js/colorpicker.js','text/javascript');
    	$this->view->headScript()->appendFile('/js/airtime/schedule/full-calendar-functions.js','text/javascript');
		$this->view->headScript()->appendFile('/js/airtime/schedule/add-show.js','text/javascript');
    	$this->view->headScript()->appendFile('/js/airtime/schedule/schedule.js','text/javascript');

		$this->view->headLink()->appendStylesheet('/css/jquery-ui-timepicker.css');
        $this->view->headLink()->appendStylesheet('/css/fullcalendar.css');
		$this->view->headLink()->appendStylesheet('/css/colorpicker/css/colorpicker.css');
		$this->view->headLink()->appendStylesheet('/css/add-show.css');
        $this->view->headLink()->appendStylesheet('/css/contextmenu.css');

        $formWhat = new Application_Form_AddShowWhat();
		$formWho = new Application_Form_AddShowWho();
		$formWhen = new Application_Form_AddShowWhen();
		$formRepeats = new Application_Form_AddShowRepeats();
		$formStyle = new Application_Form_AddShowStyle();
        $formRecord = new Application_Form_AddShowRR();
        $formAbsoluteRebroadcast = new Application_Form_AddShowAbsoluteRebroadcastDates();
        $formRebroadcast = new Application_Form_AddShowRebroadcastDates();

		$formWhat->removeDecorator('DtDdWrapper');
		$formWho->removeDecorator('DtDdWrapper');
		$formWhen->removeDecorator('DtDdWrapper');
		$formRepeats->removeDecorator('DtDdWrapper');
		$formStyle->removeDecorator('DtDdWrapper');
        $formRecord->removeDecorator('DtDdWrapper');

        $this->view->what = $formWhat;
	    $this->view->when = $formWhen;
	    $this->view->repeats = $formRepeats;
	    $this->view->who = $formWho;
	    $this->view->style = $formStyle;
        $this->view->rr = $formRecord;
        $this->view->absoluteRebroadcast = $formAbsoluteRebroadcast;
        $this->view->rebroadcast = $formRebroadcast;
        $this->view->addNewShow = true;

        $formWhat->populate(array('add_show_id' => '-1'));

        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $user = new User($userInfo->id);
        $this->view->isAdmin = $user->isAdmin();
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

    public function uploadToSoundCloudAction()
    {
        global $CC_CONFIG;
        $show_instance = $this->_getParam('id');
        $show_inst = new ShowInstance($show_instance);

        $file = $show_inst->getRecordedFile();

        if(is_null($file)) {
            $this->view->error = "Recorded file does not exist";
            return;
        }

        $show_name = $show_inst->getName();
        $show_genre = $show_inst->getGenre();
        $show_start_time = $show_inst->getShowStart();

        if(Application_Model_Preference::GetDoSoundCloudUpload())
        {
            for($i=0; $i<$CC_CONFIG['soundcloud-connection-retries']; $i++) {

                $show = new Show($show_inst->getShowId());
                $description = $show->getDescription();
                $hosts = $show->getHosts();

                $tags = array_merge($hosts, array($show_name));

                try {
                    $soundcloud = new ATSoundcloud();
                    $soundcloud_id = $soundcloud->uploadTrack($file->getRealFilePath(), $file->getName(), $description, $tags, $show_start_time, $show_genre);
                    $show_inst->setSoundCloudFileId($soundcloud_id);
                    $this->view->soundcloud_id = $soundcloud_id;
                    break;
                }
                catch (Services_Soundcloud_Invalid_Http_Response_Code_Exception $e) {
                    $code = $e->getHttpCode();
                    if(!in_array($code, array(0, 100))) {
                        break;
                    }
                }

                sleep($CC_CONFIG['soundcloud-connection-wait']);
            }
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

		if (strtotime($today_timestamp) < strtotime($show->getShowStart())) {

            if (($user->isHost($show->getShowId()) || $user->isAdmin()) && !$show->isRecorded() && !$show->isRebroadcast()) {

                $menu[] = array('action' => array('type' => 'ajax', 'url' => '/Schedule/schedule-show-dialog'.$params,
                    'callback' => 'window["buildScheduleDialog"]'), 'title' => 'Add / Remove Content');

                $menu[] = array('action' => array('type' => 'ajax', 'url' => '/Schedule/clear-show'.$params,
                            'callback' => 'window["scheduleRefetchEvents"]'), 'title' => 'Remove All Content');
            }

        }

        if(!$show->isRecorded()) {
            $menu[] = array('action' => array('type' => 'ajax', 'url' => '/Schedule/show-content-dialog'.$params,
                    'callback' => 'window["buildContentDialog"]'), 'title' => 'Show Content');
        }

        if (strtotime($show->getShowEnd()) <= strtotime($today_timestamp) 
            && is_null($show->getSoundCloudFileId())
            && Application_Model_Preference::GetDoSoundCloudUpload()) {
            $menu[] = array('action' => array('type' => 'fn',
                'callback' => "window['uploadToSoundCloud']($id)"),
                'title' => 'Upload to Soundcloud');
        }


        if (strtotime($show->getShowStart()) <= strtotime($today_timestamp) &&
                strtotime($today_timestamp) < strtotime($show->getShowEnd()) &&
                $user->isAdmin() && !$show->isRecorded()) {
                $menu[] = array('action' => array('type' => 'fn',
                    'callback' => "window['confirmCancelShow']($id)"),
                    'title' => 'Cancel Current Show');
        }

		if (strtotime($today_timestamp) < strtotime($show->getShowStart())) {

            if ($user->isAdmin()) {

                $menu[] = array('action' => array('type' => 'ajax', 'url' => '/Schedule/edit-show/format/json/id/'.$id,
                        'callback' => 'window["beginEditShow"]'), 'title' => 'Edit Show');
                //$menu[] = array('action' => array('type' => 'ajax', 'url' => '/Schedule/cancel-show'.$params,
                //        'callback' => 'window["scheduleRefetchEvents"]'), 'title' => 'Edit This Instance and All Following');
                $menu[] = array('action' => array('type' => 'ajax', 'url' => '/Schedule/delete-show'.$params,
                        'callback' => 'window["scheduleRefetchEvents"]'), 'title' => 'Delete This Instance');
                $menu[] = array('action' => array('type' => 'ajax', 'url' => '/Schedule/cancel-show'.$params,
                        'callback' => 'window["scheduleRefetchEvents"]'), 'title' => 'Delete This Instance and All Following');
            }
		}

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

        if($user->isHost($show->getShowId()) || $user->isAdmin()) {
		    $show->scheduleShow(array($plId));
        }

		$this->view->showContent = $show->getShowContent();
		$this->view->timeFilled = $show->getTimeScheduled();
		$this->view->percentFilled = $show->getPercentScheduled();

		$this->view->chosen = $this->view->render('schedule/scheduled-content.phtml');
		unset($this->view->showContent);
    }

    public function clearShowAction()
    {
        $showInstanceId = $this->_getParam('id');
        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $user = new User($userInfo->id);
        $show = new ShowInstance($showInstanceId);

        if($user->isHost($show->getShowId()) || $user->isAdmin())
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

        if($user->isHost($show->getShowId()) || $user->isAdmin()) {
		    $show->removeGroupFromShow($group_id);
        }

		$this->view->showContent = $show->getShowContent();
		$this->view->timeFilled = $show->getTimeScheduled();
		$this->view->percentFilled = $show->getPercentScheduled();
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
		$this->view->percentFilled = $show->getPercentScheduled();

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

        $originalShowId = $show->isRebroadcast();
        if (!is_null($originalShowId)){
            $originalShow = new ShowInstance($originalShowId);
            $originalShowName = $originalShow->getName();
            $originalShowStart = $originalShow->getShowStart();

            $timestamp  = strtotime($originalShowStart);
            $this->view->additionalShowInfo =
                "Rebroadcast of show \"$originalShowName\" from "
                .date("l, F jS", $timestamp)." at ".date("G:i", $timestamp);
        }
		$this->view->showContent = $show->getShowListContent();
        $this->view->dialog = $this->view->render('schedule/show-content-dialog.phtml');
        unset($this->view->showContent);
    }

    public function editShowAction()
    {
        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $user = new User($userInfo->id);
        if(!$user->isAdmin()) {
            return;
        }
        
        $showInstanceId = $this->_getParam('id');
        
        $formWhat = new Application_Form_AddShowWhat();
		$formWho = new Application_Form_AddShowWho();
		$formWhen = new Application_Form_AddShowWhen();
		$formRepeats = new Application_Form_AddShowRepeats();
		$formStyle = new Application_Form_AddShowStyle();
        $formRecord = new Application_Form_AddShowRR();
        $formAbsoluteRebroadcast = new Application_Form_AddShowAbsoluteRebroadcastDates();
        $formRebroadcast = new Application_Form_AddShowRebroadcastDates();

		$formWhat->removeDecorator('DtDdWrapper');
		$formWho->removeDecorator('DtDdWrapper');
		$formWhen->removeDecorator('DtDdWrapper');
		$formRepeats->removeDecorator('DtDdWrapper');
		$formStyle->removeDecorator('DtDdWrapper');
        $formRecord->removeDecorator('DtDdWrapper');
        $formAbsoluteRebroadcast->removeDecorator('DtDdWrapper');
        $formRebroadcast->removeDecorator('DtDdWrapper');

        $this->view->what = $formWhat;
	    $this->view->when = $formWhen;
	    $this->view->repeats = $formRepeats;
	    $this->view->who = $formWho;
	    $this->view->style = $formStyle;
        $this->view->rr = $formRecord;
        $this->view->absoluteRebroadcast = $formAbsoluteRebroadcast;
        $this->view->rebroadcast = $formRebroadcast;
        $this->view->addNewShow = false;

        $showInstance = new ShowInstance($showInstanceId);
        $show = new Show($showInstance->getShowId());
        
        $formWhat->populate(array('add_show_id' => $show->getId(),
                    'add_show_name' => $show->getName(),
                    'add_show_url' => $show->getUrl(),
                    'add_show_genre' => $show->getGenre(),
                    'add_show_description' => $show->getDescription()));

        $formWhen->populate(array('add_show_start_date' => $show->getStartDate(),
                                  'add_show_start_time' => Show::removeSecondsFromTime($show->getStartTime()),
                                  'add_show_duration' => $show->getDuration(),
                                  'add_show_repeats' => $show->isRepeating() ? 1 : 0));

        $days = array();
        $showDays = CcShowDaysQuery::create()->filterByDbShowId($showInstance->getShowId())->find();
        foreach($showDays as $showDay){
            array_push($days, $showDay->getDbDay());
        }
        
        $formRepeats->populate(array('add_show_repeat_type' => $show->getRepeatType(),
                                    'add_show_day_check' => $days,
                                    'add_show_end_date' => $show->getRepeatingEndDate(),
                                    'add_show_no_end' => ($show->getRepeatingEndDate() == '')));

        $formRecord->populate(array('add_show_record' => $show->isRecorded(),
                                'add_show_rebroadcast' => $show->isRebroadcast()));
        $formRecord->getElement('add_show_record')->setOptions(array('disabled' => true));



        $rebroadcastsRelative = $show->getRebroadcastsRelative();
        $rebroadcastFormValues = array();
        $i = 1;
        foreach ($rebroadcastsRelative as $rebroadcast){
            $rebroadcastFormValues["add_show_rebroadcast_date_$i"] = $rebroadcast['day_offset'];
            $rebroadcastFormValues["add_show_rebroadcast_time_$i"] = Show::removeSecondsFromTime($rebroadcast['start_time']);
            $i++;
        }
        $formRebroadcast->populate($rebroadcastFormValues); 

        $rebroadcastsAbsolute = $show->getRebroadcastsAbsolute();
        $rebroadcastAbsoluteFormValues = array();
        $i = 1;
        foreach ($rebroadcastsAbsolute as $rebroadcast){
            $rebroadcastAbsoluteFormValues["add_show_rebroadcast_date_absolute_$i"] = $rebroadcast['start_date'];
            $rebroadcastAbsoluteFormValues["add_show_rebroadcast_time_absolute_$i"] = Show::removeSecondsFromTime($rebroadcast['start_time']);
            $i++;
        }
        $formAbsoluteRebroadcast->populate($rebroadcastAbsoluteFormValues);

        $hosts = array();
        $showHosts = CcShowHostsQuery::create()->filterByDbShow($showInstance->getShowId())->find();
        foreach($showHosts as $showHost){
            array_push($hosts, $showHost->getDbHost());
        }
        $formWho->populate(array('add_show_hosts' => $hosts));


        $formStyle->populate(array('add_show_background_color' => $show->getBackgroundColor(),
                                    'add_show_color' => $show->getColor()));
        
        $this->view->newForm = $this->view->render('schedule/add-show-form.phtml');
        $this->view->entries = 5;
    }

    public function getFormAction(){
        $formWhat = new Application_Form_AddShowWhat();
		$formWho = new Application_Form_AddShowWho();
		$formWhen = new Application_Form_AddShowWhen();
		$formRepeats = new Application_Form_AddShowRepeats();
		$formStyle = new Application_Form_AddShowStyle();
        $formRecord = new Application_Form_AddShowRR();
        $formAbsoluteRebroadcast = new Application_Form_AddShowAbsoluteRebroadcastDates();
        $formRebroadcast = new Application_Form_AddShowRebroadcastDates();

		$formWhat->removeDecorator('DtDdWrapper');
		$formWho->removeDecorator('DtDdWrapper');
		$formWhen->removeDecorator('DtDdWrapper');
		$formRepeats->removeDecorator('DtDdWrapper');
		$formStyle->removeDecorator('DtDdWrapper');
        $formRecord->removeDecorator('DtDdWrapper');

        $this->view->what = $formWhat;
	    $this->view->when = $formWhen;
	    $this->view->repeats = $formRepeats;
	    $this->view->who = $formWho;
	    $this->view->style = $formStyle;
        $this->view->rr = $formRecord;
        $this->view->absoluteRebroadcast = $formAbsoluteRebroadcast;
        $this->view->rebroadcast = $formRebroadcast;
        $this->view->addNewShow = true;

        $formWhat->populate(array('add_show_id' => '-1'));

        $this->view->form = $this->view->render('schedule/add-show-form.phtml');
    }

    public function addShowAction()
    {
        $js = $this->_getParam('data');
        $data = array();

        //need to convert from serialized jQuery array.
        foreach($js as $j){
            $data[$j["name"]] = $j["value"];
        }
     
        $data['add_show_hosts'] =  $this->_getParam('hosts');
        $data['add_show_day_check'] =  $this->_getParam('days');

        if($data['add_show_day_check'] == "") {
            $data['add_show_day_check'] = null;
        }

        $formWhat = new Application_Form_AddShowWhat();
		$formWho = new Application_Form_AddShowWho();
		$formWhen = new Application_Form_AddShowWhen();
		$formRepeats = new Application_Form_AddShowRepeats();
		$formStyle = new Application_Form_AddShowStyle();
        $formRecord = new Application_Form_AddShowRR();
        $formAbsoluteRebroadcast = new Application_Form_AddShowAbsoluteRebroadcastDates();
        $formRebroadcast = new Application_Form_AddShowRebroadcastDates();

		$formWhat->removeDecorator('DtDdWrapper');
		$formWho->removeDecorator('DtDdWrapper');
		$formWhen->removeDecorator('DtDdWrapper');
		$formRepeats->removeDecorator('DtDdWrapper');
		$formStyle->removeDecorator('DtDdWrapper');
        $formRecord->removeDecorator('DtDdWrapper');
        $formAbsoluteRebroadcast->removeDecorator('DtDdWrapper');
        $formRebroadcast->removeDecorator('DtDdWrapper');

        $this->view->what = $formWhat;
	    $this->view->when = $formWhen;
	    $this->view->repeats = $formRepeats;
	    $this->view->who = $formWho;
	    $this->view->style = $formStyle;
        $this->view->rr = $formRecord;
        $this->view->absoluteRebroadcast = $formAbsoluteRebroadcast;
        $this->view->rebroadcast = $formRebroadcast;
        $this->view->addNewShow = true;

		$what = $formWhat->isValid($data);
		$when = $formWhen->isValid($data);
        if($when) {
            $when = $formWhen->checkReliantFields($data);
        }

        if($data["add_show_repeats"]) {
     
		    $repeats = $formRepeats->isValid($data);
            if($repeats) {
                $repeats = $formRepeats->checkReliantFields($data);
            }

            $formAbsoluteRebroadcast->reset();
            //make it valid, results don't matter anyways.
            $rebroadAb = 1;

            if ($data["add_show_rebroadcast"]) {  
                $rebroad = $formRebroadcast->isValid($data);
                if($rebroad) {
                    $rebroad = $formRebroadcast->checkReliantFields($data);
                }
            }
            else {
                $rebroad = 1;
            }
        }
        else {
            $formRebroadcast->reset();
             //make it valid, results don't matter anyways.
            $repeats = 1;
            $rebroad = 1;

            if ($data["add_show_rebroadcast"]) { 
                $rebroadAb = $formAbsoluteRebroadcast->isValid($data);
                if($rebroadAb) {
                    $rebroadAb = $formAbsoluteRebroadcast->checkReliantFields($data);
                }
            }
            else {
                $rebroadAb = 1;
            }
        }

		$who = $formWho->isValid($data);
		$style = $formStyle->isValid($data);

        //If show is a new show (not updated), then get
        //isRecorded from POST data. Otherwise get it from
        //the database since the user is not allowed to
        //update this option.
        $record = false;
        if ($data['add_show_id'] != -1){
            $show = new Show($data['add_show_id']);
            $data['add_show_record'] = $show->isRecorded();
            $record = $formRecord->isValid($data);  
            $formRecord->getElement('add_show_record')->setOptions(array('disabled' => true));
        } else {
            $record = $formRecord->isValid($data);  
        }

        if ($what && $when && $repeats && $who && $style && $record && $rebroadAb && $rebroad) {
            $userInfo = Zend_Auth::getInstance()->getStorage()->read();
            $user = new User($userInfo->id);
			if ($user->isAdmin()) {
                Show::create($data);
            }

            //send back a new form for the user.
            $formWhat->reset();
            $formWhat->populate(array('add_show_id' => '-1'));
            
		    $formWho->reset();
		    $formWhen->reset();
            $formWhen->populate(array('add_show_start_date' => date("Y-m-d"),
                                      'add_show_start_time' => '0:00',
                                      'add_show_duration' => '1:00'));
		    $formRepeats->reset();
            $formRepeats->populate(array('add_show_end_date' => date("Y-m-d")));

		    $formStyle->reset();
            $formRecord->reset();
            $formAbsoluteRebroadcast->reset();
            $formRebroadcast->reset();

            $this->view->newForm = $this->view->render('schedule/add-show-form.phtml');
		}
        else {
            //the form still needs to be "update" since
            //the validity test failed.
            if ($data['add_show_id'] != -1){
                $this->view->addNewShow = false;
            }

            $this->view->form = $this->view->render('schedule/add-show-form.phtml');
        }
    }

    public function cancelShowAction()
    {
        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $user = new User($userInfo->id);

        if($user->isAdmin()) {
		    $showInstanceId = $this->_getParam('id');

            $showInstance = new ShowInstance($showInstanceId);
            $show = new Show($showInstance->getShowId());

            $show->cancelShow($showInstance->getShowStart());
        }
    }

    public function cancelCurrentShowAction()
    {
        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $user = new User($userInfo->id);

        if($user->isAdmin()) {
            $showInstanceId = $this->_getParam('id');
            $show = new ShowInstance($showInstanceId);
            $show->clearShow();
            $show->deleteShow();
        }
    }
}








