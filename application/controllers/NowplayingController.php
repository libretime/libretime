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
    }

    public function getDataGridDataAction()
    {
        $this->view->entries = Application_Model_Nowplaying::GetDataGridData();
    }

    public function livestreamAction()
    {
        //use bare bones layout (no header bar or menu)
	$this->_helper->layout->setLayout('bare');
    }


}







