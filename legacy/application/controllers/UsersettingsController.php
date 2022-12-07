<?php

declare(strict_types=1);

class UsersettingsController extends Zend_Controller_Action
{
    public function init()
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

    public function setNowPlayingScreenSettingsAction()
    {
        $request = $this->getRequest();
        $settings = $request->getParam('settings');

        Application_Model_Preference::setNowPlayingScreenSettings($settings);
    }

    public function getNowPlayingScreenSettingsAction()
    {
        $data = Application_Model_Preference::getNowPlayingScreenSettings();
        if (!is_null($data)) {
            $this->view->settings = $data;
        }
    }

    public function setLibraryDatatableAction()
    {
        $request = $this->getRequest();
        $settings = $request->getParam('settings');

        Application_Model_Preference::setCurrentLibraryTableSetting($settings);
    }

    public function getLibraryDatatableAction()
    {
        $data = Application_Model_Preference::getCurrentLibraryTableSetting();
        if (!is_null($data)) {
            $this->_helper->json($data);
        } else {
            $this->_helper->json(false);
        }
    }

    public function setTimelineDatatableAction()
    {
        $request = $this->getRequest();
        $settings = $request->getParam('settings');

        Application_Model_Preference::setTimelineDatatableSetting($settings);
    }

    public function getTimelineDatatableAction()
    {
        $data = Application_Model_Preference::getTimelineDatatableSetting();
        if (!is_null($data)) {
            $this->view->settings = $data;
        }
    }

    public function remindmeAction()
    {
        // unset session
        SessionHelper::reopenSessionForWriting();
        Zend_Session::namespaceUnset('referrer');
        Application_Model_Preference::SetRemindMeDate();
    }

    public function remindmeNeverAction()
    {
        SessionHelper::reopenSessionForWriting();
        Zend_Session::namespaceUnset('referrer');
        // pass in true to indicate 'Remind me never' was clicked
        Application_Model_Preference::SetRemindMeDate(true);
    }

    public function donotshowregistrationpopupAction()
    {
        // unset session
        SessionHelper::reopenSessionForWriting();
        Zend_Session::namespaceUnset('referrer');
    }

    public function setLibraryScreenSettingsAction()
    {
        $request = $this->getRequest();
        $settings = $request->getParam('settings');
        Application_Model_Preference::setLibraryScreenSettings($settings);
    }
}
