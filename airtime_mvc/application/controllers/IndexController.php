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
        $this->view->headLink()->setStylesheet($baseUrl.'css/radio-page/radio-page.css?'.$CC_CONFIG['airtime_version']);
        $this->view->headLink()->appendStylesheet($baseUrl.'css/embed/weekly-schedule-widget.css?'.$CC_CONFIG['airtime_version']);

        $this->_helper->layout->setLayout('radio-page');

        $this->view->stationLogo = Application_Model_Preference::GetStationLogo();

        $stationName = Application_Model_Preference::GetStationName();
        $this->view->stationName = $stationName;

        $stationDescription = Application_Model_Preference::GetStationDescription();
        $this->view->stationDescription = $stationDescription;

        $this->view->stationUrl = Application_Common_HTTPHelper::getStationUrl();

        $this->view->displayLoginButton = Application_Model_Preference::getRadioPageDisplayLoginButton();

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
