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
					->addActionContext('edit-show', 'json')
					->addActionContext('move-show', 'json')
					->addActionContext('resize-show', 'json')
					->addActionContext('delete-show', 'json')
                    ->addActionContext('show-content-dialog', 'json')
					->addActionContext('clear-show', 'json')
                    ->addActionContext('get-current-playlist', 'json')
					->addActionContext('remove-group', 'json')
                    ->addActionContext('populate-show-form', 'json')
                    ->addActionContext('populate-show-instance-form', 'json')
                    ->addActionContext('cancel-show', 'json')
                    ->addActionContext('cancel-current-show', 'json')
                    ->addActionContext('get-form', 'json')
                    ->addActionContext('upload-to-sound-cloud', 'json')
                    ->addActionContext('content-context-menu', 'json')
                    ->addActionContext('set-time-scale', 'json')
                    ->addActionContext('set-time-interval', 'json')
                    ->addActionContext('edit-show-instance', 'json')
                    ->addActionContext('dj-edit-show', 'json')
                    ->addActionContext('edit-show-rebroadcast', 'json')
                    ->initContext();

		$this->sched_sess = new Zend_Session_Namespace("schedule");
    }

    public function indexAction()
    {
        global $CC_CONFIG;

        $request = $this->getRequest();
        $baseUrl = $request->getBaseUrl();

        $this->view->headScript()->appendFile($baseUrl.'/js/contextmenu/jquery.contextMenu.js?'.$CC_CONFIG['airtime_version'],'text/javascript');

        //full-calendar-functions.js requires this variable, so that datePicker widget can be offset to server time instead of client time
        $this->view->headScript()->appendScript("var timezoneOffset = ".date("Z")."; //in seconds");
        $this->view->headScript()->appendFile($baseUrl.'/js/airtime/schedule/full-calendar-functions.js?'.$CC_CONFIG['airtime_version'],'text/javascript');

        $this->view->headScript()->appendFile($baseUrl.'/js/fullcalendar/fullcalendar.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/timepicker/jquery.ui.timepicker.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/colorpicker/js/colorpicker.js?'.$CC_CONFIG['airtime_version'],'text/javascript');

        $this->view->headScript()->appendFile($baseUrl.'/js/airtime/schedule/add-show.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/airtime/schedule/schedule.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/blockui/jquery.blockUI.js?'.$CC_CONFIG['airtime_version'],'text/javascript');

        $this->view->headLink()->appendStylesheet($baseUrl.'/css/jquery.ui.timepicker.css?'.$CC_CONFIG['airtime_version']);
        $this->view->headLink()->appendStylesheet($baseUrl.'/css/fullcalendar.css?'.$CC_CONFIG['airtime_version']);
        $this->view->headLink()->appendStylesheet($baseUrl.'/css/colorpicker/css/colorpicker.css?'.$CC_CONFIG['airtime_version']);
        $this->view->headLink()->appendStylesheet($baseUrl.'/css/add-show.css?'.$CC_CONFIG['airtime_version']);
        $this->view->headLink()->appendStylesheet($baseUrl.'/css/jquery.contextMenu.css?'.$CC_CONFIG['airtime_version']);

        //Start Show builder JS/CSS requirements
        $this->view->headScript()->appendFile($baseUrl.'/js/contextmenu/jquery.contextMenu.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/datatables/js/jquery.dataTables.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/datatables/plugin/dataTables.pluginAPI.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/datatables/plugin/dataTables.fnSetFilteringDelay.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/datatables/plugin/dataTables.ColVis.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/datatables/plugin/dataTables.ColReorder.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/datatables/plugin/dataTables.FixedColumns.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/datatables/plugin/dataTables.TableTools.js?'.$CC_CONFIG['airtime_version'],'text/javascript');

        $this->view->headScript()->appendFile($baseUrl.'/js/airtime/buttons/buttons.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($this->view->baseUrl('/js/airtime/library/events/library_showbuilder.js?'.$CC_CONFIG['airtime_version']),'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/airtime/library/library.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/airtime/showbuilder/builder.js?'.$CC_CONFIG['airtime_version'],'text/javascript');

        $this->view->headLink()->appendStylesheet($baseUrl.'/css/media_library.css?'.$CC_CONFIG['airtime_version']);
        $this->view->headLink()->appendStylesheet($baseUrl.'/css/jquery.contextMenu.css?'.$CC_CONFIG['airtime_version']);
        $this->view->headLink()->appendStylesheet($baseUrl.'/css/datatables/css/ColVis.css?'.$CC_CONFIG['airtime_version']);
        $this->view->headLink()->appendStylesheet($baseUrl.'/css/datatables/css/ColReorder.css?'.$CC_CONFIG['airtime_version']);
        $this->view->headLink()->appendStylesheet($baseUrl.'/css/TableTools.css?'.$CC_CONFIG['airtime_version']);
        $this->view->headLink()->appendStylesheet($baseUrl.'/css/showbuilder.css?'.$CC_CONFIG['airtime_version']);
        //End Show builder JS/CSS requirements

        Application_Model_Schedule::createNewFormSections($this->view);

        $user = Application_Model_User::GetCurrentUser();
        
        if($user->isUserType(array(UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER))){
            $this->view->preloadShowForm = true;
        }
        
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
        if ($user->isUserType(array(UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER))) {
            $editable = true;
        }
        else {
            $editable = false;
        }

		$this->view->events = Application_Model_Show::getFullCalendarEvents($start, $end, $editable);
    }

    public function moveShowAction()
    {
        $deltaDay = $this->_getParam('day');
        $deltaMin = $this->_getParam('min');
        $showInstanceId = $this->_getParam('showInstanceId');

        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $user = new Application_Model_User($userInfo->id);

        if ($user->isUserType(array(UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER))) {
            try {
                $showInstance = new Application_Model_ShowInstance($showInstanceId);
            } catch (Exception $e){
                $this->view->show_error = true;
                return false;
            }
            $error = $showInstance->moveShow($deltaDay, $deltaMin);
        }

        if (isset($error)) {
            $this->view->error = $error;
        }
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
        $menu = array();
        $epochNow = time();

        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $user = new Application_Model_User($userInfo->id);
        try{
            $instance = new Application_Model_ShowInstance($id);
        }catch(Exception $e){
            $this->view->show_error = true;
            return false;
        }
        
        $isAdminOrPM = $user->isUserType(array(UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER));
        $isDJ = $user->isHost($instance->getShowId());

        $showStartLocalDT = Application_Common_DateHelper::ConvertToLocalDateTime($instance->getShowInstanceStart());
        $showEndLocalDT = Application_Common_DateHelper::ConvertToLocalDateTime($instance->getShowInstanceEnd());

		if ($epochNow < $showStartLocalDT->getTimestamp()) {

            if ( ($isAdminOrPM || $isDJ) 
                && !$instance->isRecorded()
                && !$instance->isRebroadcast()) {

                $menu["schedule"] = array("name"=> "Add / Remove Content", "icon" => "add-remove-content",
                    "url" => "/showbuilder/builder-dialog/");

                $menu["clear"] = array("name"=> "Remove All Content", "icon" => "remove-all-content",
                    "url" => "/schedule/clear-show");
            }

        }

        if (!$instance->isRecorded()) {

            $menu["content"] = array("name"=> "Show Content", "icon" => "overview", "url" => "/schedule/show-content-dialog");
        }

        if ($showEndLocalDT->getTimestamp() <= $epochNow
            && $instance->isRecorded()
            && Application_Model_Preference::GetUploadToSoundcloudOption()) {

                $text = is_null($instance->getSoundCloudFileId()) ? 'Upload to SoundCloud' : 'Re-upload to SoundCloud';
                $menu["soundcloud"] = array("name"=> $text, "icon" => "soundcloud");
        }

        if ($showStartLocalDT->getTimestamp() <= $epochNow &&
                $epochNow < $showEndLocalDT->getTimestamp() &&
                ($isAdminOrPM || $isDJ)) {

            if ($instance->isRecorded()) {
                if($isAdminOrPM){
                    $menu["cancel_recorded"] = array("name"=> "Cancel Current Show", "icon" => "delete");
                }
            } else {
                if($instance->isRepeating()){
                    $menu["edit"] = array("name"=> "Edit", "icon" => "edit", "items" => array());
                    $menu["edit"]["items"]["instance"] = array("name"=> "Edit Show Instance", "icon" => "edit", "url" => "/Schedule/populate-show-instance-form");
                    $menu["edit"]["items"]["all"] = array("name"=> "Edit Show", "icon" => "edit", "url" => "/Schedule/populate-show-form");
                }else{
                    if($instance->isRebroadcast()){
                        $menu["edit"] = array("name"=> "Edit Show", "icon" => "edit", "_type"=>"rebroadcast", "url" => "/Schedule/populate-show-form");
                    }else{
                        $menu["edit"] = array("name"=> "Edit Show", "icon" => "edit", "_type"=>"all", "url" => "/Schedule/populate-show-form");
                    }
                }
                if($isAdminOrPM){
                    $menu["cancel"] = array("name"=> "Cancel Current Show", "icon" => "delete");
                }
            }
        }

        if ($epochNow < $showStartLocalDT->getTimestamp()) {

            if ($isAdminOrPM || $isDJ) {

                if($instance->isRepeating()){
                    $menu["edit"] = array("name"=> "Edit", "icon" => "edit", "items" => array());
                    $menu["edit"]["items"]["instance"] = array("name"=> "Edit Show Instance", "icon" => "edit", "url" => "/Schedule/populate-show-instance-form");
                    $menu["edit"]["items"]["all"] = array("name"=> "Edit Show", "icon" => "edit", "url" => "/Schedule/populate-show-form");
                }else{
                    if($instance->isRebroadcast()){
                        $menu["edit"] = array("name"=> "Edit Show", "icon" => "edit", "_type"=>"rebroadcast", "url" => "/Schedule/populate-show-form");
                    }else{
                        $menu["edit"] = array("name"=> "Edit Show", "icon" => "edit", "_type"=>"all", "url" => "/Schedule/populate-show-form");
                    }
                }

                if ($instance->getShow()->isRepeating() && $isAdminOrPM) {

                    //create delete sub menu.
                    $menu["del"] = array("name"=> "Delete", "icon" => "delete", "items" => array());

                    $menu["del"]["items"]["single"] = array("name"=> "Delete This Instance", "icon" => "delete", "url" => "/schedule/delete-show");

                    $menu["del"]["items"]["following"] = array("name"=> "Delete This Instance and All Following", "icon" => "delete", "url" => "/schedule/cancel-show");
                }
                else if($isAdminOrPM){
                    //window["scheduleRefetchEvents"]'
                   $menu["del"] = array("name"=> "Delete", "icon" => "delete", "url" => "/schedule/delete-show");
                }
            }
        }

        $this->view->items = $menu;
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
            $range["previous"]["starts"] = Application_Common_DateHelper::ConvertToLocalDateTimeString($range["previous"]["starts"]);
            $range["previous"]["ends"] = Application_Common_DateHelper::ConvertToLocalDateTimeString($range["previous"]["ends"]);
        }
        if (isset($range["current"])){
            $range["current"]["starts"] = Application_Common_DateHelper::ConvertToLocalDateTimeString($range["current"]["starts"]);
            $range["current"]["ends"] = Application_Common_DateHelper::ConvertToLocalDateTimeString($range["current"]["ends"]);
        }
        if (isset($range["next"])){
            $range["next"]["starts"] = Application_Common_DateHelper::ConvertToLocalDateTimeString($range["next"]["starts"]);
            $range["next"]["ends"] = Application_Common_DateHelper::ConvertToLocalDateTimeString($range["next"]["ends"]);
        }

        Application_Model_Show::ConvertToLocalTimeZone($range["currentShow"], array("starts", "ends", "start_timestamp", "end_timestamp"));
        Application_Model_Show::ConvertToLocalTimeZone($range["nextShow"], array("starts", "ends", "start_timestamp", "end_timestamp"));
        
        $source_status = array();
        $switch_status = array();
        $live_dj = Application_Model_Preference::GetSourceStatus("live_dj");
        $master_dj = Application_Model_Preference::GetSourceStatus("master_dj");
        
        $scheduled_play_switch = Application_Model_Preference::GetSourceSwitchStatus("scheduled_play");
        $live_dj_switch = Application_Model_Preference::GetSourceSwitchStatus("live_dj");
        $master_dj_switch = Application_Model_Preference::GetSourceSwitchStatus("master_dj");
        
        //might not be the correct place to implement this but for now let's just do it here
        $source_status['live_dj_source'] = $live_dj;
        $source_status['master_dj_source'] = $master_dj;
        $this->view->source_status = $source_status;
        
        $switch_status['live_dj_source'] = $live_dj_switch;
        $switch_status['master_dj_source'] = $master_dj_switch;
        $switch_status['scheduled_play'] = $scheduled_play_switch;
        $this->view->switch_status = $switch_status;
        
        $this->view->entries = $range;
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
            //$timestamp  = Application_Common_DateHelper::ConvertToLocalDateTimeString($originalDateTime->format("Y-m-d H:i:s"));
            $this->view->additionalShowInfo =
                "Rebroadcast of show \"$originalShowName\" from "
                .$originalDateTime->format("l, F jS")." at ".$originalDateTime->format("G:i");
        }
        $this->view->showLength = $show->getShowLength();
        $this->view->timeFilled = $show->getTimeScheduled();
        $this->view->percentFilled = $show->getPercentScheduled();
        $this->view->showContent = $show->getShowListContent();
        $this->view->dialog = $this->view->render('schedule/show-content-dialog.phtml');
        $this->view->showTitle = $show->getName();
        unset($this->view->showContent);
    }

    public function populateShowInstanceFormAction(){
        $formWhat = new Application_Form_AddShowWhat();
		$formWho = new Application_Form_AddShowWho();
		$formWhen = new Application_Form_AddShowWhen();
		$formRepeats = new Application_Form_AddShowRepeats();
		$formStyle = new Application_Form_AddShowStyle();
		$formLive = new Application_Form_AddShowLiveStream();

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
	    $this->view->live = $formLive;
        $this->view->addNewShow = false;
        
        $showInstanceId = $this->_getParam('id');
                
        $show_instance = CcShowInstancesQuery::create()->findPK($showInstanceId);
        $show = new Application_Model_Show($show_instance->getDbShowId());
                
        $starts_string = $show_instance->getDbStarts();
        $ends_string = $show_instance->getDbEnds();
        
        $starts_datetime = new DateTime($starts_string, new DateTimeZone("UTC"));
        $ends_datetime = new DateTime($ends_string, new DateTimeZone("UTC"));
        
        $starts_datetime->setTimezone(new DateTimeZone(date_default_timezone_get()));
        $ends_datetime->setTimezone(new DateTimeZone(date_default_timezone_get()));

        $instance_duration = $starts_datetime->diff($ends_datetime);  

        $formWhat->populate(array('add_show_id' => $show->getId(),
                    'add_show_instance_id' => $showInstanceId,
                    'add_show_name' => $show->getName(),
                    'add_show_url' => $show->getUrl(),
                    'add_show_genre' => $show->getGenre(),
                    'add_show_description' => $show->getDescription()));        
        
        $formWhen->populate(array('add_show_start_date' => $starts_datetime->format("Y-m-d"),
                                  'add_show_start_time' => $starts_datetime->format("H:i"),
        						  'add_show_end_date_no_repeat' => $ends_datetime->format("Y-m-d"),
        						  'add_show_end_time'	=> $ends_datetime->format("H:i"),
                                  'add_show_duration' => $instance_duration->format("%h")));

        $formWhat->disable();
        $formWho->disable();
        $formWhen->disableRepeatCheckbox();
        $formRepeats->disable();
        $formStyle->disable();
        
        /*
        $formRecord->disable();
        $formAbsoluteRebroadcast->disable();
        $formRebroadcast->disable();
        */
        
        $this->view->action = "edit-show-instance";
        $this->view->newForm = $this->view->render('schedule/add-show-form.phtml');
    }

    public function populateShowFormAction()
    {
        $userInfo = Zend_Auth::getInstance()->getStorage()->read();
        $user = new Application_Model_User($userInfo->id);

        $isSaas = Application_Model_Preference::GetPlanLevel() == 'disabled'?false:true;

        $showInstanceId = $this->_getParam('id');
        // $type is used to determine if this edit is for the specific instance or for all
        // repeating shows. It's value is either "instance","rebroadcast", or "all"
        $type = $this->_getParam('type');
        
        if($type == "rebroadcast") {
            $this->view->action = "edit-show-rebroadcast";
        } else {
            $this->view->action = "edit-show";
        }
        
        try{
            $showInstance = new Application_Model_ShowInstance($showInstanceId);
        }catch(Exception $e){
            $this->view->show_error = true;
            return false;
        }
        
        $isAdminOrPM = $user->isUserType(array(UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER));
        $isDJ = $user->isHost($showInstance->getShowId());
        
        if(!($isAdminOrPM || $isDJ)) {
            return;
        }
        
        if($isDJ){
            $this->view->action = "dj-edit-show";
        }

        $formWhat = new Application_Form_AddShowWhat();
		$formWho = new Application_Form_AddShowWho();
		$formWhen = new Application_Form_AddShowWhen();
		$formRepeats = new Application_Form_AddShowRepeats();
		$formStyle = new Application_Form_AddShowStyle();
		$formLive = new Application_Form_AddShowLiveStream();

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
	    $this->view->live = $formLive;
        $this->view->addNewShow = false;

        $show = new Application_Model_Show($showInstance->getShowId());

        $formWhat->populate(array('add_show_id' => $show->getId(),
                    'add_show_instance_id' => $showInstanceId,
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
            // for a non-repeating show, we should never allow user to change the start time.
            // for the repeating show, we should allow because the form works as repeating template form
            if(!$showInstance->getShow()->isRepeating()){
                $formWhen->disableStartDateAndTime();
            }else{
                $formWhen->getElement('add_show_start_date')->setOptions(array('disabled' => true));
            }
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
        
        $formLive->populate($show->getLiveStreamInfo());

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
                $rebroadcastFormValues["add_show_rebroadcast_time_$i"] = Application_Common_DateHelper::removeSecondsFromTime($rebroadcast['start_time']);
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
            if(!$isAdminOrPM){
                $formRecord->disable();
                $formAbsoluteRebroadcast->disable();
                $formRebroadcast->disable();
            }
        }
        
        if(!$isAdminOrPM){
            $formWhat->disable();
            $formWho->disable();
            $formWhen->disable();
            $formRepeats->disable();
            $formStyle->disable();
        }
        
        if($type == "rebroadcast"){
            $formWhen->disable();
        }

        $this->view->newForm = $this->view->render('schedule/add-show-form.phtml');
        $this->view->entries = 5;
    }

    public function getFormAction() {
        
        $user = Application_Model_User::GetCurrentUser();
        
        if($user->isUserType(array(UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER))){
            Application_Model_Schedule::createNewFormSections($this->view);
            $this->view->form = $this->view->render('schedule/add-show-form.phtml');
        }
    }
        
    public function editShowRebroadcastAction(){
        $js = $this->_getParam('data');
        $data = array();

        //need to convert from serialized jQuery array.
        foreach($js as $j){
            $data[$j["name"]] = $j["value"];
        }
        
    }
    
    public function djEditShowAction(){
        $js = $this->_getParam('data');
        $data = array();

        //need to convert from serialized jQuery array.
        foreach($js as $j){
            $data[$j["name"]] = $j["value"];
        }
        
        //update cc_show
        $show = new Application_Model_Show($data["add_show_id"]);
        $show->setAirtimeAuthFlag($data["cb_airtime_auth"]);
        $show->setCustomAuthFlag($data["cb_custom_auth"]);
        $show->setCustomUsername($data["custom_username"]);
        $show->setCustomPassword($data["custom_password"]);
        
        $this->view->edit = true;
    }
    
    public function editShowInstanceAction(){
        $js = $this->_getParam('data');
        $data = array();

        //need to convert from serialized jQuery array.
        foreach($js as $j){
            $data[$j["name"]] = $j["value"];
        }
        
        $success = Application_Model_Schedule::updateShowInstance($data, $this);
        if ($success){
            $this->view->addNewShow = true;
            $this->view->newForm = $this->view->render('schedule/add-show-form.phtml');
        } else {
            $this->view->addNewShow = false;
            $this->view->form = $this->view->render('schedule/add-show-form.phtml');
        }
    }
    
    public function editShowAction(){
        
        //1) Get add_show_start_date since it might not have been sent
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
        
        $show = new Application_Model_Show($data['add_show_id']);
        $validateStartDate = true;
        
        if (!array_key_exists('add_show_start_date', $data)){
            //Changing the start date was disabled, since the
            //array key does not exist. We need to repopulate this entry from the db.
            //The start date will be returned in UTC time, so lets convert it to local time.
            $dt = Application_Common_DateHelper::ConvertToLocalDateTime($show->getStartDate());
            $startTime = Application_Common_DateHelper::ConvertToLocalDateTime($show->getStartTime());
            $data['add_show_start_date'] = $dt->format("Y-m-d");
            $data['add_show_start_time'] = $startTime->format("H:i");
            $validateStartDate = false;
        }
        
        $data['add_show_record'] = $show->isRecorded();
        
        $success = Application_Model_Schedule::addUpdateShow($data, $this, $validateStartDate);
        
        if ($success){
            $this->view->addNewShow = true;
            $this->view->newForm = $this->view->render('schedule/add-show-form.phtml');
        } else {
            if (!$validateStartDate){
                $this->view->when->getElement('add_show_start_date')->setOptions(array('disabled' => true));
            }
            $this->view->rr->getElement('add_show_record')->setOptions(array('disabled' => true));
            $this->view->addNewShow = false;
            $this->view->form = $this->view->render('schedule/add-show-form.phtml');
        }
    }
    
    public function addShowAction(){
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
        
        $validateStartDate = true;
        $success = Application_Model_Schedule::addUpdateShow($data, $this, $validateStartDate);
        
        if ($success){
            $this->view->addNewShow = true;
            $this->view->newForm = $this->view->render('schedule/add-show-form.phtml');
        } else {
            $this->view->addNewShow = true;
            $this->view->form = $this->view->render('schedule/add-show-form.phtml');
        }
    }

    public function cancelShowAction()
    {
        $user = Application_Model_User::GetCurrentUser();

        if ($user->isUserType(array(UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER))) {
		    $showInstanceId = $this->_getParam('id');

		    try {
                $showInstance = new Application_Model_ShowInstance($showInstanceId);
		    } catch(Exception $e) {
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
        $user = Application_Model_User::GetCurrentUser();

        if ($user->isUserType(array(UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER))) {
            $id = $this->_getParam('id');
           
            try {
                $scheduler = new Application_Model_Scheduler();
                $scheduler->cancelShow($id);
            }
            catch (Exception $e) {
                $this->view->error = $e->getMessage();
                Logging::log($e->getMessage());
                Logging::log("{$e->getFile()}");
                Logging::log("{$e->getLine()}");
            }
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








