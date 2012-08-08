<?php

class ApiController extends Zend_Controller_Action
{

    public function init()
    {
        $this->checkAuth();
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
                ->addActionContext('live-chat', 'json')
                ->addActionContext('update-file-system-mount', 'json')
                ->addActionContext('handle-watched-dir-missing', 'json')
                ->addActionContext('rabbitmq-do-push', 'json')
                ->addActionContext('check-live-stream-auth', 'json')
                ->addActionContext('update-source-status', 'json')
                ->addActionContext('get-bootstrap-info', 'json')
                ->addActionContext('get-files-without-replay-gain', 'json')
                ->addActionContext('reload-metadata-group', 'json')
                ->initContext();
    }

    public function checkAuth()
    {
        global $CC_CONFIG;

        $api_key = $this->_getParam('api_key');

        if (!in_array($api_key, $CC_CONFIG["apiKey"]) &&
            is_null(Zend_Auth::getInstance()->getStorage()->read())) {
            header('HTTP/1.0 401 Unauthorized');
            print 'You are not allowed to access this resource.';
            exit;
        }
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
        // disable the view and the layout
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $jsonStr = json_encode(array("version"=>Application_Model_Preference::GetAirtimeVersion()));
        echo $jsonStr;
    }

    /**
     * Sets up and send init values used in the Calendar.
     * This is only being used by schedule.js at the moment.
     */
    public function calendarInitAction()
    {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if (is_null(Zend_Auth::getInstance()->getStorage()->read())) {
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
        // disable the view and the layout
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $fileId = $this->_getParam("file");

        $media = Application_Model_StoredFile::Recall($fileId);
        if ($media != null) {

            $filepath = $media->getFilePath();
            if (is_file($filepath)) {
                $full_path = $media->getPropelOrm()->getDbFilepath();

                $file_base_name = strrchr($full_path, '/');
                /* If $full_path does not contain a '/', strrchr will return false,
                 * in which case we can use $full_path as the base name.
                 */
                if (!$file_base_name) {
                    $file_base_name = $full_path;
                } else {
                    $file_base_name = substr($file_base_name, 1);
                }

                //Download user left clicks a track and selects Download.
                if ("true" == $this->_getParam('download')) {
                    //path_info breaks up a file path into seperate pieces of informaiton.
                    //We just want the basename which is the file name with the path
                    //information stripped away. We are using Content-Disposition to specify
                    //to the browser what name the file should be saved as.
                    header('Content-Disposition: attachment; filename="'.$file_base_name.'"');
                } else {
                    //user clicks play button for track and downloads it.
                    header('Content-Disposition: inline; filename="'.$file_base_name.'"');
                }

                $this->smartReadFile($filepath, $media->getPropelOrm()->getDbMime());
                exit;
            } else {
                header ("HTTP/1.1 404 Not Found");
            }
        }

        return;
    }

    /**
    * Reads the requested portion of a file and sends its contents to the client with the appropriate headers.
    *
    * This HTTP_RANGE compatible read file function is necessary for allowing streaming media to be skipped around in.
    *
    * @param string $location
    * @param string $mimeType
    * @return void
    *
    * @link https://groups.google.com/d/msg/jplayer/nSM2UmnSKKA/Hu76jDZS4xcJ
    * @link http://php.net/manual/en/function.readfile.php#86244
    */
    public function smartReadFile($location, $mimeType = 'audio/mp3')
    {
        $size= filesize($location);
        $time= date('r', filemtime($location));

        $fm = @fopen($location, 'rb');
        if (!$fm) {
            header ("HTTP/1.1 505 Internal server error");

            return;
        }

        $begin= 0;
        $end= $size - 1;

        if (isset($_SERVER['HTTP_RANGE'])) {
            if (preg_match('/bytes=\h*(\d+)-(\d*)[\D.*]?/i', $_SERVER['HTTP_RANGE'], $matches)) {
                $begin = intval($matches[1]);
                if (!empty($matches[2])) {
                    $end = intval($matches[2]);
                }
            }
        }

        if (isset($_SERVER['HTTP_RANGE'])) {
            header('HTTP/1.1 206 Partial Content');
        } else {
            header('HTTP/1.1 200 OK');
        }
        header("Content-Type: $mimeType");
        header('Cache-Control: public, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Accept-Ranges: bytes');
        header('Content-Length:' . (($end - $begin) + 1));
        if (isset($_SERVER['HTTP_RANGE'])) {
            header("Content-Range: bytes $begin-$end/$size");
        }
        header("Content-Transfer-Encoding: binary");
        header("Last-Modified: $time");

        //We can have multiple levels of output buffering. Need to
        //keep looping until all have been disabled!!!
        //http://www.php.net/manual/en/function.ob-end-flush.php
        while (@ob_end_flush());

        $cur = $begin;
        fseek($fm, $begin, 0);

        while (!feof($fm) && $cur <= $end && (connection_status() == 0)) {
            echo  fread($fm, min(1024 * 16, ($end - $cur) + 1));
            $cur += 1024 * 16;
        }
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
        if (Application_Model_Preference::GetAllow3rdPartyApi()) {
            // disable the view and the layout
            $this->view->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);

            $date = new Application_Common_DateHelper;
            $utcTimeNow = $date->getUtcTimestamp();
            $utcTimeEnd = "";   // if empty, getNextShows will use interval instead of end of day

            $request = $this->getRequest();
            $type = $request->getParam('type');
            /* This is some *extremely* lazy programming that needs to bi fixed. For some reason
             * we are using two entirely different codepaths for very similar functionality (type = endofday
             * vs type = interval). Needs to be fixed for 2.2 - MK */
            if ($type == "endofday") {
                $limit = $request->getParam('limit');
                if ($limit == "" || !is_numeric($limit)) {
                    $limit = "5";
                }
                
                // make getNextShows use end of day
                $utcTimeEnd = Application_Common_DateHelper::GetDayEndTimestampInUtc();
                $result = array("env"=>APPLICATION_ENV,
                                "schedulerTime"=>gmdate("Y-m-d H:i:s"),
                                "currentShow"=>Application_Model_Show::getCurrentShow($utcTimeNow),
                                "nextShow"=>Application_Model_Show::getNextShows($utcTimeNow, $limit, $utcTimeEnd)
                            );
                
                Application_Model_Show::convertToLocalTimeZone($result["currentShow"],
                        array("starts", "ends", "start_timestamp", "end_timestamp"));
                Application_Model_Show::convertToLocalTimeZone($result["nextShow"],
                        array("starts", "ends", "start_timestamp", "end_timestamp"));
            } else {
                $result = Application_Model_Schedule::GetPlayOrderRange();

                //Convert from UTC to localtime for Web Browser.
                Application_Model_Show::ConvertToLocalTimeZone($result["currentShow"],
                        array("starts", "ends", "start_timestamp", "end_timestamp"));
                Application_Model_Show::ConvertToLocalTimeZone($result["nextShow"],
                        array("starts", "ends", "start_timestamp", "end_timestamp"));
            }

            //used by caller to determine if the airtime they are running or widgets in use is out of date.
            $result['AIRTIME_API_VERSION'] = AIRTIME_API_VERSION;
            header("Content-type: text/javascript");
            
            // If a callback is not given, then just provide the raw JSON.
            echo isset($_GET['callback']) ? $_GET['callback'].'('.json_encode($result).')' : json_encode($result);
        } else {
            header('HTTP/1.0 401 Unauthorized');
            print 'You are not allowed to access this resource. ';
            exit;
        }
    }

