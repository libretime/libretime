<?php

declare(strict_types=1);

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
        $request = $this->getRequest();

        Zend_Layout::getMvcInstance()->assign('parent_page', 'Analytics');

        $this->view->headScript()->appendFile(Assets::url('js/flot/jquery.flot.js'), 'text/javascript');
        $this->view->headScript()->appendFile(Assets::url('js/flot/jquery.flot.crosshair.js'), 'text/javascript');
        $this->view->headScript()->appendFile(Assets::url('js/flot/jquery.flot.resize.js'), 'text/javascript');
        $this->view->headScript()->appendFile(Assets::url('js/airtime/listenerstat/listenerstat.js'), 'text/javascript');
        $this->view->headScript()->appendFile(Assets::url('js/timepicker/jquery.ui.timepicker.js'), 'text/javascript');
        $this->view->headScript()->appendFile(Assets::url('js/airtime/buttons/buttons.js'), 'text/javascript');
        $this->view->headScript()->appendFile(Assets::url('js/airtime/utilities/utilities.js'), 'text/javascript');
        $this->view->headLink()->appendStylesheet(Assets::url('css/jquery.ui.timepicker.css'));

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

        $errorStatus = Application_Model_Preference::GetAllListenerStatErrors();
        $out = [];
        foreach ($errorStatus as $v) {
            $key = explode(':', $v['keystr']);
            if ($v['valstr'] != 'OK') {
                $v['valstr'] = _('Please make sure admin user/password is correct on Settings->Streams page.');
            }
            $out['stream ' . $key[1]] = $v['valstr'];
        }

        $this->view->errorStatus = $out;
        $this->view->date_form = $form;
    }

    public function showAction()
    {
        $request = $this->getRequest();
        $headScript = $this->view->headScript();
        AirtimeTableView::injectTableJavaScriptDependencies($headScript);
        Zend_Layout::getMvcInstance()->assign('parent_page', 'Analytics');
        $this->view->headScript()->appendFile(Assets::url('js/timepicker/jquery.ui.timepicker.js'), 'text/javascript');
        $this->view->headScript()->appendFile(Assets::url('js/airtime/buttons/buttons.js'), 'text/javascript');
        $this->view->headScript()->appendFile(Assets::url('js/airtime/utilities/utilities.js'), 'text/javascript');
        $this->view->headScript()->appendFile(Assets::url('js/airtime/listenerstat/showlistenerstat.js'), 'text/javascript');

        $this->view->headLink()->appendStylesheet(Assets::url('css/datatables/css/ColVis.css'));
        $this->view->headLink()->appendStylesheet(Assets::url('css/datatables/css/dataTables.colReorder.min.css'));
        $this->view->headLink()->appendStylesheet(Assets::url('css/jquery.ui.timepicker.css'));
        $this->view->headLink()->appendStylesheet(Assets::url('css/show_analytics.css'));

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
