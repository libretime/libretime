<?php

class ListenerstatController extends Zend_Controller_Action
{
    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext
        ->addActionContext('get-data', 'json')
        ->initContext();
    }
    
    public function indexAction()
    {
        $CC_CONFIG = Config::getConfig();

        $request = $this->getRequest();
        $baseUrl = Application_Common_OsPath::getBaseDir();

        Zend_Layout::getMvcInstance()->assign('parent_page', 'Analytics');

        $this->view->headScript()->appendFile($baseUrl.'js/flot/jquery.flot.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/flot/jquery.flot.crosshair.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/flot/jquery.flot.resize.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/airtime/listenerstat/listenerstat.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/timepicker/jquery.ui.timepicker.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/airtime/buttons/buttons.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/airtime/utilities/utilities.js?'.$CC_CONFIG['airtime_version'],'text/javascript');

        $this->view->headLink()->appendStylesheet($baseUrl.'css/jquery.ui.timepicker.css?'.$CC_CONFIG['airtime_version']);

        list($startsDT, $endsDT) = Application_Common_HTTPHelper::getStartEndFromRequest($request);
        $userTimezone = new DateTimeZone(Application_Model_Preference::GetUserTimezone());
        $startsDT->setTimezone($userTimezone);
        $endsDT->setTimezone($userTimezone);

        $form = new Application_Form_DateRange();
        $form->populate(array(
            'his_date_start' => $startsDT->format("Y-m-d"),
            'his_time_start' => $startsDT->format("H:i"),
            'his_date_end' => $endsDT->format("Y-m-d"),
            'his_time_end' => $endsDT->format("H:i")
        ));

        $errorStatus = Application_Model_StreamSetting::GetAllListenerStatErrors();
        Logging::info($errorStatus);
        $out = array();
        foreach ($errorStatus as $v) {
            $key = explode('_listener_stat_error', $v['keyname']);
            if ($v['value'] != 'OK') {
                $v['value'] = _("Please make sure admin user/password is correct on Settings->Streams page.");
            }
            $out[$key[0]] = $v['value'];
        }

        $this->view->errorStatus = $out;
        $this->view->date_form = $form;
    }
    public function showAction() {
        $CC_CONFIG = Config::getConfig();

        $request = $this->getRequest();
        $baseUrl = Application_Common_OsPath::getBaseDir();
        Zend_Layout::getMvcInstance()->assign('parent_page', 'Analytics');
        $this->view->headScript()->appendFile($baseUrl.'js/airtime/listenerstat/showlistenerstat.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/timepicker/jquery.ui.timepicker.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/airtime/buttons/buttons.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/datatables/js/jquery.dataTables.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/datatables/plugin/dataTables.pluginAPI.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/airtime/utilities/utilities.js?'.$CC_CONFIG['airtime_version'],'text/javascript');

        $this->view->headLink()->appendStylesheet($baseUrl.'css/jquery.ui.timepicker.css?'.$CC_CONFIG['airtime_version']);
        $user = Application_Model_User::getCurrentUser();
        if ($user->isUserType(array(UTYPE_SUPERADMIN, UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER))) {
            $this->view->showAllShows = true;
        }
        $form = new Application_Form_ShowBuilder();

        list($startsDT, $endsDT) = Application_Common_HTTPHelper::getStartEndFromRequest($request);
        $userTimezone = new DateTimeZone(Application_Model_Preference::GetUserTimezone());
        $startsDT->setTimezone($userTimezone);
        $endsDT->setTimezone($userTimezone);
        $form->populate(array(
            'sb_date_start' => $startsDT->format("Y-m-d"),
            'sb_time_start' => $startsDT->format("H:i"),
            'sb_date_end'   => $endsDT->format("Y-m-d"),
            'sb_time_end'   => $endsDT->format("H:i")
        ));

        $this->view->sb_form = $form;
    }

    public function getDataAction(){
        list($startsDT, $endsDT) = Application_Common_HTTPHelper::getStartEndFromRequest($this->getRequest());

        $data = Application_Model_ListenerStat::getDataPointsWithinRange($startsDT->format(DEFAULT_TIMESTAMP_FORMAT),
                                                                         $endsDT->format(DEFAULT_TIMESTAMP_FORMAT));
        $this->_helper->json->sendJson($data);
    }
    public function getShowDataAction(){
        list($startsDT, $endsDT) = Application_Common_HTTPHelper::getStartEndFromRequest($this->getRequest());
        $show_id = $this->getRequest()->getParam("show_id", null);
        $data = Application_Model_ListenerStat::getShowDataPointsWithinRange($startsDT->format(DEFAULT_TIMESTAMP_FORMAT),
            $endsDT->format(DEFAULT_TIMESTAMP_FORMAT),$show_id);
        $this->_helper->json->sendJson($data);
    }
    public function getAllShowDataAction(){
        list($startsDT, $endsDT) = Application_Common_HTTPHelper::getStartEndFromRequest($this->getRequest());
        $show_id = $this->getRequest()->getParam("show_id", null);
        $data = Application_Model_ListenerStat::getAllShowDataPointsWithinRange($startsDT->format(DEFAULT_TIMESTAMP_FORMAT),
            $endsDT->format(DEFAULT_TIMESTAMP_FORMAT),$show_id);
        $this->_helper->json->sendJson($data);
    }
}
