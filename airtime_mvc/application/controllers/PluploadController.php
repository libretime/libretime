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
        global $CC_CONFIG;
        
        $request = $this->getRequest();
        $baseUrl = $request->getBaseUrl();

        $this->view->headScript()->appendFile($baseUrl.'/js/plupload/plupload.full.min.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/plupload/jquery.plupload.queue.min.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/airtime/library/plupload.js?'.$CC_CONFIG['airtime_version'],'text/javascript');

        $this->view->headLink()->appendStylesheet($baseUrl.'/css/plupload.queue.css?'.$CC_CONFIG['airtime_version']);
    }

    public function uploadAction()
    {
        $upload_dir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";
        $tempFilePath = Application_Model_StoredFile::uploadFile($upload_dir);
        $tempFileName = basename($tempFilePath);

        die('{"jsonrpc" : "2.0", "tempfilepath" : "'.$tempFileName.'" }');
    }
    
    public function copyfileAction(){
        $upload_dir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";
        $filename = $this->_getParam('name');
        $tempname = $this->_getParam('tempname');
        $result = Application_Model_StoredFile::copyFileToStor($upload_dir, $filename, $tempname);
        if (!is_null($result))
           die('{"jsonrpc" : "2.0", "error" : {"code": '.$result['code'].', "message" : "'.$result['message'].'"}}');

        die('{"jsonrpc" : "2.0"}');
    }
}



