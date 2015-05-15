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
        $this->view->headLink()->appendStylesheet($baseUrl.'css/radio-page/radio-page.css?'.$CC_CONFIG['airtime_version']);
        $this->_helper->layout->setLayout('radio-page');

        $this->view->stationLogo = Application_Model_Preference::GetStationLogo();

        $stationName = Application_Model_Preference::GetStationName();
        $stationName = empty($stationName) ? "Station Name" : $stationName;
        $this->view->stationName = $stationName;

        $stationDescription = Application_Model_Preference::GetStationDescription();
        $stationDescription = empty($stationDescription) ? "Station Description" : $stationDescription;
        $this->view->stationDescription = $stationDescription;
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
