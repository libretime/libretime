<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {

    }

    public function indexAction()
    {
        $CC_CONFIG = Config::getConfig();
        $baseUrl = Application_Common_OsPath::getBaseDir();
        $this->view->headLink()->appendStylesheet($baseUrl.'css/landing-page.css?'.$CC_CONFIG['airtime_version']);
        $this->_helper->layout->setLayout('login');

        $this->view->stationLogo = Application_Model_Preference::GetStationLogo();
        $this->view->stationName = Application_Model_Preference::GetStationName();
        $this->view->stationDescription = Application_Model_Preference::GetStationDescription();
    }

    public function mainAction()
    {
        $this->_helper->layout->setLayout('layout');
    }

    public function maintenanceAction()
    {
        $this->getResponse()->setHttpResponseCode(503);
    }

}
