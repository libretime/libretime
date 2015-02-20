<?php

class Rest_MediaController extends Zend_Rest_Controller
{
    const IMPORT_STATUS_SUCCESS = 0;
    const IMPORT_STATUS_PENDING = 1;
    const IMPORT_STATUS_FAILED = 2;
    

    public function init()
    {
        $this->view->layout()->disableLayout();
    }
    
    public function indexAction()
    {
        $files_array = array();
        foreach (CcFilesQuery::create()->find() as $file)
        {
            array_push($files_array, CcFiles::sanitizeResponse($file));
        }
        
        $this->getResponse()
        ->setHttpResponseCode(200)
        ->appendBody(json_encode($files_array));
        
        /** TODO: Use this simpler code instead after we upgrade to Propel 1.7 (Airtime 2.6.x branch):
        $this->getResponse()
            ->setHttpResponseCode(200)
            ->appendBody(json_encode(CcFilesQuery::create()->find()->toArray(BasePeer::TYPE_FIELDNAME)));
        */
    }

    public function downloadAction()
    {
        $id = $this->getId();
        if (!$id) {
            return;
        }

        try
        {
            $this->getResponse()
                ->setHttpResponseCode(200)
                ->appendBody($this->_redirect(CcFiles::getDownloadUrl($id)));
        }
        catch (FileNotFoundException $e) {
            $this->fileNotFoundResponse();
            Logging::error($e->getMessage());
        }
        catch (Exception $e) {
            $this->unknownErrorResponse();
            Logging::error($e->getMessage());
        }
    }
    
    public function getAction()
    {
        $id = $this->getId();
        if (!$id) {
            return;
        }

        try {
            $this->getResponse()
                ->setHttpResponseCode(200)
                ->appendBody(json_encode(CcFiles::getSantiziedFileById($id)));
        }
        catch (FileNotFoundException $e) {
            $this->fileNotFoundResponse();
            Logging::error($e->getMessage());
        }
        catch (Exception $e) {
            $this->unknownErrorResponse();
            Logging::error($e->getMessage());
        }
    }
    
    public function postAction()
    {
        //If we do get an ID on a POST, then that doesn't make any sense
        //since POST is only for creating.
        if ($id = $this->_getParam('id', false)) {
            $resp = $this->getResponse();
            $resp->setHttpResponseCode(400);
            $resp->appendBody("ERROR: ID should not be specified when using POST. POST is only used for file creation, and an ID will be chosen by Airtime"); 
            return;
        }

        try {
            $sanitizedFile = CcFiles::createFromUpload($this->getRequest()->getPost());
            $this->getResponse()
                ->setHttpResponseCode(201)
                ->appendBody(json_encode($sanitizedFile));
        }
        catch (InvalidMetadataException $e) {
            $this->invalidDataResponse();
            Logging::error($e->getMessage());
        }
        catch (OverDiskQuotaException $e) {
            $this->getResponse()
                ->setHttpResponseCode(400)
                ->appendBody("ERROR: Disk Quota reached.");
        }
        catch (Exception $e) {
            $this->unknownErrorResponse();
            Logging::error($e->getMessage());
        }
    }

    public function putAction()
    {
        $id = $this->getId();
        if (!$id) {
            return;
        }

        try {
            $requestData = json_decode($this->getRequest()->getRawBody(), true);
            $sanitizedFile = CcFiles::updateFromArray($id, $requestData);
            $this->getResponse()
                ->setHttpResponseCode(201)
                ->appendBody(json_encode($sanitizedFile));
        }
        catch (InvalidMetadataException $e) {
            $this->invalidDataResponse();
            Logging::error($e->getMessage());
        }
        catch (FileNotFoundException $e) {
            $this->fileNotFoundResponse();
            Logging::error($e->getMessage());
        }
        catch (Exception $e) {
            $this->unknownErrorResponse();
            Logging::error($e->getMessage());
        }
    }

    public function deleteAction()
    {
        $id = $this->getId();
        if (!$id) {
            return;
        }
        try {
            CcFiles::deleteById($id);
            $this->getResponse()
                ->setHttpResponseCode(204);
        }
        catch (FileNotFoundException $e) {
            $this->fileNotFoundResponse();
            Logging::error($e->getMessage());
        }
        catch (Exception $e) {
            $this->unknownErrorResponse();
            Logging::error($e->getMessage());
        }
    }

    private function getId()
    {
        if (!$id = $this->_getParam('id', false)) {
            $resp = $this->getResponse();
            $resp->setHttpResponseCode(400);
            $resp->appendBody("ERROR: No file ID specified."); 
            return false;
        } 
        return $id;
    }

    private function fileNotFoundResponse()
    {
        $resp = $this->getResponse();
        $resp->setHttpResponseCode(404);
        $resp->appendBody("ERROR: Media not found."); 
    }
    
    private function importFailedResponse()
    {
        $resp = $this->getResponse();
        $resp->setHttpResponseCode(200);
        $resp->appendBody("ERROR: Import Failed.");
    }

    private function unknownErrorResponse()
    {
        $resp = $this->getResponse();
        $resp->setHttpResponseCode(400);
        $resp->appendBody("An unknown error occurred.");
    }
}

