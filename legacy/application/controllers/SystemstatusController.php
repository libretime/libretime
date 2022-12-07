<?php

declare(strict_types=1);

class SystemstatusController extends Zend_Controller_Action
{
    private $version;

    public function init()
    {
        $config = Config::getConfig();
        $this->view->headScript()->appendFile(Assets::url('js/airtime/status/status.js'), 'text/javascript');
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
