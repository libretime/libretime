<?php
class UsersettingsController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('get-now-playing-screen-settings', 'json')
                    ->addActionContext('set-now-playing-screen-settings', 'json')
                    ->addActionContext('get-audio-datatable', 'json')
                    ->addActionContext('set-audio-datatable', 'json')
                    ->addActionContext('get-webstream-datatable', 'json')
                    ->addActionContext('set-webstream-datatable', 'json')
                    ->addActionContext('get-playlist-datatable', 'json')
                    ->addActionContext('set-playlist-datatable', 'json')
                    ->addActionContext('get-timeline-datatable', 'json')
                    ->addActionContext('set-timeline-datatable', 'json')
                    ->addActionContext('remindme', 'json')
                    ->addActionContext('remindme-never', 'json')
                    ->addActionContext('donotshowregistrationpopup', 'json')
                    ->addActionContext('set-library-screen-settings', 'json')
                    ->initContext();
    }

    public function setNowPlayingScreenSettingsAction()
    {
        $request = $this->getRequest();
        $settings = $request->getParam("settings");

        Application_Model_Preference::setNowPlayingScreenSettings($settings);
    }

    public function getNowPlayingScreenSettingsAction()
    {
        $data = Application_Model_Preference::getNowPlayingScreenSettings();
        if (!is_null($data)) {
            $this->view->settings = $data;
        }
    }
    
    public function setAudioDatatableAction()
    {
    	$request = $this->getRequest();
    	$settings = $request->getParam("settings");
    
    	Application_Model_Preference::setAudioTableSetting($settings);
    }
    
    public function getAudioDatatableAction()
    {
    	$this->view->settings = Application_Model_Preference::getAudioTableSetting();
    }
    
    public function setWebstreamDatatableAction()
    {
    	$request = $this->getRequest();
    	$settings = $request->getParam("settings");
    
    	Application_Model_Preference::setWebstreamTableSetting($settings);
    }
    
    public function getWebstreamDatatableAction()
    {
    	$this->view->settings = Application_Model_Preference::getWebstreamTableSetting();
    }
    
    public function setPlaylistDatatableAction()
    {
    	$request = $this->getRequest();
    	$settings = $request->getParam("settings");
    
    	Application_Model_Preference::setPlaylistTableSetting($settings);
    }
    
    public function getPlaylistDatatableAction()
    {
    	$this->view->settings = Application_Model_Preference::getPlaylistTableSetting();
    }

    public function setTimelineDatatableAction()
    {
        $request = $this->getRequest();
        $settings = $request->getParam("settings");

        Application_Model_Preference::setTimelineDatatableSetting($settings);
    }

    public function getTimelineDatatableAction()
    {
        $start = microtime(true);

        $data = Application_Model_Preference::getTimelineDatatableSetting();
        if (!is_null($data)) {
            $this->view->settings = $data;
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
    
    public function remindmeNeverAction()
    {
        Zend_Session::namespaceUnset('referrer');
        //pass in true to indicate 'Remind me never' was clicked
        Application_Model_Preference::SetRemindMeDate(true);
    }

    public function donotshowregistrationpopupAction()
    {
        // unset session
        Zend_Session::namespaceUnset('referrer');
    }

    public function setLibraryScreenSettingsAction()
    {
        $request = $this->getRequest();
        $settings = $request->getParam("settings");
        Application_Model_Preference::setLibraryScreenSettings($settings);
    }
}