    public function weekInfoAction()
    {
        if (Application_Model_Preference::GetAllow3rdPartyApi()) {
            // disable the view and the layout
            $this->view->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);

            $date = new Application_Common_DateHelper;
            $dayStart = $date->getWeekStartDate();
            $utcDayStart = Application_Common_DateHelper::ConvertToUtcDateTimeString($dayStart);

            $dow = array("monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday");

            $result = array();
            for ($i=0; $i<7; $i++) {
                $utcDayEnd = Application_Common_DateHelper::GetDayEndTimestamp($utcDayStart);
                $shows = Application_Model_Show::getNextShows($utcDayStart, "0", $utcDayEnd);
                $utcDayStart = $utcDayEnd;

                Application_Model_Show::convertToLocalTimeZone($shows, array("starts", "ends", "start_timestamp", "end_timestamp"));

                $result[$dow[$i]] = $shows;
            }
        $result['AIRTIME_API_VERSION'] = AIRTIME_API_VERSION; //used by caller to determine if the airtime they are running or widgets in use is out of date.
            header("Content-type: text/javascript");
            Logging::log($result);
            // If a callback is not given, then just provide the raw JSON.
            echo isset($_GET['callback']) ? $_GET['callback'].'('.json_encode($result).')' : json_encode($result);
        } else {
            header('HTTP/1.0 401 Unauthorized');
            print 'You are not allowed to access this resource. ';
            exit;
        }
    }

    public function scheduleAction()
    {
        // disable the view and the layout
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $data = Application_Model_Schedule::GetScheduledPlaylists();
        echo json_encode($data, JSON_FORCE_OBJECT);
    }

    public function notifyMediaItemStartPlayAction()
    {
        // disable the view and the layout
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $schedule_group_id = $this->_getParam("schedule_id");
        $media_id = $this->_getParam("media_id");
        $result = Application_Model_Schedule::UpdateMediaPlayedStatus($media_id);
        
        //set a 'last played' timestamp for media item
        //needed for smart playlists
        try{
            $file_id = Application_Model_Schedule::GetFileId($media_id);
            $file = Application_Model_StoredFile::Recall($file_id);
            $now = new DateTime("now", new DateTimeZone("UTC"));
            $file->setLastPlayedTime($now);
        }catch(Exception $e){
            Logging::log($e);
        }
        
        echo json_encode(array("status"=>1, "message"=>""));
    }

    public function recordedShowsAction()
    {
        $today_timestamp = date("Y-m-d H:i:s");
        $now = new DateTime($today_timestamp);
        $end_timestamp = $now->add(new DateInterval("PT2H"));
        $end_timestamp = $end_timestamp->format("Y-m-d H:i:s");

        $this->view->shows = Application_Model_Show::getShows(Application_Common_DateHelper::ConvertToUtcDateTime($today_timestamp, date_default_timezone_get()),
                                                                Application_Common_DateHelper::ConvertToUtcDateTime($end_timestamp, date_default_timezone_get()),
                                                                $excludeInstance=NULL, $onlyRecord=TRUE);


        $this->view->is_recording = false;
        $this->view->server_timezone = Application_Model_Preference::GetTimezone();

        $rows = Application_Model_Show::GetCurrentShow($today_timestamp);
        Application_Model_Show::convertToLocalTimeZone($rows, array("starts", "ends", "start_timestamp", "end_timestamp"));

        if (count($rows) > 0) {
            $this->view->is_recording = ($rows[0]['record'] == 1);
        }
    }

    public function uploadFileAction()
    {
        $upload_dir = ini_get("upload_tmp_dir");
        $tempFilePath = Application_Model_StoredFile::uploadFile($upload_dir);
        $tempFileName = basename($tempFilePath);

        $fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';
        $result = Application_Model_StoredFile::copyFileToStor($upload_dir, $fileName, $tempFileName);

        if (!is_null($result)) {
            die('{"jsonrpc" : "2.0", "error" : {"code": '.$result[code].', "message" : "'.$result[message].'"}}');
        }
    }

    public function uploadRecordedAction() {
        $show_instance_id = $this->_getParam('showinstanceid');
        $file_id = $this->_getParam('fileid');
        $this->view->fileid = $file_id;
        $this->view->showinstanceid = $show_instance_id;
        $this->uploadRecordedActionParam($show_instance_id, $file_id);
    }

    // The paramterized version of the uploadRecordedAction controller. We want this controller's action
    // to be invokable from other controllers instead being of only through http
    public function uploadRecordedActionParam($show_instance_id, $file_id)
    {
        $showCanceled = false;
        $file = Application_Model_StoredFile::Recall($file_id);
        //$show_instance  = $this->_getParam('show_instance');

        $show_name = null;
        try {
            $show_inst = new Application_Model_ShowInstance($show_instance_id);
            $show_inst->setRecordedFile($file_id);
            $show_name = $show_inst->getName();
            $show_genre = $show_inst->getGenre();
            $show_start_time = Application_Common_DateHelper::ConvertToLocalDateTimeString($show_inst->getShowInstanceStart());

         } catch (Exception $e) {
            //we've reached here probably because the show was
            //cancelled, and therefore the show instance does not
            //exist anymore (ShowInstance constructor threw this error).
            //We've done all we can do (upload the file and put it in
            //the library), now lets just return.
            $showCanceled = true;
        }

        $file->setMetadataValue('MDATA_KEY_CREATOR', "Airtime Show Recorder");
        $file->setMetadataValue('MDATA_KEY_TRACKNUMBER', $show_instance_id);

        if (!$showCanceled && Application_Model_Preference::GetAutoUploadRecordedShowToSoundcloud()) {
            $id = $file->getId();
            $res = exec("/usr/lib/airtime/utils/soundcloud-uploader $id > /dev/null &");
        }
    }

    public function mediaMonitorSetupAction()
    {
        // disable the view and the layout
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $this->view->stor = Application_Model_MusicDir::getStorDir()->getDirectory();

        $watchedDirs = Application_Model_MusicDir::getWatchedDirs();
        $watchedDirsPath = array();
        foreach ($watchedDirs as $wd) {
            $watchedDirsPath[] = $wd->getDirectory();
        }
        $this->view->watched_dirs = $watchedDirsPath;
    }

    public function dispatchMetadataAction($md, $mode, $dry_run=false)
    {
        // Replace this compound result in a hash with proper error handling later on
        $return_hash = array();
        if ( $dry_run ) { // for debugging we return garbage not to screw around with the db
            return array(
                'md' => $md,
                'mode' => $mode,
                'fileid' => 123456
            );
        }
        Application_Model_Preference::SetImportTimestamp();
        Logging::log("--->Mode: $mode || file: {$md['MDATA_KEY_FILEPATH']} ");
        Logging::log( $md );
        if ($mode == "create") {
            $filepath = $md['MDATA_KEY_FILEPATH'];
            $filepath = Application_Common_OsPath::normpath($filepath);
            $file = Application_Model_StoredFile::RecallByFilepath($filepath);
            if (is_null($file)) {
                $file = Application_Model_StoredFile::Insert($md);
            } else {
                // path already exist
                if ($file->getFileExistsFlag()) {
                    // file marked as exists
                    $return_hash['error'] =  "File already exists in Airtime.";
                    return $return_hash;
                } else {
                    // file marked as not exists
                    $file->setFileExistsFlag(true);
                    $file->setMetadata($md);
                }
            }
        }
        else if ($mode == "modify") {
            $filepath = $md['MDATA_KEY_FILEPATH'];
            $file = Application_Model_StoredFile::RecallByFilepath($filepath);

            //File is not in database anymore.
            if (is_null($file)) {
                $return_hash['error'] = "File does not exist in Airtime.";
                return $return_hash;
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
                return "File doesn't exist in Airtime.";
            }
            else {
                $filepath = $md['MDATA_KEY_FILEPATH'];
                //$filepath = str_replace("\\", "", $filepath);
                $file->setFilePath($filepath);
            }
        }
        else if ($mode == "delete") {
            $filepath = $md['MDATA_KEY_FILEPATH'];
            //$filepath = str_replace("\\", "", $filepath);
            $file = Application_Model_StoredFile::RecallByFilepath($filepath);

            if (is_null($file)) {
                $return_hash['error'] =  "File doesn't exist in Airtime.";
                return $return_hash;
            }
            else {
                $file->deleteByMediaMonitor();
            }
        }
        else if ($mode == "delete_dir") {
            $filepath = $md['MDATA_KEY_FILEPATH'];
            //$filepath = str_replace("\\", "", $filepath);
            $files = Application_Model_StoredFile::RecallByPartialFilepath($filepath);

            foreach($files as $file){
                $file->deleteByMediaMonitor();
            }
            $return_hash['success'] = 1;
            return $return_hash;
        }
        $return_hash['fileid'] = $file->getId();
        return $return_hash;
    }

    public function reloadMetadataGroupAction()
    {
        $request = $this->getRequest();
        // extract all file metadata params from the request.
        // The value is a json encoded hash that has all the information related to this action
        // The key(mdXXX) does not have any meaning as of yet but it could potentially correspond
        // to some unique id.
        $responses = array();
        $dry = $request->getParam('dry') || false;
        $params = $request->getParams();
        $valid_modes = array('delete_dir', 'delete', 'moved', 'modify', 'create');
        foreach ($request->getParams() as $k => $raw_json) {
            // Valid requests must start with mdXXX where XXX represents at least 1 digit
            if( !preg_match('/^md\d+$/', $k) ) { continue; }
            $info_json = json_decode($raw_json, $assoc=true);
            $recorded = $info_json["is_record"];
            unset( $info_json["is_record"] );
            //unset( $info_json["MDATA_KEY_DURATION"] );
            //unset( $info_json["MDATA_KEY_SAMPLERATE"] );
            //unset( $info_json["MDATA_KEY_BITRATE"] );

            if( !array_key_exists('mode', $info_json) ) { // Log invalid requests
                Logging::log("Received bad request(key=$k), no 'mode' parameter. Bad request is:");
                Logging::log( $info_json );
                array_push( $responses, array(
                    'error' => "Bad request. no 'mode' parameter passed.",
                    'key' => $k));
                continue;
            } elseif ( !in_array($info_json['mode'], $valid_modes) )  {
                // A request still has a chance of being invalid even if it exists but it's validated
                // by $valid_modes array
                $mode = $info_json['mode'];
                Logging::log("Received bad request(key=$k). 'mode' parameter was invalid with value: '$mode'. Request:");
                Logging::log( $info_json );
                array_push( $responses, array(
                    'error' => "Bad request. 'mode' parameter is invalid",
                    'key' => $k,
                    'mode' => $mode ) );
                continue;
            }
            // Removing 'mode' key from $info_json might not be necessary...
            $mode = $info_json['mode'];
            unset( $info_json['mode'] );
            $response = $this->dispatchMetadataAction($info_json, $mode, $dry_run=$dry);
            // We tack on the 'key' back to every request in case the would like to associate
            // his requests with particular responses
            $response['key'] = $k;
            array_push($responses, $response);
            // On recorded show requests we do some extra work here. Not sure what it actually is and it
            // was usually called from the python api client. Now we just call it straight from the controller to
            // save the http roundtrip
        }
        die( json_encode($responses) );
    }

    public function reloadMetadataAction()
    {
        $request = $this->getRequest();

        $mode = $request->getParam('mode');
        $params = $request->getParams();

        $md = array();
        //extract all file metadata params from the request.
        foreach ($params as $key => $value) {
            if (preg_match('/^MDATA_KEY/', $key)) {
                $md[$key] = $value;
            }
        }

        Logging::log( $md );

        // update import timestamp
        Application_Model_Preference::SetImportTimestamp();
        if ($mode == "create") {
            $filepath = $md['MDATA_KEY_FILEPATH'];
            //$filepath = str_replace("\\", "", $filepath);
            //$filepath = str_replace("//", "/", $filepath);
            $filepath = Application_Common_OsPath::normpath($filepath);

            $file = Application_Model_StoredFile::RecallByFilepath($filepath);
            if (is_null($file)) {
                $file = Application_Model_StoredFile::Insert($md);
            } else {
                // path already exist
                if ($file->getFileExistsFlag()) {
                    // file marked as exists
                    $this->view->error = "File already exists in Airtime.";

                    return;
                } else {
                    // file marked as not exists
                    $file->setFileExistsFlag(true);
                    $file->setMetadata($md);
                }
            }
        } else if ($mode == "modify") {
            $filepath = $md['MDATA_KEY_FILEPATH'];
            //$filepath = str_replace("\\", "", $filepath);
            $file = Application_Model_StoredFile::RecallByFilepath($filepath);

            //File is not in database anymore.
            if (is_null($file)) {
                $this->view->error = "File does not exist in Airtime.";

                return;
            } else {
                //Updating a metadata change.
                $file->setMetadata($md);
            }
        } else if ($mode == "moved") {
            $md5 = $md['MDATA_KEY_MD5'];
            $file = Application_Model_StoredFile::RecallByMd5($md5);

            if (is_null($file)) {
                $this->view->error = "File doesn't exist in Airtime.";

                return;
            } else {
                $filepath = $md['MDATA_KEY_FILEPATH'];
                //$filepath = str_replace("\\", "", $filepath);
                $file->setFilePath($filepath);
            }
        } else if ($mode == "delete") {
            $filepath = $md['MDATA_KEY_FILEPATH'];
            //$filepath = str_replace("\\", "", $filepath);
            $file = Application_Model_StoredFile::RecallByFilepath($filepath);

            if (is_null($file)) {
                $this->view->error = "File doesn't exist in Airtime.";

                return;
            } else {
                $file->deleteByMediaMonitor();
            }
        } else if ($mode == "delete_dir") {
            $filepath = $md['MDATA_KEY_FILEPATH'];
            //$filepath = str_replace("\\", "", $filepath);
            $files = Application_Model_StoredFile::RecallByPartialFilepath($filepath);

            foreach ($files as $file) {
                $file->deleteByMediaMonitor();
            }

            return;
        }
        $this->view->id = $file->getId();
    }

    public function listAllFilesAction()
    {
        $request = $this->getRequest();
        $dir_id = $request->getParam('dir_id');

        $this->view->files = Application_Model_StoredFile::listAllFiles($dir_id);
    }

    public function listAllWatchedDirsAction()
    {
        $request = $this->getRequest();

        $result = array();

        $arrWatchedDirs = Application_Model_MusicDir::getWatchedDirs();
        $storDir = Application_Model_MusicDir::getStorDir();

        $result[$storDir->getId()] = $storDir->getDirectory();

        foreach ($arrWatchedDirs as $watchedDir) {
            $result[$watchedDir->getId()] = $watchedDir->getDirectory();
        }

        $this->view->dirs = $result;
    }

    public function addWatchedDirAction()
    {
        $request = $this->getRequest();
        $path = base64_decode($request->getParam('path'));

        $this->view->msg = Application_Model_MusicDir::addWatchedDir($path);
    }

    public function removeWatchedDirAction()
    {
        $request = $this->getRequest();
        $path = base64_decode($request->getParam('path'));

        $this->view->msg = Application_Model_MusicDir::removeWatchedDir($path);
    }

    public function setStorageDirAction()
    {
        $request = $this->getRequest();
        $path = base64_decode($request->getParam('path'));

        $this->view->msg = Application_Model_MusicDir::setStorDir($path);
    }

    public function getStreamSettingAction()
    {
        $request = $this->getRequest();

        $info = Application_Model_StreamSetting::getStreamSetting();
        $this->view->msg = $info;
    }

    public function statusAction()
    {
        $request = $this->getRequest();
        $getDiskInfo = $request->getParam('diskinfo') == "true";

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

        if ($getDiskInfo) {
            $status["partitions"] = Application_Model_Systemstatus::GetDiskInfo();
        }

        $this->view->status = $status;
    }

    public function registerComponentAction()
    {
        $request = $this->getRequest();

        $component = $request->getParam('component');
        $remoteAddr = Application_Model_ServiceRegister::GetRemoteIpAddr();
        Logging::log("Registered Component: ".$component."@".$remoteAddr);

        Application_Model_ServiceRegister::Register($component, $remoteAddr);
    }

    public function updateLiquidsoapStatusAction()
    {
        $request = $this->getRequest();

        $msg = $request->getParam('msg');
        $stream_id = $request->getParam('stream_id');
        $boot_time = $request->getParam('boot_time');

        Application_Model_StreamSetting::setLiquidsoapError($stream_id, $msg, $boot_time);
    }

    public function updateSourceStatusAction()
    {
        $request = $this->getRequest();

        $msg = $request->getParam('msg');
        $sourcename = $request->getParam('sourcename');
        $status = $request->getParam('status');

        // on source disconnection sent msg to pypo to turn off the switch
        // Added AutoTransition option
        if ($status == "false" && Application_Model_Preference::GetAutoTransition()) {
            $data = array("sourcename"=>$sourcename, "status"=>"off");
            Application_Model_RabbitMq::SendMessageToPypo("switch_source", $data);
            Application_Model_Preference::SetSourceSwitchStatus($sourcename, "off");
            Application_Model_LiveLog::SetEndTime($sourcename == 'scheduled_play'?'S':'L',
                                                  new DateTime("now", new DateTimeZone('UTC')));
        } elseif ($status == "true" && Application_Model_Preference::GetAutoSwitch()) {
            $data = array("sourcename"=>$sourcename, "status"=>"on");
            Application_Model_RabbitMq::SendMessageToPypo("switch_source", $data);
            Application_Model_Preference::SetSourceSwitchStatus($sourcename, "on");
            Application_Model_LiveLog::SetNewLogTime($sourcename == 'scheduled_play'?'S':'L',
                                                  new DateTime("now", new DateTimeZone('UTC')));
        }
        Application_Model_Preference::SetSourceStatus($sourcename, $status);
    }

    // handles addition/deletion of mount point which watched dirs reside
    public function updateFileSystemMountAction()
    {
        $request = $this->getRequest();

        $params = $request->getParams();
        $added_list = empty($params['added_dir'])?array():explode(',', $params['added_dir']);
        $removed_list = empty($params['removed_dir'])?array():explode(',', $params['removed_dir']);

        // get all watched dirs
        $watched_dirs = Application_Model_MusicDir::getWatchedDirs(null, null);

        foreach ($added_list as $ad) {
            $ad .= '/';
            foreach ($watched_dirs as $dir) {
                $dirPath = $dir->getDirectory();

                // if mount path itself was watched
                if ($dirPath == $ad) {
                    Application_Model_MusicDir::addWatchedDir($dirPath, false);
                } else if (substr($dirPath, 0, strlen($ad)) === $ad && $dir->getExistsFlag() == false) {
                    // if dir contains any dir in removed_list( if watched dir resides on new mounted path )
                    Application_Model_MusicDir::addWatchedDir($dirPath, false);
                } elseif (substr($ad, 0, strlen($dirPath)) === $dirPath) {
                    // is new mount point within the watched dir?
                    // pyinotify doesn't notify anyhing in this case, so we add this mount point as
                    // watched dir
                    // bypass nested loop check
                    Application_Model_MusicDir::addWatchedDir($ad, false, true);
                }
            }
        }

        foreach ($removed_list as $rd) {
            $rd .= '/';
            foreach ($watched_dirs as $dir) {
                $dirPath = $dir->getDirectory();
                // if dir contains any dir in removed_list( if watched dir resides on new mounted path )
                if (substr($dirPath, 0, strlen($rd)) === $rd && $dir->getExistsFlag() == true) {
                    Application_Model_MusicDir::removeWatchedDir($dirPath, false);
                } elseif (substr($rd, 0, strlen($dirPath)) === $dirPath) {
                    // is new mount point within the watched dir?
                    // pyinotify doesn't notify anyhing in this case, so we walk through all files within
                    // this watched dir in DB and mark them deleted.
                    // In case of h) of use cases, due to pyinotify behaviour of noticing mounted dir, we need to
                    // compare agaisnt all files in cc_files table

                    $watchDir = Application_Model_MusicDir::getDirByPath($rd);
                    // get all the files that is under $dirPath
                    $files = Application_Model_StoredFile::listAllFiles($dir->getId(), true);
                    foreach ($files as $f) {
                        // if the file is from this mount
                        if (substr($f->getFilePath(), 0, strlen($rd)) === $rd) {
                            $f->delete();
                        }
                    }
                    if ($watchDir) {
                        Application_Model_MusicDir::removeWatchedDir($rd, false);
                    }
                }
            }
        }
    }

    // handles case where watched dir is missing
    public function handleWatchedDirMissingAction()
    {
        $request = $this->getRequest();

        $dir = base64_decode($request->getParam('dir'));
        Application_Model_MusicDir::removeWatchedDir($dir, false);
    }

    /* This action is for use by our dev scripts, that make
     * a change to the database and we want rabbitmq to send
     * out a message to pypo that a potential change has been made. */
    public function rabbitmqDoPushAction()
    {
        $request = $this->getRequest();
        Logging::log("Notifying RabbitMQ to send message to pypo");

        Application_Model_RabbitMq::PushSchedule();
    }

    public function getBootstrapInfoAction()
    {
        $live_dj = Application_Model_Preference::GetSourceSwitchStatus('live_dj');
        $master_dj = Application_Model_Preference::GetSourceSwitchStatus('master_dj');
        $scheduled_play = Application_Model_Preference::GetSourceSwitchStatus('scheduled_play');

        $res = array("live_dj"=>$live_dj, "master_dj"=>$master_dj, "scheduled_play"=>$scheduled_play);
        $this->view->switch_status = $res;
        $this->view->station_name = Application_Model_Preference::GetStationName();
        $this->view->stream_label = Application_Model_Preference::GetStreamLabelFormat();
        $this->view->transition_fade = Application_Model_Preference::GetDefaultTransitionFade();
    }

    /* This is used but Liquidsoap to check authentication of live streams*/
    public function checkLiveStreamAuthAction()
    {
        $request = $this->getRequest();

        $username = $request->getParam('username');
        $password = $request->getParam('password');
        $djtype = $request->getParam('djtype');

        if ($djtype == 'master') {
            //check against master
            if ($username == Application_Model_Preference::GetLiveSteamMasterUsername()
                    && $password == Application_Model_Preference::GetLiveSteamMasterPassword()) {
                $this->view->msg = true;
            } else {
                $this->view->msg = false;
            }
        } elseif ($djtype == "dj") {
            //check against show dj auth
            $showInfo = Application_Model_Show::GetCurrentShow();
            // there is current playing show
            if (isset($showInfo[0]['id'])) {
                $current_show_id = $showInfo[0]['id'];
                $CcShow = CcShowQuery::create()->findPK($current_show_id);

                // get custom pass info from the show
                $custom_user = $CcShow->getDbLiveStreamUser();
                $custom_pass = $CcShow->getDbLiveStreamPass();

                // get hosts ids
                $show = new Application_Model_Show($current_show_id);
                $hosts_ids = $show->getHostsIds();

                // check against hosts auth
                if ($CcShow->getDbLiveStreamUsingAirtimeAuth()) {
                    foreach ($hosts_ids as $host) {
                        $h = new Application_Model_User($host['subjs_id']);
                        if ($username == $h->getLogin() && md5($password) == $h->getPassword()) {
                            $this->view->msg = true;

                            return;
                        }
                    }
                }
                // check against custom auth
                if ($CcShow->getDbLiveStreamUsingCustomAuth()) {
                    if ($username == $custom_user && $password == $custom_pass) {
                        $this->view->msg = true;
                    } else {
                        $this->view->msg = false;
                    }
                } else {
                    $this->view->msg = false;
                }
            } else {
                // no show is currently playing
                $this->view->msg = false;
            }
        }
    }

    /* This action is for use by our dev scripts, that make
     * a change to the database and we want rabbitmq to send
     * out a message to pypo that a potential change has been made. */
    public function getFilesWithoutReplayGainAction()
    {
        // disable the view and the layout
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $dir_id = $this->_getParam('dir_id');

        //connect to db and get get sql
        $rows = Application_Model_StoredFile::listAllFiles2($dir_id, 100);
        
        echo json_encode($rows);
    }

    public function updateReplayGainValueAction()
    {
        // disable layout
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $request = $this->getRequest();
        $data = json_decode($request->getParam('data'));
        
        foreach ($data as $pair) {
            list($id, $gain) = $pair;
            
            $file = Application_Model_StoredFile::Recall($p_id = $id)->getPropelOrm();
            Logging::debug("Setting $gain for file id $id");
            $file->setDbReplayGain($gain);
            $file->save();
        }
    }
}
