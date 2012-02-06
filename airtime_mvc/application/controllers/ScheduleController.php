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
                    ->addActionContext('content-context-menu', 'json')
                    ->addActionContext('set-time-scale', 'json')
                    ->addActionContext('set-time-interval', 'json')
                    ->initContext();

		$this->sched_sess = new Zend_Session_Namespace("schedule");
    }

    public function indexAction()
    {
        $request = $this->getRequest();
        $baseUrl = $request->getBaseUrl();
        $baseDir = dirname($_SERVER['SCRIPT_FILENAME']);

        $this->view->headScript()->appendFile($baseUrl.'/js/contextmenu/jjmenu.js?'.filemtime($baseDir.'/js/contextmenu/jjmenu.js'),'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/datatables/js/jquery.dataTables.js?'.filemtime($baseDir.'/js/datatables/js/jquery.dataTables.js'),'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/datatables/plugin/dataTables.pluginAPI.js?'.filemtime($baseDir.'/js/datatables/plugin/dataTables.pluginAPI.js'),'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/fullcalendar/fullcalendar.js?'.filemtime($baseDir.'/js/fullcalendar/fullcalendar.js'),'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/timepicker/jquery.ui.timepicker.js?'.filemtime($baseDir.'/js/timepicker/jquery.ui.timepicker.js'),'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/colorpicker/js/colorpicker.js?'.filemtime($baseDir.'/js/colorpicker/js/colorpicker.js'),'text/javascript');

        //full-calendar-functions.js requires this variable, so that datePicker widget can be offset to server time instead of client time
        $this->view->headScript()->appendScript("var timezoneOffset = ".date("Z")."; //in seconds");
        $this->view->headScript()->appendFile($baseUrl.'/js/airtime/schedule/full-calendar-functions.js?'.filemtime($baseDir.'/js/airtime/schedule/full-calendar-functions.js'),'text/javascript');

        $this->view->headScript()->appendFile($baseUrl.'/js/airtime/schedule/add-show.js?'.filemtime($baseDir.'/js/airtime/schedule/add-show.js'),'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/airtime/schedule/schedule.js?'.filemtime($baseDir.'/js/airtime/schedule/schedule.js'),'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/meioMask/jquery.meio.mask.js?'.filemtime($baseDir.'/js/meioMask/jquery.meio.mask.js'),'text/javascript');

        $this->view->headLink()->appendStylesheet($baseUrl.'/css/jquery.ui.timepicker.css?'.filemtime($baseDir.'/css/jquery.ui.timepicker.css'));
        $this->view->headLink()->appendStylesheet($baseUrl.'/css/fullcalendar.css?'.filemtime($baseDir.'/css/fullcalendar.css'));
        $this->view->headLink()->appendStylesheet($baseUrl.'/css/colorpicker/css/colorpicker.css?'.filemtime($baseDir.'/css/colorpicker/css/colorpicker.css'));
        $this->view->headLink()->appendStylesheet($baseUrl.'/css/add-show.css?'.filemtime($baseDir.'/css/add-show.css'));
        $this->view->headLink()->appendStylesheet($baseUrl.'/css/contextmenu.css?'.filemtime($baseDir.'/css/contextmenu.css'));

        Application_Model_Schedule::createNewFormSections($this->view);

        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $user = new Application_Model_User($userInfo->id);
        $this->view->isAdmin = $user->isAdmin();
        $this->view->isProgramManager = $user->isUserType('P');

        $this->view->headScript()->appendScript("var weekStart = ".Application_Model_Preference::GetWeekStartDay().";");
    }

    public function eventFeedAction()
    {
        $start = new DateTime($this->_getParam('start', null));
        $start->setTimezone(new DateTimeZone("UTC"));
        $end = new DateTime($this->_getParam('end', null));
        $end->setTimezone(new DateTimeZone("UTC"));

		$userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $user = new Application_Model_User($userInfo->id);
        if($user->isUserType(array(UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER)))
            $editable = true;
        else
            $editable = false;

		$this->view->events = Application_Model_Show::getFullCalendarEvents($start, $end, $editable);
    }

    public function moveShowAction()
    {
        $deltaDay = $this->_getParam('day');
        $deltaMin = $this->_getParam('min');
        $showInstanceId = $this->_getParam('showInstanceId');

        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $user = new Application_Model_User($userInfo->id);

        if($user->isUserType(array(UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER))) {
            try{
                $showInstance = new Application_Model_ShowInstance($showInstanceId);
            }catch(Exception $e){
                $this->view->show_error = true;
                return false;
            }
            $error = $showInstance->moveShow($deltaDay, $deltaMin);
        }

        if(isset($error))
            $this->view->error = $error;

    }

    public function resizeShowAction()
    {
        $deltaDay = $this->_getParam('day');
		$deltaMin = $this->_getParam('min');
		$showId = $this->_getParam('showId');

        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $user = new Application_Model_User($userInfo->id);

        if ($user->isUserType(array(UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER))) {
            try{
                $show = new Application_Model_Show($showId);
            }catch(Exception $e){
                $this->view->show_error = true;
                return false;
            }
		    $error = $show->resizeShow($deltaDay, $deltaMin);
        }

		if (isset($error)) {
			$this->view->error = $error;
		}
    }

    public function deleteShowAction()
    {
        $showInstanceId = $this->_getParam('id');

		$userInfo = Zend_Auth::getInstance()->getStorage()->read();
		$user = new Application_Model_User($userInfo->id);

        if ($user->isUserType(array(UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER))) {

            try {
		      $showInstance = new Application_Model_ShowInstance($showInstanceId);
            }
            catch(Exception $e){
                $this->view->show_error = true;
                return false;
            }

		    $showInstance->delete();

		    $this->view->show_id = $showInstance->getShowId();
        }
    }

    public function uploadToSoundCloudAction()
    {
        global $CC_CONFIG;
        $show_instance = $this->_getParam('id');
        try{
            $show_inst = new Application_Model_ShowInstance($show_instance);
        }catch(Exception $e){
            $this->view->show_error = true;
            return false;
        }

        $file = $show_inst->getRecordedFile();
        $id = $file->getId();
        $res = exec("/usr/lib/airtime/utils/soundcloud-uploader $id > /dev/null &");
        // we should die with ui info
        die();
    }

    public function makeContextMenuAction()
    {
        $id = $this->_getParam('id');
        $epochNow = time();

        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $user = new Application_Model_User($userInfo->id);
        try{
            $show = new Application_Model_ShowInstance($id);
        }catch(Exception $e){
            $this->view->show_error = true;
            return false;
        }


		$params = '/format/json/id/#id#';

        $showStartDateHelper = Application_Model_DateHelper::ConvertToLocalDateTime($show->getShowInstanceStart());
        $showEndDateHelper = Application_Model_DateHelper::ConvertToLocalDateTime($show->getShowInstanceEnd());

        $menu = array();

		if ($epochNow < $showStartDateHelper->getTimestamp()) {

            if ($user->isUserType(array(UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER, UTYPE_HOST),$show->getShowId()) && !$show->isRecorded() && !$show->isRebroadcast()) {

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

        if ($showEndDateHelper->getTimestamp() <= $epochNow
            && $show->isRecorded()
            && Application_Model_Preference::GetUploadToSoundcloudOption()) {
                if(is_null($show->getSoundCloudFileId())){
                    $menu[] = array('action' => array('type' => 'fn',
                        'callback' => "window['uploadToSoundCloud']($id)"),
                        'title' => 'Upload to SoundCloud');
                }else{
                    $menu[] = array('action' => array('type' => 'fn',
                        'callback' => "window['uploadToSoundCloud']($id)"),
                        'title' => 'Re-upload to SoundCloud');
                }
        }


        if ($showStartDateHelper->getTimestamp() <= $epochNow &&
                $epochNow < $showEndDateHelper->getTimestamp() &&
                $user->isUserType(array(UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER))) {
            if ($show->isRecorded()) {
                $menu[] = array('action' => array('type' => 'fn',
                    'callback' => "window['confirmCancelRecordedShow']($id)"),
                    'title' => 'Cancel Current Show');
            } else {
                $menu[] = array('action' => array('type' => 'fn',
                    'callback' => "window['confirmCancelShow']($id)"),
                    'title' => 'Cancel Current Show');
            }
        }

        if ($epochNow < $showStartDateHelper->getTimestamp()) {

            if ($user->isUserType(array(UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER))) {

                $menu[] = array('action' => array('type' => 'ajax', 'url' => '/Schedule/edit-show/format/json/id/'.$id,
                        'callback' => 'window["beginEditShow"]'), 'title' => 'Edit Show');
                $menu[] = array('action' => array('type' => 'ajax', 'url' => '/Schedule/delete-show'.$params,
                        'callback' => 'window["scheduleRefetchEvents"]'), 'title' => 'Delete This Instance');
                if ($show->getShow()->isRepeating() || $show->getShow()->isRebroadcast()) {
                    $menu[] = array('action' => array('type' => 'ajax', 'url' => '/Schedule/cancel-show'.$params,
                            'callback' => 'window["scheduleRefetchEvents"]'), 'title' => 'Delete This Instance and All Following');
                }
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
        $user = new Application_Model_User($userInfo->id);
        try{
		  $show = new Application_Model_ShowInstance($showInstanceId);
        }catch(Exception $e){
            $this->view->show_error = true;
            return false;
        }

        if($user->isUserType(array(UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER, UTYPE_HOST),$show->getShowId())) {
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
        $user = new Application_Model_User($userInfo->id);
        try{
            $show = new Application_Model_ShowInstance($showInstanceId);
        }catch(Exception $e){
            $this->view->show_error = true;
            return false;
        }

        if($user->isUserType(array(UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER, UTYPE_HOST),$show->getShowId()))
            $show->clearShow();
    }

    public function getCurrentPlaylistAction()
    {

        $range = Application_Model_Schedule::GetPlayOrderRange();

        /* Convert all UTC times to localtime before sending back to user. */
        if (isset($range["previous"])){
            $range["previous"]["starts"] = Application_Model_DateHelper::ConvertToLocalDateTimeString($range["previous"]["starts"]);
            $range["previous"]["ends"] = Application_Model_DateHelper::ConvertToLocalDateTimeString($range["previous"]["ends"]);
        }
        if (isset($range["current"])){
            $range["current"]["starts"] = Application_Model_DateHelper::ConvertToLocalDateTimeString($range["current"]["starts"]);
            $range["current"]["ends"] = Application_Model_DateHelper::ConvertToLocalDateTimeString($range["current"]["ends"]);
        }
        if (isset($range["next"])){
            $range["next"]["starts"] = Application_Model_DateHelper::ConvertToLocalDateTimeString($range["next"]["starts"]);
            $range["next"]["ends"] = Application_Model_DateHelper::ConvertToLocalDateTimeString($range["next"]["ends"]);
        }

        Application_Model_Show::ConvertToLocalTimeZone($range["currentShow"], array("starts", "ends", "start_timestamp", "end_timestamp"));
        Application_Model_Show::ConvertToLocalTimeZone($range["nextShow"], array("starts", "ends", "start_timestamp", "end_timestamp"));

        $this->view->entries = $range;
    }

    public function findPlaylistsAction()
    {
        $post = $this->getRequest()->getPost();
        try{
            $show = new Application_Model_ShowInstance($this->sched_sess->showInstanceId);
        }catch(Exception $e){
            $this->view->show_error = true;
            return false;
        }

        $playlists = $show->searchPlaylistsForShow($post);
        foreach( $playlists['aaData'] as &$data){
            // calling two functions to format time to 1 decimal place
            $sec = Application_Model_Playlist::playlistTimeToSeconds($data['length']);
            $data['length'] = Application_Model_Playlist::secondsToPlaylistTime($sec);
        }

        //for datatables
        die(json_encode($playlists));
    }

    public function removeGroupAction()
    {
        $showInstanceId = $this->sched_sess->showInstanceId;
        $group_id = $this->_getParam('groupId');
		$search = $this->_getParam('search', null);

		$userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $user = new Application_Model_User($userInfo->id);
        try{
            $show = new Application_Model_ShowInstance($showInstanceId);
        }catch(Exception $e){
            $this->view->show_error = true;
            return false;
        }

        if($user->isUserType(array(UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER, UTYPE_HOST),$show->getShowId())) {
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

        try{
            $show = new Application_Model_ShowInstance($showInstanceId);
        }catch(Exception $e){
            $this->view->show_error = true;
            return false;
        }

        $start_timestamp = $show->getShowInstanceStart();
		$end_timestamp = $show->getShowInstanceEnd();

        $dateInfo_s = getDate(strtotime(Application_Model_DateHelper::ConvertToLocalDateTimeString($start_timestamp)));
        $dateInfo_e = getDate(strtotime(Application_Model_DateHelper::ConvertToLocalDateTimeString($end_timestamp)));

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
        $this->view->startTime = sprintf("%02d:%02d", $dateInfo_s['hours'], $dateInfo_s['minutes']);
        $this->view->endTime = sprintf("%02d:%02d", $dateInfo_e['hours'], $dateInfo_e['minutes']);

		$this->view->chosen = $this->view->render('schedule/scheduled-content.phtml');
		$this->view->dialog = $this->view->render('schedule/schedule-show-dialog.phtml');
		unset($this->view->showContent);
    }

    public function showContentDialogAction()
    {
        $showInstanceId = $this->_getParam('id');
        try{
            $show = new Application_Model_ShowInstance($showInstanceId);
        }catch(Exception $e){
            $this->view->show_error = true;
            return false;
        }

        $originalShowId = $show->isRebroadcast();
        if (!is_null($originalShowId)){
            try{
                $originalShow = new Application_Model_ShowInstance($originalShowId);
            }catch(Exception $e){
                $this->view->show_error = true;
                return false;
            }
            $originalShowName = $originalShow->getName();
            $originalShowStart = $originalShow->getShowInstanceStart();

            //convert from UTC to user's timezone for display.
            $originalDateTime = new DateTime($originalShowStart, new DateTimeZone("UTC"));
            $originalDateTime->setTimezone(new DateTimeZone(date_default_timezone_get()));
            //$timestamp  = Application_Model_DateHelper::ConvertToLocalDateTimeString($originalDateTime->format("Y-m-d H:i:s"));
            $this->view->additionalShowInfo =
                "Rebroadcast of show \"$originalShowName\" from "
                .$originalDateTime->format("l, F jS")." at ".$originalDateTime->format("G:i");
        }
        $this->view->showLength = $show->getShowLength();
        $this->view->timeFilled = $show->getTimeScheduled();
        $this->view->percentFilled = $show->getPercentScheduled();
        $this->view->showContent = $show->getShowListContent();
        $this->view->dialog = $this->view->render('schedule/show-content-dialog.phtml');
        unset($this->view->showContent);
    }

    public function editShowAction()
    {
        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $user = new Application_Model_User($userInfo->id);
        if(!$user->isUserType(array(UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER))) {
            return;
        }

        $isSaas = Application_Model_Preference::GetPlanLevel() == 'disabled'?false:true;

        $showInstanceId = $this->_getParam('id');
        try{
            $showInstance = new Application_Model_ShowInstance($showInstanceId);
        }catch(Exception $e){
            $this->view->show_error = true;
            return false;
        }

        $formWhat = new Application_Form_AddShowWhat();
		$formWho = new Application_Form_AddShowWho();
		$formWhen = new Application_Form_AddShowWhen();
		$formRepeats = new Application_Form_AddShowRepeats();
		$formStyle = new Application_Form_AddShowStyle();

		$formWhat->removeDecorator('DtDdWrapper');
		$formWho->removeDecorator('DtDdWrapper');
		$formWhen->removeDecorator('DtDdWrapper');
		$formRepeats->removeDecorator('DtDdWrapper');
		$formStyle->removeDecorator('DtDdWrapper');

        $this->view->what = $formWhat;
	    $this->view->when = $formWhen;
	    $this->view->repeats = $formRepeats;
	    $this->view->who = $formWho;
	    $this->view->style = $formStyle;
        $this->view->addNewShow = false;

        $show = new Application_Model_Show($showInstance->getShowId());

        $formWhat->populate(array('add_show_id' => $show->getId(),
                    'add_show_name' => $show->getName(),
                    'add_show_url' => $show->getUrl(),
                    'add_show_genre' => $show->getGenre(),
                    'add_show_description' => $show->getDescription()));

        $startsDateTime = new DateTime($show->getStartDate()." ".$show->getStartTime(), new DateTimeZone("UTC"));
        $endsDateTime = new DateTime($show->getEndDate()." ".$show->getEndTime(), new DateTimeZone("UTC"));

        $startsDateTime->setTimezone(new DateTimeZone(date_default_timezone_get()));
        $endsDateTime->setTimezone(new DateTimeZone(date_default_timezone_get()));

        $formWhen->populate(array('add_show_start_date' => $startsDateTime->format("Y-m-d"),
                                  'add_show_start_time' => $startsDateTime->format("H:i"),
        						  'add_show_end_date_no_repeat' => $endsDateTime->format("Y-m-d"),
        						  'add_show_end_time'	=> $endsDateTime->format("H:i"),
                                  'add_show_duration' => $show->getDuration(true),
                                  'add_show_repeats' => $show->isRepeating() ? 1 : 0));

        if ($show->isStartDateTimeInPast()){
            $formWhen->getElement('add_show_start_date')->setOptions(array('disabled' => true));
        }

        //need to get the days of the week in the php timezone (for the front end).
        $days = array();
        $showDays = CcShowDaysQuery::create()->filterByDbShowId($showInstance->getShowId())->find();
        foreach($showDays as $showDay){
            $showStartDay = new DateTime($showDay->getDbFirstShow(), new DateTimeZone($showDay->getDbTimezone()));
            $showStartDay->setTimezone(new DateTimeZone(date_default_timezone_get()));
            array_push($days, $showStartDay->format('w'));
        }

        $displayedEndDate = new DateTime($show->getRepeatingEndDate(), new DateTimeZone($showDays[0]->getDbTimezone()));
        $displayedEndDate->sub(new DateInterval("P1D"));//end dates are stored non-inclusively.
        $displayedEndDate->setTimezone(new DateTimeZone(date_default_timezone_get()));

        $formRepeats->populate(array('add_show_repeat_type' => $show->getRepeatType(),
                                    'add_show_day_check' => $days,
                                    'add_show_end_date' => $displayedEndDate->format("Y-m-d"),
                                    'add_show_no_end' => ($show->getRepeatingEndDate() == '')));

        $hosts = array();
        $showHosts = CcShowHostsQuery::create()->filterByDbShow($showInstance->getShowId())->find();
        foreach($showHosts as $showHost){
            array_push($hosts, $showHost->getDbHost());
        }
        $formWho->populate(array('add_show_hosts' => $hosts));
        $formStyle->populate(array('add_show_background_color' => $show->getBackgroundColor(),
                                    'add_show_color' => $show->getColor()));

        if(!$isSaas){
            $formRecord = new Application_Form_AddShowRR();
            $formAbsoluteRebroadcast = new Application_Form_AddShowAbsoluteRebroadcastDates();
            $formRebroadcast = new Application_Form_AddShowRebroadcastDates();

            $formRecord->removeDecorator('DtDdWrapper');
            $formAbsoluteRebroadcast->removeDecorator('DtDdWrapper');
            $formRebroadcast->removeDecorator('DtDdWrapper');

            $this->view->rr = $formRecord;
            $this->view->absoluteRebroadcast = $formAbsoluteRebroadcast;
            $this->view->rebroadcast = $formRebroadcast;

            $formRecord->populate(array('add_show_record' => $show->isRecorded(),
                                'add_show_rebroadcast' => $show->isRebroadcast()));

            $formRecord->getElement('add_show_record')->setOptions(array('disabled' => true));



            $rebroadcastsRelative = $show->getRebroadcastsRelative();
            $rebroadcastFormValues = array();
            $i = 1;
            foreach ($rebroadcastsRelative as $rebroadcast){
                $rebroadcastFormValues["add_show_rebroadcast_date_$i"] = $rebroadcast['day_offset'];
                $rebroadcastFormValues["add_show_rebroadcast_time_$i"] = Application_Model_DateHelper::removeSecondsFromTime($rebroadcast['start_time']);
                $i++;
            }
            $formRebroadcast->populate($rebroadcastFormValues);

            $rebroadcastsAbsolute = $show->getRebroadcastsAbsolute();
            $rebroadcastAbsoluteFormValues = array();
            $i = 1;
            foreach ($rebroadcastsAbsolute as $rebroadcast){
                $rebroadcastAbsoluteFormValues["add_show_rebroadcast_date_absolute_$i"] = $rebroadcast['start_date'];
                $rebroadcastAbsoluteFormValues["add_show_rebroadcast_time_absolute_$i"] = $rebroadcast['start_time'];
                $i++;
            }
            $formAbsoluteRebroadcast->populate($rebroadcastAbsoluteFormValues);
        }

        $this->view->newForm = $this->view->render('schedule/add-show-form.phtml');
        $this->view->entries = 5;
    }

    public function getFormAction(){
        Application_Model_Schedule::createNewFormSections($this->view);
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

        $show = new Application_Model_Show($data['add_show_id']);

        $startDateModified = true;
        if ($data['add_show_id'] != -1 && !array_key_exists('add_show_start_date', $data)){
            //show is being updated and changing the start date was disabled, since the
            //array key does not exist. We need to repopulate this entry from the db.
            //The start date will be return in UTC time, so lets convert it to local time.
            $dt = Application_Model_DateHelper::ConvertToLocalDateTime($show->getStartDate());
            $data['add_show_start_date'] = $dt->format("Y-m-d");
            $startDateModified = false;
        }

        $data['add_show_hosts'] =  $this->_getParam('hosts');
        $data['add_show_day_check'] =  $this->_getParam('days');

        if($data['add_show_day_check'] == "") {
            $data['add_show_day_check'] = null;
        }

        $isSaas = Application_Model_Preference::GetPlanLevel() == 'disabled'?false:true;
        $record = false;

        $formWhat = new Application_Form_AddShowWhat();
		$formWho = new Application_Form_AddShowWho();
		$formWhen = new Application_Form_AddShowWhen();
		$formRepeats = new Application_Form_AddShowRepeats();
		$formStyle = new Application_Form_AddShowStyle();

		$formWhat->removeDecorator('DtDdWrapper');
		$formWho->removeDecorator('DtDdWrapper');
		$formWhen->removeDecorator('DtDdWrapper');
		$formRepeats->removeDecorator('DtDdWrapper');
		$formStyle->removeDecorator('DtDdWrapper');

		$what = $formWhat->isValid($data);
		$when = $formWhen->isValid($data);
        if($when) {
            $when = $formWhen->checkReliantFields($data, $startDateModified);
        }


        //The way the following code works is that is parses the hour and
        //minute from a string with the format "1h 20m" or "2h" or "36m".
        //So we are detecting whether an hour or minute value exists via strpos
        //and then parse appropriately. A better way to do this in the future is
        //actually pass the format from javascript in the format hh:mm so we don't
        //have to do this extra String parsing.
        $hPos = strpos($data["add_show_duration"], 'h');
        $mPos = strpos($data["add_show_duration"], 'm');

        $hValue = 0;
        $mValue = 0;

        if($hPos !== false){
        	$hValue = trim(substr($data["add_show_duration"], 0, $hPos));
        }
        if($mPos !== false){
            $hPos = $hPos === FALSE ? 0 : $hPos+1;
        	$mValue = trim(substr($data["add_show_duration"], $hPos, -1 ));
        }

        $data["add_show_duration"] = $hValue.":".$mValue;

        if(!$isSaas){
            $formRecord = new Application_Form_AddShowRR();
            $formAbsoluteRebroadcast = new Application_Form_AddShowAbsoluteRebroadcastDates();
            $formRebroadcast = new Application_Form_AddShowRebroadcastDates();

            $formRecord->removeDecorator('DtDdWrapper');
            $formAbsoluteRebroadcast->removeDecorator('DtDdWrapper');
            $formRebroadcast->removeDecorator('DtDdWrapper');

            //If show is a new show (not updated), then get
            //isRecorded from POST data. Otherwise get it from
            //the database since the user is not allowed to
            //update this option.
            if ($data['add_show_id'] != -1){
                $data['add_show_record'] = $show->isRecorded();
                $record = $formRecord->isValid($data);
                $formRecord->getElement('add_show_record')->setOptions(array('disabled' => true));
            } else {
                $record = $formRecord->isValid($data);
            }
        }

        if($data["add_show_repeats"]) {
		    $repeats = $formRepeats->isValid($data);
            if($repeats) {
                $repeats = $formRepeats->checkReliantFields($data);
            }
            if(!$isSaas){
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
        }
        else {
            $repeats = 1;
            if(!$isSaas){
                $formRebroadcast->reset();
                 //make it valid, results don't matter anyways.
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
        }

		$who = $formWho->isValid($data);
		$style = $formStyle->isValid($data);
        if ($what && $when && $repeats && $who && $style) {
            if(!$isSaas){
                if($record && $rebroadAb && $rebroad){
                    $userInfo = Zend_Auth::getInstance()->getStorage()->read();
                    $user = new Application_Model_User($userInfo->id);
                    if ($user->isUserType(array(UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER))) {
                        Application_Model_Show::create($data);
                    }

                    //send back a new form for the user.
                    Application_Model_Schedule::createNewFormSections($this->view);

                    $this->view->newForm = $this->view->render('schedule/add-show-form.phtml');
                }else{
                    $this->view->what = $formWhat;
                    $this->view->when = $formWhen;
                    $this->view->repeats = $formRepeats;
                    $this->view->who = $formWho;
                    $this->view->style = $formStyle;
                    $this->view->rr = $formRecord;
                    $this->view->absoluteRebroadcast = $formAbsoluteRebroadcast;
                    $this->view->rebroadcast = $formRebroadcast;
                    $this->view->addNewShow = true;

                    //the form still needs to be "update" since
                    //the validity test failed.
                    if ($data['add_show_id'] != -1){
                        $this->view->addNewShow = false;
                    }
                    if (!$startDateModified){
                        $formWhen->getElement('add_show_start_date')->setOptions(array('disabled' => true));
                    }

                    $this->view->form = $this->view->render('schedule/add-show-form.phtml');

                }
            }else{
                $userInfo = Zend_Auth::getInstance()->getStorage()->read();
                $user = new Application_Model_User($userInfo->id);
                if ($user->isUserType(array(UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER))) {
                    Application_Model_Show::create($data);
                }

                //send back a new form for the user.
                Application_Model_Schedule::createNewFormSections($this->view);

                $this->view->newForm = $this->view->render('schedule/add-show-form.phtml');
            }
		}
        else {
            $this->view->what = $formWhat;
            $this->view->when = $formWhen;
            $this->view->repeats = $formRepeats;
            $this->view->who = $formWho;
            $this->view->style = $formStyle;
            if(!$isSaas){
                $this->view->rr = $formRecord;
                $this->view->absoluteRebroadcast = $formAbsoluteRebroadcast;
                $this->view->rebroadcast = $formRebroadcast;
            }
            $this->view->addNewShow = true;

            //the form still needs to be "update" since
            //the validity test failed.
            if ($data['add_show_id'] != -1){
                $this->view->addNewShow = false;
            }
            if (!$startDateModified){
                $formWhen->getElement('add_show_start_date')->setOptions(array('disabled' => true));
            }

            $this->view->form = $this->view->render('schedule/add-show-form.phtml');
        }
    }

    public function cancelShowAction()
    {
        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $user = new Application_Model_User($userInfo->id);

        if($user->isUserType(array(UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER))) {
		    $showInstanceId = $this->_getParam('id');

		    try {
                $showInstance = new Application_Model_ShowInstance($showInstanceId);
		    }
		    catch(Exception $e){
                $this->view->show_error = true;
                return false;
            }
            $show = new Application_Model_Show($showInstance->getShowId());

            $show->cancelShow($showInstance->getShowInstanceStart());

            $this->view->show_id = $showInstance->getShowId();
        }
    }

    public function cancelCurrentShowAction()
    {
        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $user = new Application_Model_User($userInfo->id);

        if($user->isUserType(array(UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER))) {
            $showInstanceId = $this->_getParam('id');
            try{
                $showInstance = new Application_Model_ShowInstance($showInstanceId);
            }catch(Exception $e){
                $this->view->show_error = true;
                return false;
            }
            $showInstance->clearShow();
            $showInstance->delete();
            // send 'cancel-current-show' command to pypo
            Application_Model_RabbitMq::SendMessageToPypo("cancel_current_show", array());
        }
    }

    public function contentContextMenuAction(){
    	global $CC_CONFIG;

    	$id = $this->_getParam('id');

        $params = '/format/json/id/#id#/';

        $paramsPop = str_replace('#id#', $id, $params);

        // added for downlaod
        $id = $this->_getParam('id');

        $file_id = $this->_getParam('id', null);
        $file = Application_Model_StoredFile::Recall($file_id);

        $baseUrl = $this->getRequest()->getBaseUrl();
        $url = $file->getRelativeFileUrl($baseUrl).'/download/true';
        $menu[] = array('action' => array('type' => 'gourl', 'url' => $url),
            				'title' => 'Download');

        //returns format jjmenu is looking for.
        die(json_encode($menu));
    }

    /**
     * Sets the user specific preference for which time scale to use in Calendar.
     * This is only being used by schedule.js at the moment.
     */
    public function setTimeScaleAction() {
    	Application_Model_Preference::SetCalendarTimeScale($this->_getParam('timeScale'));
    }

/**
     * Sets the user specific preference for which time interval to use in Calendar.
     * This is only being used by schedule.js at the moment.
     */
    public function setTimeIntervalAction() {
    	Application_Model_Preference::SetCalendarTimeInterval($this->_getParam('timeInterval'));
    }
}








