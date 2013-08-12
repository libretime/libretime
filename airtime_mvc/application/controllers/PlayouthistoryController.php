<?php

class PlayouthistoryController extends Zend_Controller_Action
{
    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext
            ->addActionContext('file-history-feed', 'json')
            ->addActionContext('item-history-feed', 'json')
            ->addActionContext('edit-file-item', 'json')
            ->addActionContext('create-list-item', 'json')
            ->addActionContext('edit-list-item', 'json')
            ->addActionContext('delete-list-item', 'json')
            ->addActionContext('update-list-item', 'json')
            ->addActionContext('update-file-item', 'json')
            ->addActionContext('create-template', 'json')
            ->addActionContext('update-template', 'json')
            ->addActionContext('delete-template', 'json')
            ->addActionContext('set-template-default', 'json')
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
        $this->view->headScript()->appendFile($baseUrl.'js/bootstrap-datetime/bootstrap-datetimepicker.min.js?'.$CC_CONFIG['airtime_version'],'text/javascript');

        $offset = date("Z") * -1;
        $this->view->headScript()->appendScript("var serverTimezoneOffset = {$offset}; //in seconds");
        $this->view->headScript()->appendFile($baseUrl.'js/timepicker/jquery.ui.timepicker.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/airtime/buttons/buttons.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/airtime/utilities/utilities.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/airtime/playouthistory/historytable.js?'.$CC_CONFIG['airtime_version'],'text/javascript');

        $this->view->headLink()->appendStylesheet($baseUrl.'js/datatables/plugin/TableTools/css/TableTools.css?'.$CC_CONFIG['airtime_version']);
        $this->view->headLink()->appendStylesheet($baseUrl.'css/jquery.ui.timepicker.css?'.$CC_CONFIG['airtime_version']);
        $this->view->headLink()->appendStylesheet($baseUrl.'css/playouthistory.css?'.$CC_CONFIG['airtime_version']);
        $this->view->headLink()->appendStylesheet($baseUrl.'css/bootstrap-datetimepicker.min.css?'.$CC_CONFIG['airtime_version']);
        
        //set datatables columns for display of data.
        $historyService = new Application_Service_HistoryService();
        $columns = json_encode($historyService->getDatatablesLogSheetColumns());
        $script = "localStorage.setItem( 'datatables-historyitem-aoColumns', JSON.stringify($columns) ); ";
        
