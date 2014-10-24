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

        $csrf_namespace = new Zend_Session_Namespace('csrf_namespace');
        $csrf_namespace->setExpirationSeconds(5*60*60);
        $csrf_namespace->authtoken = sha1(uniqid(rand(),1));

        $csrf_element = new Zend_Form_Element_Hidden('csrf');
        $csrf_element->setValue($csrf_namespace->authtoken)->setRequired('true')->removeDecorator('HtmlTag')->removeDecorator('Label');
        $csrf_form = new Zend_Form();
        $csrf_form->addElement($csrf_element);
        $this->view->form = $csrf_form;
    }

    public function uploadAction()
    {
        $current_namespace = new Zend_Session_Namespace('csrf_namespace');
        $observed_csrf_token = $this->_getParam('csrf_token');
        $expected_csrf_token = $current_namespace->authtoken;

        if($observed_csrf_token == $expected_csrf_token){
            $upload_dir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";
            $tempFilePath = Application_Model_StoredFile::uploadFile($upload_dir);
            $tempFileName = basename($tempFilePath);
         
            $this->_helper->json->sendJson(array("jsonrpc" => "2.0", "tempfilepath" => $tempFileName));
        }else{
            $this->_helper->json->sendJson(array("jsonrpc" => "2.0", "valid" => false, "error" => "CSRF token did not match."));
        }
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
