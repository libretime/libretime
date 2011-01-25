<?php

class NowplayingController extends Zend_Controller_Action
{

    public function init()
    {
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('get-data-grid-data', 'json')->initContext();
    }

    public function indexAction()
    {
		$this->view->headScript()->appendFile('/js/datatables/js/jquery.dataTables.min.js','text/javascript');
		
		
		$this->view->headLink()->appendStylesheet('/css/datatables/css/demo_page.css');
		$this->view->headLink()->appendStylesheet('/css/datatables/css/demo_table.css');
		
		//$this->_helper->viewRenderer->setResponseSegment('nowplaying');
    }

    public function getDataGridDataAction()
    {
		$this->view->entries = Application_Model_Nowplaying::GetDataGridData();
    }


}





