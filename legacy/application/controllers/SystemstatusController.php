<?php

class SystemstatusController extends Zend_Controller_Action
{
    private $version;

    public function init()
    {
        $config = Config::getConfig();
        $baseUrl = Application_Common_OsPath::getBaseDir();
        $this->view->headScript()->appendFile($baseUrl . 'js/airtime/status/status.js?' . $config['airtime_version'], 'text/javascript');
        $this->version = $config['airtime_version'];
    }

    public function indexAction()
    {
        Zend_Layout::getMvcInstance()->assign('parent_page', 'Settings');

        $partitions = Application_Model_Systemstatus::GetDiskInfo();
        $this->view->status = new stdClass();
        $this->view->status->partitions = $partitions;
        $this->view->version = $this->version;
    }
}
