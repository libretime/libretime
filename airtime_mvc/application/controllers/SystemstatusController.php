<?php

class SystemstatusController extends Zend_Controller_Action
{
    public function init()
    {

    }

    public function indexAction()
    {
        $ss = new Application_Model_Systemstatus();

        $stats = array("Total R");
        
        $this->view->status = $ss->getResults();
    }

    public function getLogFileAction()
    {
        $log_files = array("PLAYOUT_ENGINE_RUNNING_SECONDS"=>"/var/log/airtime/pypo/pypo.log",
                "LIQUIDSOAP_RUNNING_SECONDS"=>"/var/log/airtime/pypo-liquidsoap/ls_script.log",
                "MEDIA_MONITOR_RUNNING_SECONDS"=>"/var/log/airtime/media-monitor/media-monitor.log",
                "SHOW_RECORDER_RUNNING_SECONDS"=>"/var/log/airtime/show-recorder/show-recorder.log");

        $id = $this->_getParam('id');
        Logging::log($id);

        if (array_key_exists($id, $log_files)){
            $filepath = $log_files[$id];
            $filename = basename($filepath);
            header("Content-Disposition: attachment; filename=$filename");
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
        }
    }
}
