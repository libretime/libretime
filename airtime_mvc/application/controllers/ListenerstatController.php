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

        $this->view->headScript()->appendFile($baseUrl.'js/flot/jquery.flot.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/flot/jquery.flot.crosshair.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/airtime/listenerstat/listenerstat.js?'.$CC_CONFIG['airtime_version'],'text/javascript');

        $offset = date("Z") * -1;
        $this->view->headScript()->appendScript("var serverTimezoneOffset = {$offset}; //in seconds");
        $this->view->headScript()->appendFile($baseUrl.'js/timepicker/jquery.ui.timepicker.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/airtime/buttons/buttons.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/airtime/utilities/utilities.js?'.$CC_CONFIG['airtime_version'],'text/javascript');

        $this->view->headLink()->appendStylesheet($baseUrl.'css/jquery.ui.timepicker.css?'.$CC_CONFIG['airtime_version']);

        //default time is the last 24 hours.
        $now = time();
        $from = $request->getParam("from", $now - (24*60*60));
        $to = $request->getParam("to", $now);

        $start = DateTime::createFromFormat("U", $from, new DateTimeZone("UTC"));
        $start->setTimezone(new DateTimeZone(date_default_timezone_get()));
        $end = DateTime::createFromFormat("U", $to, new DateTimeZone("UTC"));
        $end->setTimezone(new DateTimeZone(date_default_timezone_get()));

        $form = new Application_Form_DateRange();
        $form->populate(array(
                'his_date_start' => $start->format("Y-m-d"),
                'his_time_start' => $start->format("H:i"),
                'his_date_end' => $end->format("Y-m-d"),
                'his_time_end' => $end->format("H:i")
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
        $request = $this->getRequest();
        $current_time = time();

        $params = $request->getParams();

        $starts_epoch = $request->getParam("startTimestamp", $current_time - (60*60*24));
        $ends_epoch = $request->getParam("endTimestamp", $current_time);

        $startsDT = DateTime::createFromFormat("U", $starts_epoch, new DateTimeZone("UTC"));
        $endsDT = DateTime::createFromFormat("U", $ends_epoch, new DateTimeZone("UTC"));

        $data = Application_Model_ListenerStat::getDataPointsWithinRange($startsDT->format("Y-m-d H:i:s"), $endsDT->format("Y-m-d H:i:s"));
        $this->_helper->json->sendJson($data);
    }
}
