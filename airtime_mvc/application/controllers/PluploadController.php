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
        $CC_CONFIG = Config::getConfig();

        $baseUrl = Application_Common_OsPath::getBaseDir();
        $locale = Application_Model_Preference::GetLocale();

        $this->view->headScript()->appendFile($baseUrl.'js/plupload/plupload.full.min.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/plupload/jquery.plupload.queue.min.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/airtime/library/plupload.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/plupload/i18n/'.$locale.'.js?'.$CC_CONFIG['airtime_version'],'text/javascript');

        $this->view->headLink()->appendStylesheet($baseUrl.'css/plupload.queue.css?'.$CC_CONFIG['airtime_version']);
    }

    public function uploadAction()
    {
        $upload_dir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";
        $tempFilePath = Application_Model_StoredFile::uploadFile($upload_dir);
        $tempFileName = basename($tempFilePath);

        $this->_helper->json->sendJson(array("jsonrpc" => "2.0", "tempfilepath" => $tempFileName));
    }

    public function copyfileAction()
    {
        $upload_dir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";
        $filename = $this->_getParam('name');
        $tempname = $this->_getParam('tempname');
        $result = Application_Model_StoredFile::copyFileToStor($upload_dir,
            $filename, $tempname);
        if (!is_null($result))
           $this->_helper->json->sendJson(array("jsonrpc" => "2.0", "error" => $result));

        $this->_helper->json->sendJson(array("jsonrpc" => "2.0"));
    }
}
