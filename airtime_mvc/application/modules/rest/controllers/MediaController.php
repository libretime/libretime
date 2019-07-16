<?php

class Rest_MediaController extends Zend_Rest_Controller
{
    public function init()
    {
        $this->view->layout()->disableLayout();

        // Remove reliance on .phtml files to render requests
        $this->_helper->viewRenderer->setNoRender(true);
    }

    /**
     * headAction is needed as it is defined as an abstract function in the base controller
     *
     * @return void
     */
    public function headAction()
    {
        Logging::info("HEAD action received");
    }
    
    public function indexAction()
    {
        $totalFileCount = CcFilesQuery::create()->count();

        // Check if offset and limit were sent with request.
        // Default limit to zero and offset to $totalFileCount
        $offset = $this->_getParam('offset', 0);
        $limit = $this->_getParam('limit', $totalFileCount);

        //Sorting parameters
        $sortColumn = $this->_getParam('sort', CcFilesPeer::ID);
        $sortDir = $this->_getParam('sort_dir', Criteria::ASC);

        $query = CcFilesQuery::create()
            ->filterByDbHidden(false)
            ->filterByDbFileExists(true)
            ->filterByDbImportStatus(0)
            ->setLimit($limit)
            ->setOffset($offset)
            ->orderBy($sortColumn, $sortDir);
            //->orderByDbId();


        $queryCount = $query->count();
        $queryResult = $query->find();

        $files_array = array();
        foreach ($queryResult as $file)
        {
            array_push($files_array, CcFiles::sanitizeResponse($file));
        }

        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('X-TOTAL-COUNT', $totalFileCount)
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

        // In case the download fails
        $counterIncremented = false;
        try {
            $this->getResponse()
                ->setHttpResponseCode(200);
            $inline = false;
            // SAAS-1081 - download counter for station podcast downloads
            if ($key = $this->getRequest()->getParam("download_key", false)) {
                Application_Model_Preference::incrementStationPodcastDownloadCounter();
                $counterIncremented = true;
            }
            Application_Service_MediaService::streamFileDownload($id, $inline);
        }
        catch (LibreTimeFileNotFoundException $e) {
            $this->fileNotFoundResponse();
            Logging::error($e->getMessage());
        }
        catch (Exception $e) {
            if ($counterIncremented) Application_Model_Preference::decrementStationPodcastDownloadCounter();
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
                ->appendBody(json_encode(CcFiles::getSanitizedFileById($id)));
        }
        catch (LibreTimeFileNotFoundException $e) {
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
            // REST uploads are not from Zend_Form, hence we handle them using Zend_File_transfer directly
            // we need to specify an explicit adapter since autodetection broke in php 7.2
            $upload = new Zend_File_Transfer('Zend_File_Transfer_Adapter_Http');
            // this error should not really get hit, letting the user know if it does is nice for debugging
            // see: https://github.com/LibreTime/libretime/issues/3#issuecomment-281143417
            if (!$upload->isValid('file')) {
                throw new Exception("invalid file uploaded");
            }
            $fileInfo = $upload->getFileInfo('file');
            // this should have more info on any actual faults detected by php
            if ($fileInfo['file']['error']) {
                throw new Exception(sprintf('File upload error: %s', $fileInfo['file']['error']));
            }
            $sanitizedFile = CcFiles::createFromUpload($fileInfo);
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
            $this->serviceUnavailableResponse();
            Logging::error($e->getMessage() . "\n" . $e->getTraceAsString());
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
        catch (LibreTimeFileNotFoundException $e) {
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
        catch (LibreTimeFileNotFoundException $e) {
            $this->fileNotFoundResponse();
            Logging::error($e->getMessage());
        }
        catch (Exception $e) {
            $this->unknownErrorResponse();
            Logging::error($e->getMessage());
        }
    }

    /**
     * Publish endpoint for individual media items
     */
    public function publishAction() {
        $id = $this->getId();
        try {
            // Is there a better way to do this?
            $data = json_decode($this->getRequest()->getRawBody(), true)["sources"];
            Application_Service_PublishService::publish($id, $data);
            $this->getResponse()
                ->setHttpResponseCode(200);
        } catch (Exception $e) {
            $this->unknownErrorResponse();
            Logging::error($e->getMessage());
        }
    }

    public function publishSourcesAction() {
        $id = $this->_getParam('id', false);
        $sources = Application_Service_PublishService::getSourceLists($id);
        $this->getResponse()
            ->setHttpResponseCode(200)
            ->appendBody(json_encode($sources));

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

    private function serviceUnavailableResponse()
    {
        $resp = $this->getResponse();
        $resp->setHttpResponseCode(400);
        $resp->appendBody("An error occurred while processing your upload. Please try again in a few minutes.");
    }
}

