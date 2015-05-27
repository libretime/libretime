<?php

class SystemstatusController extends Zend_Controller_Action
{
    public function init()
    {
        $CC_CONFIG = Config::getConfig();

        $baseUrl = Application_Common_OsPath::getBaseDir();

        $this->view->headScript()->appendFile($baseUrl.'js/airtime/status/status.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
    }

    public function indexAction()
    {
        /*
        $services = array(
            "pypo"=>Application_Model_Systemstatus::GetPypoStatus(),
            "liquidsoap"=>Application_Model_Systemstatus::GetLiquidsoapStatus(),
            //"media-monitor"=>Application_Model_Systemstatus::GetMediaMonitorStatus(),
        );
        */

        $partitions = Application_Model_Systemstatus::GetDiskInfo();

        $this->view->status = new StdClass;
        //$this->view->status->services = $services;
        $this->view->status->partitions = $partitions;
    }
}
