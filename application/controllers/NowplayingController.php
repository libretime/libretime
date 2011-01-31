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
        $this->view->headScript()->appendFile('/js/playlist/nowplayingdatagrid.js','text/javascript');
        $this->view->headScript()->appendFile('/js/playlist/helperfunctions.js','text/javascript');
		$this->view->headScript()->appendFile('/js/playlist/playlist.js','text/javascript');

        $this->view->headLink()->appendStylesheet('/css/pro_dropdown_3.css');
		$this->view->headLink()->appendStylesheet('/css/styles.css');
    }

    public function getDataGridDataAction()
    {
        //$this->view->entries = json_encode(Application_Model_Nowplaying::GetDataGridData());
        $this->view->entries = Application_Model_Nowplaying::GetDataGridData();
    }

    public function livestreamAction()
    {
        //use bare bones layout (no header bar or menu)
        $this->_helper->layout->setLayout('bare');
    }
}







