<?php

class SystemstatusController extends Zend_Controller_Action
{
    public function init()
    {
        global $CC_CONFIG;
        
        $request = $this->getRequest();
        $baseUrl = $request->getBaseUrl();
        
        $this->view->headScript()->appendFile($baseUrl.'/js/airtime/status/status.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/sprintf/sprintf-0.7-beta1.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
    }

    public function indexAction()
    {
        $services = array(
            "pypo"=>Application_Model_Systemstatus::GetPypoStatus(),
            "liquidsoap"=>Application_Model_Systemstatus::GetLiquidsoapStatus(),
            "media-monitor"=>Application_Model_Systemstatus::GetMediaMonitorStatus(),
            "rabbitmq-server"=>Application_Model_Systemstatus::GetRabbitMqStatus()
        );

        $partitions = Application_Model_Systemstatus::GetDiskInfo();
        
        $this->view->status = new StdClass;
        $this->view->status->services = $services;
        $this->view->status->partitions = $partitions;
    }
}
