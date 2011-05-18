<?php

class ApiController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $context = $this->_helper->getHelper('contextSwitch');
        $context->addActionContext('version', 'json')
                ->addActionContext('recorded-shows', 'json')
                ->addActionContext('upload-recorded', 'json')
                ->addActionContext('reload-metadata', 'json')
                ->initContext();
    }

    public function indexAction()
    {
        // action body
    }

	/**
	 * Returns Airtime version. i.e "1.7.0 alpha"
	 *
	 * First checks to ensure the correct API key was
	 * supplied, then returns AIRTIME_VERSION as defined
	 * in application/conf.php
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
        $jsonStr = json_encode(array("version"=>AIRTIME_VERSION));
        echo $jsonStr;
    }

	/**
	 * Allows remote client to download requested media file.
	 *
	 * @return void
	 *      The given value increased by the increment amount.
	 */
    public function getMediaAction()
    {
        global $CC_CONFIG;

        // disable the view and the layout
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $api_key = $this->_getParam('api_key');
        $downlaod = $this->_getParam('download');

        if(!in_array($api_key, $CC_CONFIG["apiKey"]))
        {
        	header('HTTP/1.0 401 Unauthorized');
        	print 'You are not allowed to access this resource.';
        	exit;
        }

        $filename = $this->_getParam("file");
        $file_id = substr($filename, 0, strpos($filename, "."));
        if (ctype_alnum($file_id) && strlen($file_id) == 32) {
          $media = StoredFile::RecallByGunid($file_id);
          if ($media != null && !PEAR::isError($media)) {
            $filepath = $media->getRealFilePath();
            if(!is_file($filepath))
            {
            	header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
            	//print 'Resource in database, but not in storage. Sorry.';
            	exit;
            }

            // !! binary mode !!
            $fp = fopen($filepath, 'rb');

        	// possibly use fileinfo module here in the future.
        	// http://www.php.net/manual/en/book.fileinfo.php
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            if ($ext == "ogg")
                header("Content-Type: audio/ogg");
            else if ($ext == "mp3")
                header("Content-Type: audio/mpeg");
			if ($downlaod){
				header('Content-Disposition: attachment; filename="'.$media->getName().'"');
			}
            header("Content-Length: " . filesize($filepath));
            fpassthru($fp);
            return;
          }
      }
	  header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
	  exit;
    }

    public function liveInfoAction()
    {
        if (Application_Model_Preference::GetAllow3rdPartyApi()){
            // disable the view and the layout
            $this->view->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);

            $date = new DateHelper;
            $timeNow = $date->getTimestamp();
            $result = array("env"=>APPLICATION_ENV,
                "schedulerTime"=>gmdate("Y-m-d H:i:s"),
                "currentShow"=>Show_DAL::GetCurrentShow($timeNow),
                "nextShow"=>Show_DAL::GetNextShows($timeNow, 5),
                "timezone"=> date("T"),
                "timezoneOffset"=> date("Z"));

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

            $dow = array("sunday", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday");

            $result = array();
            for ($i=0; $i<7; $i++){
                $result[$dow[$i]] = Show_DAL::GetShowsByDayOfWeek($i);
            }

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

        $result = Schedule::GetScheduledPlaylists();
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
        $result = Schedule::UpdateMediaPlayedStatus($media_id);

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
            $sg = new ScheduleGroup($schedule_group_id);
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
        $this->view->shows = Show::getShows($today_timestamp, $end_timestamp, $excludeInstance=NULL, $onlyRecord=TRUE);


        $this->view->is_recording = false;

        $rows = Show_DAL::GetCurrentShow($today_timestamp);
        if (count($rows) > 0){
            $this->view->is_recording = ($rows[0]['record'] == 1);
        }
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

        $upload_dir = ini_get("upload_tmp_dir");
        $file = StoredFile::uploadFile($upload_dir);

        $show_instance  = $this->_getParam('show_instance');

        try {
            $show_inst = new ShowInstance($show_instance);

            $show_inst->setRecordedFile($file->getId());
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
        } catch (Exception $e){
            //we've reached here probably because the show was
            //cancelled, and therefore the show instance does not
            //exist anymore (ShowInstance constructor threw this error).
            //We've done all we can do (upload the file and put it in
            //the library), now lets just return.
        }

        $this->view->id = $file->getId();
    }

    public function reloadMetadataAction() {

        global $CC_CONFIG;

        $api_key = $this->_getParam('api_key');
        if (!in_array($api_key, $CC_CONFIG["apiKey"]))
        {
        	header('HTTP/1.0 401 Unauthorized');
        	print 'You are not allowed to access this resource.';
        	exit;
        }

        $md = $this->_getParam('md');

        $file = StoredFile::Recall(null, $md['gunid']);
        if (PEAR::isError($file) || is_null($file)) {
            $this->view->response = "File not in Airtime's Database";
            return;
        }

        $res = $file->replaceDbMetadata($md);

        if (PEAR::isError($res)) {
            $this->view->response = "Metadata Change Failed";
        }
        else {
            $this->view->response = "Success!";
        }
    }
}

