<?php

class SystemstatusController extends Zend_Controller_Action
{
    public function init()
    {
        $request = $this->getRequest();
        $baseUrl = $request->getBaseUrl();
        
        $this->view->headScript()->appendFile($baseUrl.'/js/airtime/status/status.js','text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/sprintf/sprintf-0.7-beta1.js','text/javascript');
    }

    public function indexAction()
    {
        $services = array(
            "pypo"=>Application_Model_Systemstatus::GetPypoStatus(),
            "liquidsoap"=>Application_Model_Systemstatus::GetLiquidsoapStatus(),
            "show-recorder"=>Application_Model_Systemstatus::GetShowRecorderStatus(),
            "media-monitor"=>Application_Model_Systemstatus::GetMediaMonitorStatus(),
            "rabbitmq-server"=>Application_Model_Systemstatus::GetRabbitMqStatus()
        );

        $partitions = Application_Model_Systemstatus::GetDiskInfo();
        
        $this->view->status = new StdClass;
        $this->view->status->services = $services;
        $this->view->status->partitions = $partitions;
    }

    public function getLogFileAction()
    {
        $log_files = array("pypo"=>"/var/log/airtime/pypo/pypo.log",
                "liquidsoap"=>"/var/log/airtime/pypo-liquidsoap/ls_script.log",
                "media-monitor"=>"/var/log/airtime/media-monitor/media-monitor.log",
                "show-recorder"=>"/var/log/airtime/show-recorder/show-recorder.log",
                "icecast2"=>"/var/log/icecast2/error.log");

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
