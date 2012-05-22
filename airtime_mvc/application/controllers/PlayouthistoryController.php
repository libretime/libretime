<?php

class PlayoutHistoryController extends Zend_Controller_Action
{
	public function init()
	{
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext
			->addActionContext('playout-history-feed', 'json')
			->initContext();
	}
	
	public function indexAction()
	{
		global $CC_CONFIG;
		
		$request = $this->getRequest();
        $baseUrl = $request->getBaseUrl();

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
		
		$this->view->headScript()->appendFile($baseUrl.'/js/contextmenu/jquery.contextMenu.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
		$this->view->headScript()->appendFile($baseUrl.'/js/datatables/js/jquery.dataTables.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
		$this->view->headScript()->appendFile($baseUrl.'/js/datatables/plugin/dataTables.pluginAPI.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
		$this->view->headScript()->appendFile($baseUrl.'/js/datatables/plugin/dataTables.fnSetFilteringDelay.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
		$this->view->headScript()->appendFile($baseUrl.'/js/datatables/plugin/TableTools/js/ZeroClipboard.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
		$this->view->headScript()->appendFile($baseUrl.'/js/datatables/plugin/TableTools/js/TableTools.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
		
		$offset = date("Z") * -1;
		$this->view->headScript()->appendScript("var serverTimezoneOffset = {$offset}; //in seconds");
		$this->view->headScript()->appendFile($baseUrl.'/js/timepicker/jquery.ui.timepicker.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
		$this->view->headScript()->appendFile($baseUrl.'/js/airtime/buttons/buttons.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
		$this->view->headScript()->appendFile($baseUrl.'/js/airtime/utilities/utilities.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
		$this->view->headScript()->appendFile($baseUrl.'/js/airtime/playouthistory/historytable.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
		
		$this->view->headLink()->appendStylesheet($baseUrl.'/js/datatables/plugin/TableTools/css/TableTools.css?'.$CC_CONFIG['airtime_version']);
		$this->view->headLink()->appendStylesheet($baseUrl.'/css/jquery.ui.timepicker.css?'.$CC_CONFIG['airtime_version']);
		$this->view->headLink()->appendStylesheet($baseUrl.'/css/playouthistory.css?'.$CC_CONFIG['airtime_version']);
	}
	
	public function playoutHistoryFeedAction()
	{
		$request = $this->getRequest();
		$current_time = time();
		
		$params = $request->getParams();
		
		$starts_epoch = $request->getParam("start", $current_time - (60*60*24));
		$ends_epoch = $request->getParam("end", $current_time);
		
		$startsDT = DateTime::createFromFormat("U", $starts_epoch, new DateTimeZone("UTC"));
		$endsDT = DateTime::createFromFormat("U", $ends_epoch, new DateTimeZone("UTC"));
		
		Logging::log("history starts {$startsDT->format("Y-m-d H:i:s")}");
		Logging::log("history ends {$endsDT->format("Y-m-d H:i:s")}");
		
		$history = new Application_Model_PlayoutHistory($startsDT, $endsDT, $params);
		
		$r = $history->getItems();
		
		$this->view->sEcho = $r["sEcho"];
		$this->view->iTotalDisplayRecords = $r["iTotalDisplayRecords"];
		$this->view->iTotalRecords = $r["iTotalRecords"];
		$this->view->history = $r["history"];
	}
}