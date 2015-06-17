<?php

class SystemstatusController extends Zend_Controller_Action
{
    public function init()
    {
        /* Disable this on Airtime pro since we're not using Media Monitor/Monit

        $CC_CONFIG = Config::getConfig();
        $baseUrl = Application_Common_OsPath::getBaseDir();
        $this->view->headScript()->appendFile($baseUrl.'js/airtime/status/status.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        */
    }

    public function indexAction()
    {
        $partitions = Application_Model_Systemstatus::GetDiskInfo();

        $this->view->status = new StdClass;
        $this->view->status->partitions = $partitions;
    }
}
