<?php

class NowplayingController extends Zend_Controller_Action
{

    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('get-data-grid-data', 'json')
                    ->initContext();
    }

    public function indexAction()
    {
        $request = $this->getRequest();
        $baseUrl = $request->getBaseUrl();

        $this->view->headScript()->appendFile($baseUrl.'/js/datatables/js/jquery.dataTables.min.js','text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/airtime/nowplaying/nowplayingdatagrid.js','text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/airtime/nowplaying/nowview.js','text/javascript');
        
        //popup if previous page was login
        $refer_sses = new Zend_Session_Namespace('referrer');
        if($refer_sses->referrer == 'login'){
        	//unset session
        	Zend_Session::namespaceUnset('referrer');
        	$this->view->headScript()->appendFile($baseUrl.'/js/airtime/nowplaying/register.js','text/javascript');
        }
    }

    public function getDataGridDataAction()
    {
        $viewType = $this->_request->getParam('view');
        $dateString = $this->_request->getParam('date');
        $this->view->entries = Application_Model_Nowplaying::GetDataGridData($viewType, $dateString);
        
    }

    public function livestreamAction()
    {
        //use bare bones layout (no header bar or menu)
        $this->_helper->layout->setLayout('bare');
    }

    public function dayViewAction()
    {
        $request = $this->getRequest();
        $baseUrl = $request->getBaseUrl();

        $this->view->headScript()->appendFile($baseUrl.'/js/datatables/js/jquery.dataTables.min.js','text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/airtime/nowplaying/nowplayingdatagrid.js','text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'/js/airtime/nowplaying/dayview.js','text/javascript');
    }
}









