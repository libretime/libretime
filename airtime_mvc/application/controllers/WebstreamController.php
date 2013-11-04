<?php

use Airtime\CcWebstream;
use Airtime\CcWebstreamQuery;

class WebstreamController extends Zend_Controller_Action
{
    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('new', 'json')
                    ->addActionContext('save', 'json')
                    ->addActionContext('edit', 'json')
                    ->addActionContext('delete', 'json')
                    ->initContext();
    }

    public function newAction()
    {
    	//clear the session in case an old playlist was open: CC-4196
    	Application_Model_Library::changePlaylist(null, null);
    	
    	$service = new Application_Service_WebstreamService();
    	$form = $service->makeWebstreamForm(null);
    	
    	$form->setDefaults(array(
    		'name' => 'Unititled Webstream',
    		'hours' => 0,
    		'mins' => 30,
    	));
    	
    	$this->view->action = "new";
    	$this->view->html = $form->render();
    }

    public function editAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam("id");
        
        $service = new Application_Service_WebstreamService();
    	$form = $service->makeWebstreamForm($id, true);

        Application_Model_Library::changePlaylist($id, "stream");

        $this->view->action = "edit";
        $this->view->html = $form->render();
    }

    public function deleteAction()
    {
        $request = $this->getRequest();
        $ids = $request->getParam("ids");

        Application_Model_Library::changePlaylist(null, null);
        
        $service = new Application_Service_WebstreamService();
        $service->deleteWebstreams($ids);

        $this->view->action = "delete";
        $this->view->html = $this->view->render('form/webstream.phtml');

    }

    public function saveAction()
    {
        $request = $this->getRequest();
        $parameters = array();
        
        foreach (array('id','hours', 'mins', 'name','description','url') as $p) {
            $parameters[$p] = trim($request->getParam($p));
        }
        
        Logging::info($parameters);
        
        $service = new Application_Service_WebstreamService();
        $form = $service->makeWebstreamForm(null);
        
        if ($form->isValid($parameters)) {
        	Logging::info("form is valid");
        	
        	$values = $form->getValues();
        	Logging::info($values);
        	
        	$ws = $service->saveWebstream($values);
        	
        	$this->view->statusMessage = "<div class='success'>"._("Webstream saved.")."</div>";
        	$this->view->streamId = $ws->getId();
        	$this->view->length = "00:05"; //$di->format("%Hh %Im");
        }
        else {
        	Logging::info("form is not valid");
        	
        	$this->view->statusMessage = "<div class='errors'>"._("Invalid form values.")."</div>";
        	$this->view->streamId = -1;
        	$this->view->errors = $form->getMessages();
        }
    }
}
