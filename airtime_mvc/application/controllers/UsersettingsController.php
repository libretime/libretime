<?php
class UsersettingsController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('get-now-playing-screen-settings', 'json')
                    ->addActionContext('set-now-playing-screen-settings', 'json')
                    ->addActionContext('get-library-datatable', 'json')
                    ->addActionContext('set-library-datatable', 'json')
                    ->addActionContext('get-timeline-datatable', 'json')
                    ->addActionContext('set-timeline-datatable', 'json')
                    ->addActionContext('remindme', 'json')
                    ->addActionContext('donotshowregistrationpopup', 'json')
                    ->initContext();
    }
    
    public function setNowPlayingScreenSettingsAction() {
        
        $request = $this->getRequest();
        $settings = $request->getParam("settings");
        
        $data = serialize($settings);
        Application_Model_Preference::SetValue("nowplaying_screen", $data, true);
    }
    
    public function getNowPlayingScreenSettingsAction() {
    
        $data = Application_Model_Preference::GetValue("nowplaying_screen", true);
        if ($data != "") {
            $this->view->settings = unserialize($data);
        }
    }

    public function setLibraryDatatableAction() {

        $request = $this->getRequest();
        $settings = $request->getParam("settings");

        $data = serialize($settings);
        Application_Model_Preference::SetValue("library_datatable", $data, true);
    }

    public function getLibraryDatatableAction() {

        $data = Application_Model_Preference::GetValue("library_datatable", true);
        if ($data != "") {
            $this->view->settings = unserialize($data);
        }
    }

    public function setTimelineDatatableAction() {

        $start = microtime(true);
        
        $request = $this->getRequest();
        $settings = $request->getParam("settings");

        $data = serialize($settings);
        Application_Model_Preference::SetValue("timeline_datatable", $data, true);
        
        $end = microtime(true);
        
        Logging::debug("saving timeline datatables info took:");
        Logging::debug(floatval($end) - floatval($start));
    }

    public function getTimelineDatatableAction() {
        
        $start = microtime(true);

        $data = Application_Model_Preference::GetValue("timeline_datatable", true);
        if ($data != "") {
            $this->view->settings = unserialize($data);
        }
        
        $end = microtime(true);
        
        Logging::debug("getting timeline datatables info took:");
        Logging::debug(floatval($end) - floatval($start));
    }
    
    public function remindmeAction()
    {
        // unset session
        Zend_Session::namespaceUnset('referrer');
        Application_Model_Preference::SetRemindMeDate();
    }
    
    public function donotshowregistrationpopupAction()
    {
        // unset session
        Zend_Session::namespaceUnset('referrer');
    }
}