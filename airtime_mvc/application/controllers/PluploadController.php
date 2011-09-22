<?php

class PluploadController extends Zend_Controller_Action
{

    public function init()
    {
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('upload', 'json')
		            ->addActionContext('copyfile', 'json')
				    ->initContext();
    }

    public function indexAction()
    {
        $request = $this->getRequest();
        $baseUrl = $request->getBaseUrl();

        $this->view->headScript()->appendFile($baseUrl.'/js/plupload/plupload.full.min.js','text/javascript');
		$this->view->headScript()->appendFile($baseUrl.'/js/plupload/jquery.plupload.queue.min.js','text/javascript');
		$this->view->headScript()->appendFile($baseUrl.'/js/airtime/library/plupload.js','text/javascript');

		$this->view->headLink()->appendStylesheet($baseUrl.'/css/plupload.queue.css');
    }

    public function uploadAction()
    {
        $upload_dir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";
        Application_Model_StoredFile::uploadFile($upload_dir);

		die('{"jsonrpc" : "2.0"}');
    }
    
    public function copyfileAction(){
        $upload_dir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";
        $filename = $this->_getParam('name');
        Application_Model_StoredFile::copyFileToStor($upload_dir, $filename);

        die('{"jsonrpc" : "2.0"}');
    }
}





