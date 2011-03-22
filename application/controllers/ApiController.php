<?php

class ApiController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('version', 'json')
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


            header("Content-Length: " . filesize($filepath));
            fpassthru($fp);
            return;
          }
      }
	  header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
	  exit;
    }

    public function liveInfoAction(){
        global $CC_CONFIG;

        // disable the view and the layout
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $result = Schedule::GetPlayOrderRange(0, 1);
        //echo json_encode($result);
        header("Content-type: text/javascript");
        echo $_GET['callback'].'('.json_encode($result).')';
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

        $from = $this->_getParam("from");
        $to = $this->_getParam("to");
        if (Schedule::ValidPypoTimeFormat($from) && Schedule::ValidPypoTimeFormat($to)) {
            $result = Schedule::ExportRangeAsJson($from, $to);
            $result['stream_metadata'] = array();
            $result['stream_metadata']['format'] = Application_Model_Preference::GetStreamLabelFormat();
            $result['stream_metadata']['station_name'] = Application_Model_Preference::GetStationName();
            echo json_encode($result);
        }
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
}

