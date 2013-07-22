<?php

class PlayouthistoryController extends Zend_Controller_Action
{
    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext
            ->addActionContext('aggregate-history-feed', 'json')
            ->addActionContext('item-history-feed', 'json')
            ->addActionContext('edit-aggregate-item', 'json')
            ->addActionContext('create-list-item', 'json')
            ->addActionContext('edit-list-item', 'json')
            ->addActionContext('update-aggregate-item', 'json')
            ->initContext();
        }

    public function indexAction()
    {
        $CC_CONFIG = Config::getConfig();

        $request = $this->getRequest();

        $baseUrl = Application_Common_OsPath::getBaseDir();

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

        $this->view->date_form = $form;

        $this->view->headScript()->appendFile($baseUrl.'js/contextmenu/jquery.contextMenu.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/datatables/js/jquery.dataTables.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/datatables/plugin/dataTables.pluginAPI.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/datatables/plugin/dataTables.fnSetFilteringDelay.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/datatables/plugin/TableTools/js/ZeroClipboard.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/datatables/plugin/TableTools/js/TableTools.js?'.$CC_CONFIG['airtime_version'],'text/javascript');

        $offset = date("Z") * -1;
        $this->view->headScript()->appendScript("var serverTimezoneOffset = {$offset}; //in seconds");
        $this->view->headScript()->appendFile($baseUrl.'js/timepicker/jquery.ui.timepicker.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/airtime/buttons/buttons.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/airtime/utilities/utilities.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/airtime/playouthistory/historytable.js?'.$CC_CONFIG['airtime_version'],'text/javascript');

        $this->view->headLink()->appendStylesheet($baseUrl.'js/datatables/plugin/TableTools/css/TableTools.css?'.$CC_CONFIG['airtime_version']);
        $this->view->headLink()->appendStylesheet($baseUrl.'css/jquery.ui.timepicker.css?'.$CC_CONFIG['airtime_version']);
        $this->view->headLink()->appendStylesheet($baseUrl.'css/playouthistory.css?'.$CC_CONFIG['airtime_version']);
    }

    public function aggregateHistoryFeedAction()
    {
        $request = $this->getRequest();
        $current_time = time();

        $params = $request->getParams();

        $starts_epoch = $request->getParam("start", $current_time - (60*60*24));
        $ends_epoch = $request->getParam("end", $current_time);

        $startsDT = DateTime::createFromFormat("U", $starts_epoch, new DateTimeZone("UTC"));
        $endsDT = DateTime::createFromFormat("U", $ends_epoch, new DateTimeZone("UTC"));

        $historyService = new Application_Service_HistoryService();
        $r = $historyService->getAggregateView($startsDT, $endsDT, $params);

        $this->view->sEcho = $r["sEcho"];
        $this->view->iTotalDisplayRecords = $r["iTotalDisplayRecords"];
        $this->view->iTotalRecords = $r["iTotalRecords"];
        $this->view->history = $r["history"];
    }

    public function itemHistoryFeedAction()
    {
        $request = $this->getRequest();
        $current_time = time();

        $params = $request->getParams();

        $starts_epoch = $request->getParam("start", $current_time - (60*60*24));
        $ends_epoch = $request->getParam("end", $current_time);

        $startsDT = DateTime::createFromFormat("U", $starts_epoch, new DateTimeZone("UTC"));
        $endsDT = DateTime::createFromFormat("U", $ends_epoch, new DateTimeZone("UTC"));

        $historyService = new Application_Service_HistoryService();
        $r = $historyService->getListView($startsDT, $endsDT, $params);

        $this->view->sEcho = $r["sEcho"];
        $this->view->iTotalDisplayRecords = $r["iTotalDisplayRecords"];
        $this->view->iTotalRecords = $r["iTotalRecords"];
        $this->view->history = $r["history"];
    }

    public function editAggregateItemAction()
    {
    	$file_id = $this->_getParam('id');

    	$historyService = new Application_Service_HistoryService();
    	$form = $historyService->makeHistoryFileForm($file_id);

    	$this->view->form = $form;
    	$this->view->dialog = $this->view->render('form/edit-history-file.phtml');

    	unset($this->view->form);
    }

    public function createListItemAction()
    {
        $request = $this->getRequest();
        $params = $request->getPost();
        Logging::info($params);

        $historyService = new Application_Service_HistoryService();
        $historyService->createPlayedItem($params);
    }

    public function editListItemAction()
    {
        $file_id = $this->_getParam('id');

        $historyService = new Application_Service_HistoryService();
        $form = $historyService->makeHistoryFileForm($file_id);

        $this->view->form = $form;
        $this->view->dialog = $this->view->render('form/edit-history-file.phtml');

        unset($this->view->form);
    }

    public function updateAggregateItemAction()
    {
        $request = $this->getRequest();
        $params = $request->getPost();
        Logging::info($params);

    	$historyService = new Application_Service_HistoryService();
    	$json = $historyService->editPlayedFile($params);

    	$this->view->data = $json;
    }
}
