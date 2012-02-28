<?php

class ApiController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $context = $this->_helper->getHelper('contextSwitch');
        $context->addActionContext('version', 'json')
                ->addActionContext('recorded-shows', 'json')
                ->addActionContext('calendar-init', 'json')
                ->addActionContext('upload-file', 'json')
                ->addActionContext('upload-recorded', 'json')
                ->addActionContext('media-monitor-setup', 'json')
                ->addActionContext('media-item-status', 'json')
                ->addActionContext('reload-metadata', 'json')
                ->addActionContext('list-all-files', 'json')
                ->addActionContext('list-all-watched-dirs', 'json')
                ->addActionContext('add-watched-dir', 'json')
                ->addActionContext('remove-watched-dir', 'json')
                ->addActionContext('set-storage-dir', 'json')
                ->addActionContext('get-stream-setting', 'json')
                ->addActionContext('status', 'json')
                ->addActionContext('register-component', 'json')
                ->addActionContext('update-liquidsoap-status', 'json')
                ->addActionContext('library-init', 'json')
                ->addActionContext('live-chat', 'json')
                ->initContext();
    }

    public function indexAction()
    {
        // action body
    }

    /**
     * Returns Airtime version. i.e "1.7.0-beta"
     *
     * First checks to ensure the correct API key was
     * supplied, then returns AIRTIME_VERSION as defined
     * in the database
     *
     * @return void
     *
     */
    public function versionAction()
    {
        global $CC_CONFIG;

        // disable the view and the layout
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $api_key = $this->_getParam('api_key');
        if (!in_array($api_key, $CC_CONFIG["apiKey"]))
        {
            header('HTTP/1.0 401 Unauthorized');
            print 'You are not allowed to access this resource.';
            exit;
        }
        $jsonStr = json_encode(array("version"=>Application_Model_Preference::GetAirtimeVersion()));
        echo $jsonStr;
    }
    
    /**
     * Sets up and send init values used in the Calendar.
     * This is only being used by schedule.js at the moment.
     */
    public function calendarInitAction(){
    	$this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        if(is_null(Zend_Auth::getInstance()->getStorage()->read())) {
            header('HTTP/1.0 401 Unauthorized');
            print 'You are not allowed to access this resource.';
            return;
        }
        
    	$this->view->calendarInit = array(
        	"timestamp" => time(), 
        	"timezoneOffset" => date("Z"), 
        	"timeScale" => Application_Model_Preference::GetCalendarTimeScale(),
    		"timeInterval" => Application_Model_Preference::GetCalendarTimeInterval(),
    		"weekStartDay" => Application_Model_Preference::GetWeekStartDay()
        );
    }

    /**
     * Allows remote client to download requested media file.
     *
     * @return void
     *
     */
    public function getMediaAction()
    {
        global $CC_CONFIG;

        // disable the view and the layout
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $api_key = $this->_getParam('api_key');
        $download = ("true" == $this->_getParam('download'));

        $logger = Logging::getLogger();

        if(!in_array($api_key, $CC_CONFIG["apiKey"]) &&
            is_null(Zend_Auth::getInstance()->getStorage()->read()))
        {
            header('HTTP/1.0 401 Unauthorized');
            print 'You are not allowed to access this resource.';
            $logger->info("401 Unauthorized");
            return;
        }

        $filename = $this->_getParam("file");
        $file_id = substr($filename, 0, strpos($filename, "."));
        if (ctype_alnum($file_id) && strlen($file_id) == 32) {
          $media = Application_Model_StoredFile::RecallByGunid($file_id);
          if ($media != null && !PEAR::isError($media)) {
            $filepath = $media->getFilePath();
            if(is_file($filepath)){
                // possibly use fileinfo module here in the future.
                // http://www.php.net/manual/en/book.fileinfo.php
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                if ($ext == "ogg")
                    header("Content-Type: audio/ogg");
                else if ($ext == "mp3")
                    header("Content-Type: audio/mpeg");
                if ($download){
                    //path_info breaks up a file path into seperate pieces of informaiton.
                    //We just want the basename which is the file name with the path
                    //information stripped away. We are using Content-Disposition to specify
                    //to the browser what name the file should be saved as.
                    //
                    // By james.moon:
                    // I'm removing pathinfo() since it strips away UTF-8 characters.
                    // Using manualy parsing
                    $full_path = $media->getPropelOrm()->getDbFilepath();
                    $file_base_name = strrchr($full_path, '/');
                    $file_base_name = substr($file_base_name, 1);
                    header('Content-Disposition: attachment; filename="'.$file_base_name.'"');
                }
                $logger->info("Sending $filepath");
                header("Content-Length: " . filesize($filepath));

                // !! binary mode !!
                $fp = fopen($filepath, 'rb');

                //We can have multiple levels of output buffering. Need to
                //keep looping until all have been disabled!!!
                //http://www.php.net/manual/en/function.ob-end-flush.php
                while (@ob_end_flush());

                fpassthru($fp);
                fclose($fp);

                //make sure to exit here so that no other output is sent.
                exit;
            } else {
                $logger->err('Resource in database, but not in storage: "'.$filepath.'"');
            }
          } else {
            $logger->err('$media != null && !PEAR::isError($media)');
          }
      } else {
        $logger->err('ctype_alnum($file_id) && strlen($file_id) == 32');
      }
      header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
      $logger->info("404 Not Found");
      return;
    }

    /**
     * Retrieve the currently playing show as well as upcoming shows.
     * Number of shows returned and the time interval in which to
     * get the next shows can be configured as GET parameters.
     * 
     * TODO: in the future, make interval length a parameter instead of hardcode to 48
     * 
     * Possible parameters:
     * type - Can have values of "endofday" or "interval". If set to "endofday",
     *        the function will retrieve shows from now to end of day.
     *        If set to "interval", shows in the next 48 hours will be retrived.
     *        Default is "interval".
     * limit - How many shows to retrieve
     *         Default is "5".
     */
    public function liveInfoAction()
    {
        if (Application_Model_Preference::GetAllow3rdPartyApi()){
            // disable the view and the layout
            $this->view->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);

            $date = new Application_Model_DateHelper;
            $utcTimeNow = $date->getUtcTimestamp();
            $utcTimeEnd = "";   // if empty, GetNextShows will use interval instead of end of day
            
            $request = $this->getRequest();
            $type = $request->getParam('type');
            if($type == "endofday") {
                // make GetNextShows use end of day
                $utcTimeEnd = Application_Model_DateHelper::GetDayEndTimestampInUtc();
            }
            
            $limit = $request->getParam('limit');
            if($limit == "" || !is_numeric($limit)) {
                $limit = "5";
            }
            
            $result = array("env"=>APPLICATION_ENV,
                "schedulerTime"=>gmdate("Y-m-d H:i:s"),
                "currentShow"=>Application_Model_Show::GetCurrentShow($utcTimeNow),
                "nextShow"=>Application_Model_Show::GetNextShows($utcTimeNow, $limit, $utcTimeEnd),
                "timezone"=> date("T"),
                "timezoneOffset"=> date("Z"),
		"AIRTIME_API_VERSION"=>AIRTIME_API_VERSION); //used by caller to determine if the airtime they are running or widgets in use is out of date.
               
            //Convert from UTC to localtime for user.
            Application_Model_Show::ConvertToLocalTimeZone($result["currentShow"], array("starts", "ends", "start_timestamp", "end_timestamp"));
            Application_Model_Show::ConvertToLocalTimeZone($result["nextShow"], array("starts", "ends", "start_timestamp", "end_timestamp"));

            //echo json_encode($result);
            header("Content-type: text/javascript");
            echo $_GET['callback'].'('.json_encode($result).')';
        } else {
            header('HTTP/1.0 401 Unauthorized');
            print 'You are not allowed to access this resource. ';
            exit;
        }
    }
    
    public function weekInfoAction()
    {
        if (Application_Model_Preference::GetAllow3rdPartyApi()){
            // disable the view and the layout
            $this->view->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);

            $date = new Application_Model_DateHelper;
            $dayStart = $date->getWeekStartDate();
            $utcDayStart = Application_Model_DateHelper::ConvertToUtcDateTimeString($dayStart);
            
            $dow = array("sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday");

            $result = array();
            for ($i=0; $i<7; $i++){
                $utcDayEnd = Application_Model_DateHelper::GetDayEndTimestamp($utcDayStart);
                $shows = Application_Model_Show::GetNextShows($utcDayStart, "0", $utcDayEnd);
                $utcDayStart = $utcDayEnd;
                
                Application_Model_Show::ConvertToLocalTimeZone($shows, array("starts", "ends", "start_timestamp", "end_timestamp"));
                
                $result[$dow[$i]] = $shows;
            }
	    $result['AIRTIME_API_VERSION'] = AIRTIME_API_VERSION; //used by caller to determine if the airtime they are running or widgets in use is out of date.
            header("Content-type: text/javascript");
            echo $_GET['callback'].'('.json_encode($result).')';
        } else {
            header('HTTP/1.0 401 Unauthorized');
            print 'You are not allowed to access this resource. ';
            exit;
        }
    }

    public function scheduleAction()
    {
        global $CC_CONFIG;

        // disable the view and the layout
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $api_key = $this->_getParam('api_key');

        if(!in_array($api_key, $CC_CONFIG["apiKey"]))
        {
            header('HTTP/1.0 401 Unauthorized');
            print 'You are not allowed to access this resource. ';
            exit;
        }

        PEAR::setErrorHandling(PEAR_ERROR_RETURN);

        $result = Application_Model_Schedule::GetScheduledPlaylists();
        echo json_encode($result);
    }

    public function notifyMediaItemStartPlayAction()
    {
        global $CC_CONFIG;

        // disable the view and the layout
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $api_key = $this->_getParam('api_key');
        if(!in_array($api_key, $CC_CONFIG["apiKey"]))
        {
            header('HTTP/1.0 401 Unauthorized');
            print 'You are not allowed to access this resource.';
            exit;
        }

        $schedule_group_id = $this->_getParam("schedule_id");
        $media_id = $this->_getParam("media_id");
        $result = Application_Model_Schedule::UpdateMediaPlayedStatus($media_id);

        if (!PEAR::isError($result)) {
            echo json_encode(array("status"=>1, "message"=>""));
        } else {
            echo json_encode(array("status"=>0, "message"=>"DB Error:".$result->getMessage()));
        }
    }

    public function notifyScheduleGroupPlayAction()
    {
        global $CC_CONFIG;

        // disable the view and the layout
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $api_key = $this->_getParam('api_key');
        if(!in_array($api_key, $CC_CONFIG["apiKey"]))
        {
            header('HTTP/1.0 401 Unauthorized');
            print 'You are not allowed to access this resource.';
            exit;
        }

        PEAR::setErrorHandling(PEAR_ERROR_RETURN);

        $schedule_group_id = $this->_getParam("schedule_id");
        if (is_numeric($schedule_group_id)) {
            $sg = new Application_Model_ScheduleGroup($schedule_group_id);
            if ($sg->exists()) {
                $result = $sg->notifyGroupStartPlay();
                if (!PEAR::isError($result)) {
                    echo json_encode(array("status"=>1, "message"=>""));
                    exit;
                } else {
                    echo json_encode(array("status"=>0, "message"=>"DB Error:".$result->getMessage()));
                    exit;
                }
            } else {
                echo json_encode(array("status"=>0, "message"=>"Schedule group does not exist: ".$schedule_group_id));
                exit;
            }
        } else {
            echo json_encode(array("status"=>0, "message"=>"Incorrect or non-numeric arguments given."));
            exit;
        }
    }

    public function recordedShowsAction()
    {
        global $CC_CONFIG;

        $api_key = $this->_getParam('api_key');
        if (!in_array($api_key, $CC_CONFIG["apiKey"]))
        {
            header('HTTP/1.0 401 Unauthorized');
            print 'You are not allowed to access this resource.';
            exit;
        }

        $today_timestamp = date("Y-m-d H:i:s");
        $now = new DateTime($today_timestamp);
        $end_timestamp = $now->add(new DateInterval("PT2H"));
        $end_timestamp = $end_timestamp->format("Y-m-d H:i:s");
        
        $this->view->shows = Application_Model_Show::getShows(Application_Model_DateHelper::ConvertToUtcDateTime($today_timestamp, date_default_timezone_get()), 
                                                                Application_Model_DateHelper::ConvertToUtcDateTime($end_timestamp, date_default_timezone_get()),
                                                                $excludeInstance=NULL, $onlyRecord=TRUE);


        $this->view->is_recording = false;
        $this->view->server_timezone = Application_Model_Preference::GetTimezone();

        $rows = Application_Model_Show::GetCurrentShow($today_timestamp);
        Application_Model_Show::ConvertToLocalTimeZone($rows, array("starts", "ends", "start_timestamp", "end_timestamp"));

        if (count($rows) > 0){
            $this->view->is_recording = ($rows[0]['record'] == 1);
        }
    }

    public function uploadFileAction()
    {
        global $CC_CONFIG;

        $api_key = $this->_getParam('api_key');
        if (!in_array($api_key, $CC_CONFIG["apiKey"]))
        {
            header('HTTP/1.0 401 Unauthorized');
            print 'You are not allowed to access this resource.';
            exit;
        }

        $upload_dir = ini_get("upload_tmp_dir");
        $tempFilePath = Application_Model_StoredFile::uploadFile($upload_dir);
        $tempFileName = basename($tempFilePath);
        
        $fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';
        Application_Model_StoredFile::copyFileToStor($upload_dir, $fileName, $tempFileName);
    }

    public function uploadRecordedAction()
    {
        global $CC_CONFIG;

        $api_key = $this->_getParam('api_key');
        if (!in_array($api_key, $CC_CONFIG["apiKey"]))
        {
            header('HTTP/1.0 401 Unauthorized');
            print 'You are not allowed to access this resource.';
            exit;
        }

        //this file id is the recording for this show instance.
        $show_instance_id = $this->_getParam('showinstanceid');
        $file_id = $this->_getParam('fileid');

        $this->view->fileid = $file_id;
        $this->view->showinstanceid = $show_instance_id;


       	$showCanceled = false;
       	$file = Application_Model_StoredFile::Recall($file_id);
        //$show_instance  = $this->_getParam('show_instance');

        $show_name = null;
        try {
            $show_inst = new Application_Model_ShowInstance($show_instance_id);

            $show_inst->setRecordedFile($file_id);
            $show_name = $show_inst->getName();
            $show_genre = $show_inst->getGenre();
            $show_start_time = Application_Model_DateHelper::ConvertToLocalDateTimeString($show_inst->getShowInstanceStart());

         } catch (Exception $e){
            //we've reached here probably because the show was
            //cancelled, and therefore the show instance does not
            //exist anymore (ShowInstance constructor threw this error).
            //We've done all we can do (upload the file and put it in
            //the library), now lets just return.
            $showCanceled = true;
        }

        if (isset($show_name)) {
          
            $show_name = str_replace(" ", "-", $show_name);
            
            //2011-12-09-19-28-00-ofirrr-256kbps
            $filename = $file->getName();
            
            //replace the showname in the filepath incase it has been edited since the show started recording
            //(some old bug)
            $filename_parts = explode("-", $filename);
            $new_name = array_slice($filename_parts, 0, 6);
            $new_name[] = $show_name;
            $new_name[] = $filename_parts[count($filename_parts)-1];
            
            $tmpTitle = implode("-", $new_name);
        }
        else {
            $tmpTitle = $file->getName();
        }

		$file->setMetadataValue('MDATA_KEY_TITLE', $tmpTitle);
        $file->setMetadataValue('MDATA_KEY_CREATOR', "Airtime Show Recorder");
        $file->setMetadataValue('MDATA_KEY_TRACKNUMBER', null);

        if (!$showCanceled && Application_Model_Preference::GetAutoUploadRecordedShowToSoundcloud())
        {
        	for ($i=0; $i<$CC_CONFIG['soundcloud-connection-retries']; $i++) {

        		$show = new Application_Model_Show($show_inst->getShowId());
        		$description = $show->getDescription();
        		$hosts = $show->getHosts();

        		$tags = array_merge($hosts, array($show_name));

        		try {
        			$soundcloud = new Application_Model_Soundcloud();
        			$soundcloud_id = $soundcloud->uploadTrack($file->getFilePath(), $tmpTitle, $description, $tags, $show_start_time, $show_genre);
        			$file->setSoundCloudFileId($soundcloud_id);
        			break;
        		}
        		catch (Services_Soundcloud_Invalid_Http_Response_Code_Exception $e) {
        			$code = $e->getHttpCode();
                    $msg = $e->getHttpBody();
                    $temp = explode('"error":',$msg);
                    $msg = trim($temp[1], '"}');
                    $this->setSoundCloudErrorCode($code);
                    $this->setSoundCloudErrorMsg($msg);
                    // setting sc id to -3 which indicates error
                    $this->setSoundCloudFileId(SOUNDCLOUD_ERROR);
        			if(!in_array($code, array(0, 100))) {
        				break;
        			}
        		}

        		sleep($CC_CONFIG['soundcloud-connection-wait']);
        	}
        }

        $this->view->id = $file_id;

    }

    public function mediaMonitorSetupAction() {
        global $CC_CONFIG;

        // disable the view and the layout
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $api_key = $this->_getParam('api_key');
        if (!in_array($api_key, $CC_CONFIG["apiKey"]))
        {
            header('HTTP/1.0 401 Unauthorized');
            print 'You are not allowed to access this resource.';
            exit;
        }

        $this->view->stor = Application_Model_MusicDir::getStorDir()->getDirectory();
        
        $watchedDirs = Application_Model_MusicDir::getWatchedDirs();
        $watchedDirsPath = array();
        foreach($watchedDirs as $wd){
            $watchedDirsPath[] = $wd->getDirectory();
        }
        $this->view->watched_dirs = $watchedDirsPath;
    }

    public function reloadMetadataAction() {
        global $CC_CONFIG;

        $request = $this->getRequest();
        $api_key = $request->getParam('api_key');
        if (!in_array($api_key, $CC_CONFIG["apiKey"]))
        {
            header('HTTP/1.0 401 Unauthorized');
            print 'You are not allowed to access this resource.';
            exit;
        }

        $mode = $request->getParam('mode');
        $params = $request->getParams();

        $md = array();
        //extract all file metadata params from the request.
        foreach ($params as $key => $value) {
            if (preg_match('/^MDATA_KEY/', $key)) {
                $md[$key] = $value;
            }
        }

        // update import timestamp
        Application_Model_Preference::SetImportTimestamp();
        
        if ($mode == "create") {
            $filepath = $md['MDATA_KEY_FILEPATH'];
            $filepath = str_replace("\\", "", $filepath);

            $file = Application_Model_StoredFile::RecallByFilepath($filepath);

            if (is_null($file)) {
                $file = Application_Model_StoredFile::Insert($md);
            }
            else {
                $this->view->error = "File already exists in Airtime.";
                return;
            }
        }
        else if ($mode == "modify") {
            $filepath = $md['MDATA_KEY_FILEPATH'];
            $filepath = str_replace("\\", "", $filepath);
            $file = Application_Model_StoredFile::RecallByFilepath($filepath);

            //File is not in database anymore.
            if (is_null($file)) {
                $this->view->error = "File does not exist in Airtime.";
                return;
            }
            //Updating a metadata change.
            else {
                $file->setMetadata($md);
            }
        }
        else if ($mode == "moved") {
            $md5 = $md['MDATA_KEY_MD5'];
            $file = Application_Model_StoredFile::RecallByMd5($md5);

            if (is_null($file)) {
                $this->view->error = "File doesn't exist in Airtime.";
                return;
            }
            else {
                $filepath = $md['MDATA_KEY_FILEPATH'];
                $filepath = str_replace("\\", "", $filepath);
                $file->setFilePath($filepath);
                //$file->setMetadata($md);
            }
        }
        else if ($mode == "delete") {
            $filepath = $md['MDATA_KEY_FILEPATH'];
            $filepath = str_replace("\\", "", $filepath);
            $file = Application_Model_StoredFile::RecallByFilepath($filepath);

            if (is_null($file)) {
                $this->view->error = "File doesn't exist in Airtime.";
                return;
            }
            else {
                $file->delete();
            }
        }
        else if ($mode == "delete_dir") {
            $filepath = $md['MDATA_KEY_FILEPATH'];
            $filepath = str_replace("\\", "", $filepath);
            $files = Application_Model_StoredFile::RecallByPartialFilepath($filepath);

            foreach($files as $file){
                $file->delete();
            }
            return;
        }
        $this->view->id = $file->getId();
    }

    public function listAllFilesAction() {
        global $CC_CONFIG;

        $request = $this->getRequest();
        $api_key = $request->getParam('api_key');
        if (!in_array($api_key, $CC_CONFIG["apiKey"]))
        {
            header('HTTP/1.0 401 Unauthorized');
            print 'You are not allowed to access this resource.';
            exit;
        }
        $dir_id = $request->getParam('dir_id');

        $this->view->files = Application_Model_StoredFile::listAllFiles($dir_id);
    }

    public function listAllWatchedDirsAction() {
        global $CC_CONFIG;

        $request = $this->getRequest();
        $api_key = $request->getParam('api_key');
        if (!in_array($api_key, $CC_CONFIG["apiKey"]))
        {
            header('HTTP/1.0 401 Unauthorized');
            print 'You are not allowed to access this resource.';
            exit;
        }

        $result = array();

        $arrWatchedDirs = Application_Model_MusicDir::getWatchedDirs();
        $storDir = Application_Model_MusicDir::getStorDir();

        $result[$storDir->getId()] = $storDir->getDirectory();

        foreach ($arrWatchedDirs as $watchedDir){
            $result[$watchedDir->getId()] = $watchedDir->getDirectory();
        }

        $this->view->dirs = $result;
    }

    public function addWatchedDirAction() {
        global $CC_CONFIG;

        $request = $this->getRequest();
        $api_key = $request->getParam('api_key');
        $path = base64_decode($request->getParam('path'));

        if (!in_array($api_key, $CC_CONFIG["apiKey"]))
        {
            header('HTTP/1.0 401 Unauthorized');
            print 'You are not allowed to access this resource.';
            exit;
        }

        $this->view->msg = Application_Model_MusicDir::addWatchedDir($path);
    }

    public function removeWatchedDirAction() {
        global $CC_CONFIG;

        $request = $this->getRequest();
        $api_key = $request->getParam('api_key');
        $path = base64_decode($request->getParam('path'));

        if (!in_array($api_key, $CC_CONFIG["apiKey"]))
        {
            header('HTTP/1.0 401 Unauthorized');
            print 'You are not allowed to access this resource.';
            exit;
        }

        $this->view->msg = Application_Model_MusicDir::removeWatchedDir($path);
    }

    public function setStorageDirAction() {
        global $CC_CONFIG;

        $request = $this->getRequest();
        $api_key = $request->getParam('api_key');
        $path = base64_decode($request->getParam('path'));

        if (!in_array($api_key, $CC_CONFIG["apiKey"]))
        {
            header('HTTP/1.0 401 Unauthorized');
            print 'You are not allowed to access this resource.';
            exit;
        }

        $this->view->msg = Application_Model_MusicDir::setStorDir($path);
    }
    
    public function getStreamSettingAction() {
        global $CC_CONFIG;
        
        $request = $this->getRequest();
        $api_key = $request->getParam('api_key');
        if (!in_array($api_key, $CC_CONFIG["apiKey"]))
        {
            header('HTTP/1.0 401 Unauthorized');
            print 'You are not allowed to access this resource.';
            exit;
        }

        $this->view->msg = Application_Model_StreamSetting::getStreamSetting();
    }
    
    public function statusAction() {
        global $CC_CONFIG;
        
        $request = $this->getRequest();
        $api_key = $request->getParam('api_key');
        $getDiskInfo = $request->getParam('diskinfo') == "true";
        /*
        if (!in_array($api_key, $CC_CONFIG["apiKey"]))
        {
            header('HTTP/1.0 401 Unauthorized');
            print 'You are not allowed to access this resource.';
            exit;
        }
        */
        
        $status = array(
            "platform"=>Application_Model_Systemstatus::GetPlatformInfo(),
            "airtime_version"=>Application_Model_Preference::GetAirtimeVersion(),
            "services"=>array(
                "rabbitmq"=>Application_Model_Systemstatus::GetRabbitMqStatus(),
                "pypo"=>Application_Model_Systemstatus::GetPypoStatus(),
                "liquidsoap"=>Application_Model_Systemstatus::GetLiquidsoapStatus(),
                "media_monitor"=>Application_Model_Systemstatus::GetMediaMonitorStatus()
            )
        );
        
        if ($getDiskInfo){
            $status["partitions"] = Application_Model_Systemstatus::GetDiskInfo();
        }
        
        $this->view->status = $status;
    }

    public function registerComponentAction(){
        $request = $this->getRequest();

        $component = $request->getParam('component');
        $remoteAddr = $_SERVER['REMOTE_ADDR'];
        Logging::log("Registered Component: ".$component."@".$remoteAddr);

        Application_Model_ServiceRegister::Register($component, $remoteAddr);
    }
    
    public function updateLiquidsoapStatusAction(){
        $request = $this->getRequest();
        
        $msg = $request->getParam('msg');
        $stream_id = $request->getParam('stream_id');
        $boot_time = $request->getParam('boot_time');
        
        Application_Model_StreamSetting::setLiquidsoapError($stream_id, $msg, $boot_time);
    }
    
    /**
     * Sets up and send init values used in the Library.
     * This is being used by library.js
     */
    public function libraryInitAction(){
    	$this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        if(is_null(Zend_Auth::getInstance()->getStorage()->read())) {
            header('HTTP/1.0 401 Unauthorized');
            print 'You are not allowed to access this resource.';
            return;
        }
        
    	$this->view->libraryInit = array(
        	"numEntries"=>Application_Model_Preference::GetLibraryNumEntries()
        );
    }
}