        $columns = json_encode($historyService->getDatatablesFileSummaryColumns());
        $script.= "localStorage.setItem( 'datatables-historyfile-aoColumns', JSON.stringify($columns) );";
        $this->view->headScript()->appendScript($script);
    }

    public function fileHistoryFeedAction()
    {
    	try {
	        $request = $this->getRequest();
	        $current_time = time();
	
	        $params = $request->getParams();
	
	        $starts_epoch = $request->getParam("start", $current_time - (60*60*24));
	        $ends_epoch = $request->getParam("end", $current_time);
	
	        $startsDT = DateTime::createFromFormat("U", $starts_epoch, new DateTimeZone("UTC"));
	        $endsDT = DateTime::createFromFormat("U", $ends_epoch, new DateTimeZone("UTC"));
	
	        $historyService = new Application_Service_HistoryService();
	        $r = $historyService->getFileSummaryData($startsDT, $endsDT, $params);
	
	        $this->view->sEcho = $r["sEcho"];
	        $this->view->iTotalDisplayRecords = $r["iTotalDisplayRecords"];
	        $this->view->iTotalRecords = $r["iTotalRecords"];
	        $this->view->history = $r["history"];
        }
        catch (Exception $e) {
        	Logging::info($e);
        	Logging::info($e->getMessage());
        }
    }

    public function itemHistoryFeedAction()
    {
    	try {
	        $request = $this->getRequest();
	        $current_time = time();
	
	        $params = $request->getParams();
	
	        $starts_epoch = $request->getParam("start", $current_time - (60*60*24));
	        $ends_epoch = $request->getParam("end", $current_time);
	
	        $startsDT = DateTime::createFromFormat("U", $starts_epoch, new DateTimeZone("UTC"));
	        $endsDT = DateTime::createFromFormat("U", $ends_epoch, new DateTimeZone("UTC"));
	
	        $historyService = new Application_Service_HistoryService();
	        //$r = $historyService->getListView($startsDT, $endsDT, $params);
	        $r = $historyService->getPlayedItemData($startsDT, $endsDT, $params);
	
	        $this->view->sEcho = $r["sEcho"];
	        $this->view->iTotalDisplayRecords = $r["iTotalDisplayRecords"];
	        $this->view->iTotalRecords = $r["iTotalRecords"];
	        $this->view->history = $r["history"];
    	}
    	catch (Exception $e) {
    		Logging::info($e);
    		Logging::info($e->getMessage());
    	}
    }

    public function editFileItemAction()
    {
    	$file_id = $this->_getParam('id');

    	$historyService = new Application_Service_HistoryService();
    	$form = $historyService->makeHistoryFileForm($file_id);

    	$this->view->form = $form;
    	$this->view->dialog = $this->view->render('playouthistory/dialog.phtml');

    	unset($this->view->form);
    }

    public function createListItemAction()
    {
    	try {
	        $request = $this->getRequest();
	        $params = $request->getPost();
	        Logging::info($params);
	
	        $historyService = new Application_Service_HistoryService();
	        $json = $historyService->createPlayedItem($params);
	        
	        $this->_helper->json->sendJson($json);
    	}
        catch (Exception $e) {
        	Logging::info($e);
        	Logging::info($e->getMessage());
        }
    }

    public function editListItemAction()
    {
        $id = $this->_getParam('id', null);
        Logging::info("Id is: $id");
        
        $populate = isset($id) ? true : false;

        $historyService = new Application_Service_HistoryService();
        $form = $historyService->makeHistoryItemForm($id, $populate);

        $this->view->form = $form;
        $this->view->dialog = $this->view->render('playouthistory/dialog.phtml');

        unset($this->view->form);
    }

    public function deleteListItemAction()
    {
    	$history_id = $this->_getParam('id');

    	$historyService = new Application_Service_HistoryService();
    	$historyService->deletePlayedItem($history_id);
    }

    public function updateListItemAction()
    {
    	try {
	    	$request = $this->getRequest();
	    	$params = $request->getPost();
	    	Logging::info($params);
	
	    	$historyService = new Application_Service_HistoryService();
	    	$json = $historyService->editPlayedItem($params);
	
	    	$this->_helper->json->sendJson($json);
    	}
    	catch (Exception $e) {
    		Logging::info($e);
    		Logging::info($e->getMessage());
    	}
    }

    public function updateFileItemAction()
    {
        $request = $this->getRequest();
        $params = $request->getPost();
        Logging::info($params);

    	$historyService = new Application_Service_HistoryService();
    	$json = $historyService->editPlayedFile($params);

    	$this->_helper->json->sendJson($json);
    }

    public function templateAction()
    {
    	$CC_CONFIG = Config::getConfig();
    	$baseUrl = Application_Common_OsPath::getBaseDir();
    	
    	$this->view->headScript()->appendFile($baseUrl.'js/airtime/playouthistory/template.js?'.$CC_CONFIG['airtime_version'],'text/javascript');

    	$historyService = new Application_Service_HistoryService();
    	$this->view->template_list = $historyService->getListItemTemplates();
    	$this->view->template_file = $historyService->getFileTemplates();
    	$this->view->configured = $historyService->getConfiguredTemplateIds();
    }

    public function configureTemplateAction() {
    	
    	$CC_CONFIG = Config::getConfig();
    	$baseUrl = Application_Common_OsPath::getBaseDir();
    	
    	$this->view->headScript()->appendFile($baseUrl.'js/airtime/playouthistory/configuretemplate.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
    	 
    	try {
	    	
	    	$templateId = $this->_getParam('id');
	    	
	    	$historyService = new Application_Service_HistoryService();	        
	    	$template = $historyService->loadTemplate($templateId);
	    	
	    	$templateType = $template["type"];
	    	$supportedTypes = $historyService->getSupportedTemplateTypes();
	    	
	    	if (!in_array($templateType, $supportedTypes)) {
	    		throw new Exception("Error: $templateType is not supported.");
	    	}
	    	
	    	$getMandatoryFields = "mandatory".ucfirst($templateType)."Fields";
	        $mandatoryFields = $historyService->$getMandatoryFields();
	        
	        $this->view->template_id = $templateId;
	        $this->view->template_name = $template["name"];
	        $this->view->template_fields = $template["fields"];
	        $this->view->template_type = $templateType;
	        $this->view->fileMD = $historyService->getFileMetadataTypes();
	        $this->view->fields = $historyService->getFieldTypes();
	        $this->view->required_fields = $mandatoryFields;
	        $this->view->configured = $historyService->getConfiguredTemplateIds();
        }
        catch (Exception $e) {
        	Logging::info("Error?");
        	Logging::info($e);
        	Logging::info($e->getMessage());
        	
        	$this->_forward('template', 'playouthistory');
        }
    }

    public function createTemplateAction()
    {
    	$templateType = $this->_getParam('type', null);
    	
    	$request = $this->getRequest();
    	$params = $request->getPost();
    	
    	try {
    		$historyService = new Application_Service_HistoryService();
    		$supportedTypes = $historyService->getSupportedTemplateTypes();
    		
    		if (!in_array($templateType, $supportedTypes)) {
    			throw new Exception("Error: $templateType is not supported.");
    		}
    			
    		$id = $historyService->createTemplate($params);
    		
    		$this->view->url = $this->view->baseUrl("Playouthistory/configure-template/id/{$id}");
    	}
    	catch (Exception $e) {
    		Logging::info($e);
    		Logging::info($e->getMessage());
    		
    		$this->view->error = $e->getMessage();
    	}
    }
    
    public function setTemplateDefaultAction()
    {
    	$templateId = $this->_getParam('id', null);
    	
    	try {
    		$historyService = new Application_Service_HistoryService();
    		$historyService->setConfiguredTemplate($templateId);
    	}
    	catch (Exception $e) {
    		Logging::info($e);
    		Logging::info($e->getMessage());
    	}
    }

    public function updateTemplateAction()
    { 
    	$templateId = $this->_getParam('id', null);
    	$name = $this->_getParam('name', null);
    	$fields = $this->_getParam('fields', array());
    	 
    	try {
    		$historyService = new Application_Service_HistoryService();
    		$historyService->updateItemTemplate($templateId, $name, $fields);
    	}
    	catch (Exception $e) {
    		Logging::info($e);
    		Logging::info($e->getMessage());
    	}
    }

    public function deleteTemplateAction()
    {
    	$templateId = $this->_getParam('id');
    	
    	try {
    		$historyService = new Application_Service_HistoryService();
    		$historyService->deleteTemplate($templateId);
    	}
    	catch (Exception $e) {
    		Logging::info($e);
    		Logging::info($e->getMessage());
    	}
    }
}
