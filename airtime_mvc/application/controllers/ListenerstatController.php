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
                $v['value'] = _("Please make sure admin user/password is correct on System->Streams page.");
            }
            $out[$key[0]] = $v['value'];
        }

        $this->view->errorStatus = $out;
        $this->view->date_form = $form;
    }

    public function getDataAction(){
        list($startsDT, $endsDT) = Application_Common_HTTPHelper::getStartEndFromRequest($this->getRequest());
        
        $data = Application_Model_ListenerStat::getDataPointsWithinRange($startsDT->format(DEFAULT_TIMESTAMP_FORMAT),
                                                                         $endsDT->format(DEFAULT_TIMESTAMP_FORMAT));
        $this->_helper->json->sendJson($data);
    }
}
