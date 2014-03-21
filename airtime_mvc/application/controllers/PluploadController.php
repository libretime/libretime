<?php

class PluploadController extends Zend_Controller_Action
{
    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('upload',            'json')
                    ->addActionContext('recent-uploads',     'json')
                    ->initContext();
    }

    public function indexAction()
    {
        $CC_CONFIG = Config::getConfig();

        $baseUrl = Application_Common_OsPath::getBaseDir();
        $locale = Application_Model_Preference::GetLocale();

        $this->view->headScript()->appendFile($baseUrl.'js/datatables/js/jquery.dataTables.js?'.$CC_CONFIG['airtime_version'], 'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/plupload/plupload.full.min.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/plupload/jquery.plupload.queue.min.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/airtime/library/plupload.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
        $this->view->headScript()->appendFile($baseUrl.'js/plupload/i18n/'.$locale.'.js?'.$CC_CONFIG['airtime_version'],'text/javascript');

        $this->view->headLink()->appendStylesheet($baseUrl.'css/plupload.queue.css?'.$CC_CONFIG['airtime_version']);
        $this->view->headLink()->appendStylesheet($baseUrl.'css/addmedia.css?'.$CC_CONFIG['airtime_version']);
    }

    public function uploadAction()
    {
        $upload_dir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";
        $tempFilePath = Application_Model_StoredFile::uploadFile($upload_dir);
        $tempFileName = basename($tempFilePath);

        $this->_helper->json->sendJson(array("jsonrpc" => "2.0", "tempfilepath" => $tempFileName));
    }
    
    public function recentUploadsAction()
    {
        if (isset($_GET['uploadFilter'])) {
            $filter = $_GET['uploadFilter'];
        } else {
            $filter = "all";
        }
        
        $limit = isset($_GET['iDisplayLength']) ? $_GET['iDisplayLength'] : 10;
        $rowStart = isset($_GET['iDisplayStart']) ? $_GET['iDisplayStart'] : 0;
        
        $recentUploadsQuery = CcFilesQuery::create()->filterByDbUtime(array('min' => time() - 30 * 24 * 60 * 60))
                            ->orderByDbUtime(Criteria::DESC)
                            ->offset($rowStart)
                            ->limit($limit);
        
        if ($filter == "pending") {
            $recentUploadsQuery->filterByDbImportStatus("1");
        } else if ($filter == "failed") {
            $recentUploadsQuery->filterByDbImportStatus(array('min' => 100));
        }
        $recentUploads = $recentUploadsQuery->find();
        
        $numRecentUploads = $limit;
        $numTotalRecentUploads = CcFilesQuery::create()->filterByDbUtime(array('min' => time() - 30 * 24 * 60 * 60))
            ->count();
        
        //$this->_helper->json->sendJson(array("jsonrpc" => "2.0", "tempfilepath" => $tempFileName));
        
        $uploadsArray = array();
        
        foreach ($recentUploads as $upload)
        {
            $upload->toArray(BasePeer::TYPE_FIELDNAME);
            //array_push($uploadsArray, $upload); //TODO: $this->sanitizeResponse($upload));
            
            //$this->_helper->json->sendJson($upload->asJson());
            //TODO: Invoke sanitization here
            array_push($uploadsArray, $upload->toArray(BasePeer::TYPE_FIELDNAME));
        }
        

        $this->view->sEcho = intval($this->getRequest()->getParam('sEcho'));
        $this->view->iTotalDisplayRecords = $numTotalRecentUploads;
        //$this->view->iTotalDisplayRecords = $numRecentUploads; //$r["iTotalDisplayRecords"];
        $this->view->iTotalRecords = $numTotalRecentUploads; //$r["iTotalRecords"];
        $this->view->files = $uploadsArray; //$r["aaData"];
    }
}
