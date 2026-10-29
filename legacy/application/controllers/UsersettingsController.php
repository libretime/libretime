<?php

class UsersettingsController extends Zend_Controller_Action
{
    public function init(): void
    {
        // Initialize action controller here
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('get-now-playing-screen-settings', 'json')
            ->addActionContext('set-now-playing-screen-settings', 'json')
            ->addActionContext('get-library-datatable', 'json')
            ->addActionContext('set-library-datatable', 'json')
            ->addActionContext('get-timeline-datatable', 'json')
            ->addActionContext('set-timeline-datatable', 'json')
            ->addActionContext('remindme', 'json')
            ->addActionContext('remindme-never', 'json')
            ->addActionContext('donotshowregistrationpopup', 'json')
            ->addActionContext('set-library-screen-settings', 'json')
            ->initContext();
    }

    public function setNowPlayingScreenSettingsAction(): void
    {
        $request = $this->getRequest();
        $settings = $request->getParam('settings');

        Application_Model_Preference::setNowPlayingScreenSettings($settings);
    }

    public function getNowPlayingScreenSettingsAction(): void
    {
        $data = Application_Model_Preference::getNowPlayingScreenSettings();
        if (!is_null($data)) {
            $this->view->settings = $data;
        }
    }

    public function setLibraryDatatableAction(): void
    {
        $request = $this->getRequest();
        $settings = $request->getParam('settings');

        Application_Model_Preference::setCurrentLibraryTableSetting($settings);
    }

    public function getLibraryDatatableAction(): void
    {
        $data = Application_Model_Preference::getCurrentLibraryTableSetting();
        if (!is_null($data)) {
            $this->_helper->json($data);
        } else {
            $this->_helper->json(false);
        }
    }

    public function setTimelineDatatableAction(): void
    {
        $request = $this->getRequest();
        $settings = $request->getParam('settings');

        Application_Model_Preference::setTimelineDatatableSetting($settings);
    }

    public function getTimelineDatatableAction(): void
    {
        $data = Application_Model_Preference::getTimelineDatatableSetting();
        if (!is_null($data)) {
            $this->view->settings = $data;
        }
    }

    public function remindmeAction(): void
    {
        // unset session
        SessionHelper::reopenSessionForWriting();
        Zend_Session::namespaceUnset('referrer');
        Application_Model_Preference::SetRemindMeDate();
    }

    public function remindmeNeverAction(): void
    {
        SessionHelper::reopenSessionForWriting();
        Zend_Session::namespaceUnset('referrer');
        // pass in true to indicate 'Remind me never' was clicked
        Application_Model_Preference::SetRemindMeDate(true);
    }

    public function donotshowregistrationpopupAction(): void
    {
        // unset session
        SessionHelper::reopenSessionForWriting();
        Zend_Session::namespaceUnset('referrer');
    }

    public function setLibraryScreenSettingsAction(): void
    {
        $request = $this->getRequest();
        $settings = $request->getParam('settings');
        Application_Model_Preference::setLibraryScreenSettings($settings);
    }
}
