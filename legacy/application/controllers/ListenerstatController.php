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

        $this->view->headScript()->appendFile($baseUrl . 'js/flot/jquery.flot.js?' . $CC_CONFIG['airtime_version'], 'text/javascript');
        $this->view->headScript()->appendFile($baseUrl . 'js/flot/jquery.flot.crosshair.js?' . $CC_CONFIG['airtime_version'], 'text/javascript');
        $this->view->headScript()->appendFile($baseUrl . 'js/flot/jquery.flot.resize.js?' . $CC_CONFIG['airtime_version'], 'text/javascript');
        $this->view->headScript()->appendFile($baseUrl . 'js/airtime/listenerstat/listenerstat.js?' . $CC_CONFIG['airtime_version'], 'text/javascript');
        $this->view->headScript()->appendFile($baseUrl . 'js/timepicker/jquery.ui.timepicker.js?' . $CC_CONFIG['airtime_version'], 'text/javascript');
        $this->view->headScript()->appendFile($baseUrl . 'js/airtime/buttons/buttons.js?' . $CC_CONFIG['airtime_version'], 'text/javascript');
        $this->view->headScript()->appendFile($baseUrl . 'js/airtime/utilities/utilities.js?' . $CC_CONFIG['airtime_version'], 'text/javascript');
        $this->view->headLink()->appendStylesheet($baseUrl . 'css/jquery.ui.timepicker.css?' . $CC_CONFIG['airtime_version']);

        [$startsDT, $endsDT] = Application_Common_HTTPHelper::getStartEndFromRequest($request);
        $userTimezone = new DateTimeZone(Application_Model_Preference::GetUserTimezone());
        $startsDT->setTimezone($userTimezone);
        $endsDT->setTimezone($userTimezone);

        $form = new Application_Form_DateRange();
        $form->populate([
            'his_date_start' => $startsDT->format('Y-m-d'),
            'his_time_start' => $startsDT->format('H:i'),
            'his_date_end' => $endsDT->format('Y-m-d'),
            'his_time_end' => $endsDT->format('H:i'),
        ]);

        $errorStatus = Application_Model_StreamSetting::GetAllListenerStatErrors();
        Logging::info($errorStatus);
        $out = [];
        foreach ($errorStatus as $v) {
            $key = explode('_listener_stat_error', $v['keyname']);
            if ($v['value'] != 'OK') {
                $v['value'] = _('Please make sure admin user/password is correct on Settings->Streams page.');
            }
            $out[$key[0]] = $v['value'];
        }

        $this->view->errorStatus = $out;
        $this->view->date_form = $form;
    }

    public function showAction()
    {
        $CC_CONFIG = Config::getConfig();

        $request = $this->getRequest();
        $baseUrl = Application_Common_OsPath::getBaseDir();
        $headScript = $this->view->headScript();
        AirtimeTableView::injectTableJavaScriptDependencies($headScript, $baseUrl, $CC_CONFIG['airtime_version']);
        Zend_Layout::getMvcInstance()->assign('parent_page', 'Analytics');
        $this->view->headScript()->appendFile($baseUrl . 'js/timepicker/jquery.ui.timepicker.js?' . $CC_CONFIG['airtime_version'], 'text/javascript');
        $this->view->headScript()->appendFile($baseUrl . 'js/airtime/buttons/buttons.js?' . $CC_CONFIG['airtime_version'], 'text/javascript');
        $this->view->headScript()->appendFile($baseUrl . 'js/airtime/utilities/utilities.js?' . $CC_CONFIG['airtime_version'], 'text/javascript');
        $this->view->headScript()->appendFile($baseUrl . 'js/airtime/listenerstat/showlistenerstat.js?' . $CC_CONFIG['airtime_version'], 'text/javascript');

        $this->view->headLink()->appendStylesheet($baseUrl . 'css/datatables/css/ColVis.css?' . $CC_CONFIG['airtime_version']);
        $this->view->headLink()->appendStylesheet($baseUrl . 'css/datatables/css/dataTables.colReorder.min.css?' . $CC_CONFIG['airtime_version']);
        $this->view->headLink()->appendStylesheet($baseUrl . 'css/jquery.ui.timepicker.css?' . $CC_CONFIG['airtime_version']);
        $this->view->headLink()->appendStylesheet($baseUrl . 'css/show_analytics.css' . $CC_CONFIG['airtime_version']);

        $user = Application_Model_User::getCurrentUser();
        if ($user->isUserType([UTYPE_SUPERADMIN, UTYPE_ADMIN, UTYPE_PROGRAM_MANAGER])) {
            $this->view->showAllShows = true;
        }
        $data = [];
        $this->view->showData = $data;

        $form = new Application_Form_ShowListenerStat();

        [$startsDT, $endsDT] = Application_Common_HTTPHelper::getStartEndFromRequest($request);
        $userTimezone = new DateTimeZone(Application_Model_Preference::GetUserTimezone());
        $startsDT->setTimezone($userTimezone);
        $endsDT->setTimezone($userTimezone);
        $form->populate([
            'his_date_start' => $startsDT->format('Y-m-d'),
            'his_time_start' => $startsDT->format('H:i'),
            'his_date_end' => $endsDT->format('Y-m-d'),
            'his_time_end' => $endsDT->format('H:i'),
        ]);

        $this->view->date_form = $form;
    }

    public function getDataAction()
    {
        [$startsDT, $endsDT] = Application_Common_HTTPHelper::getStartEndFromRequest($this->getRequest());
        $data = Application_Model_ListenerStat::getDataPointsWithinRange(
            $startsDT->format(DEFAULT_TIMESTAMP_FORMAT),
            $endsDT->format(DEFAULT_TIMESTAMP_FORMAT)
        );
        $this->_helper->json->sendJson($data);
    }

    public function getShowDataAction()
    {
        [$startsDT, $endsDT] = Application_Common_HTTPHelper::getStartEndFromRequest($this->getRequest());
        $show_id = $this->getRequest()->getParam('show_id', null);
        $data = Application_Model_ListenerStat::getShowDataPointsWithinRange(
            $startsDT->format(DEFAULT_TIMESTAMP_FORMAT),
            $endsDT->format(DEFAULT_TIMESTAMP_FORMAT),
            $show_id
        );
        $this->_helper->json->sendJson($data);
    }

    public function getAllShowData()
    {
        [$startsDT, $endsDT] = Application_Common_HTTPHelper::getStartEndFromRequest($this->getRequest());

        return Application_Model_ListenerStat::getAllShowDataPointsWithinRange(
            $startsDT->format(DEFAULT_TIMESTAMP_FORMAT),
            $endsDT->format(DEFAULT_TIMESTAMP_FORMAT)
        );
    }

    public function getAllShowDataAction()
    {
        [$startsDT, $endsDT] = Application_Common_HTTPHelper::getStartEndFromRequest($this->getRequest());
        $show_id = $this->getRequest()->getParam('show_id', null);
        $data = Application_Model_ListenerStat::getAllShowDataPointsWithinRange(
            $startsDT->format(DEFAULT_TIMESTAMP_FORMAT),
            $endsDT->format(DEFAULT_TIMESTAMP_FORMAT)
        );
        $this->_helper->json->sendJson($data);
    }
}
