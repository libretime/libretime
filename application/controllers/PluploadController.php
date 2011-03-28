<?php

class PluploadController extends Zend_Controller_Action
{

    public function init()
    {
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
		$ajaxContext->addActionContext('upload', 'json')
                    ->addActionContext('upload-recorded', 'json')
				    ->initContext();
    }


    public function indexAction()
    {
        // action body
    }

    public function uploadAction()
    {
        $upload_dir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";
        $file = StoredFile::uploadFile($upload_dir);

		die('{"jsonrpc" : "2.0", "id" : '.$file->getId().' }');
    }

    public function pluploadAction()
    {
        $this->view->headScript()->appendFile('/js/plupload/plupload.full.min.js','text/javascript');
		$this->view->headScript()->appendFile('/js/plupload/jquery.plupload.queue.min.js','text/javascript');
		$this->view->headScript()->appendFile('/js/airtime/library/plupload.js','text/javascript');

		$this->view->headLink()->appendStylesheet('/css/plupload.queue.css');

    }


}





