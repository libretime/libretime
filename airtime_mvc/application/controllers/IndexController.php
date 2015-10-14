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
        $this->view->headTitle(Application_Model_Preference::GetHeadTitle());
        $this->view->headScript()->appendFile($baseUrl . 'js/libs/jquery-1.8.3.min.js?' . $CC_CONFIG['airtime_version'], 'text/javascript')
            ->appendFile($baseUrl . 'js/i18n/jquery.i18n.js?' . $CC_CONFIG['airtime_version'], 'text/javascript')
            ->appendFile($baseUrl . 'locale/general-translation-table?' . $CC_CONFIG['airtime_version'], 'text/javascript')
            ->appendScript("$.i18n.setDictionary(general_dict)")
            ->appendScript("var baseUrl='$baseUrl'");

        $this->view->headLink()->setStylesheet($baseUrl.'css/radio-page/radio-page.css?'.$CC_CONFIG['airtime_version']);
        $this->view->headLink()->appendStylesheet($baseUrl.'css/embed/weekly-schedule-widget.css?'.$CC_CONFIG['airtime_version']);

        $this->_helper->layout->setLayout('radio-page');

        $this->view->stationLogo = Application_Model_Preference::GetStationLogo();

        $stationName = Application_Model_Preference::GetStationName();
        $this->view->stationName = $stationName;

        $stationDescription = Application_Model_Preference::GetStationDescription();
        $this->view->stationDescription = $stationDescription;

        $this->view->stationUrl = Application_Common_HTTPHelper::getStationUrl();

        $displayRadioPageLoginButtonValue = Application_Model_Preference::getRadioPageDisplayLoginButton();
        if ($displayRadioPageLoginButtonValue == "") {
            $displayRadioPageLoginButtonValue = true;
        }
        $this->view->displayLoginButton = $displayRadioPageLoginButtonValue;

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
