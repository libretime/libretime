<?php

require_once 'Zend/Service/Amazon/S3.php';

class ProvisioningController extends Zend_Controller_Action
{
    public function init()
    {
    }
    /**
     * Delete the Airtime Pro station's files from Amazon S3
     */
    public function terminateAction()
    {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        if (!$this->verifyAPIKey()) {
            return;
        }
        
        $amazon_s3 = new Amazon_S3();
        $zend_s3 = $amazon_s3->getZendServiceAmazonS3();
        $bucket = $amazon_s3->getBucket();
        
        // Get all files stored on Amazon S3
        $cloudFiles = CloudFilesQuery::create()->find();
        foreach ($cloudFiles as $cloudFile) {
            $resource_id = $this->getResourceId();
            $amz_resource = utf8_encode("$bucket/$resource_id");
            $zend_s3->removeObject($amz_resource);
        }
    }
    
    private function verifyAPIKey()
    {
        // The API key is passed in via HTTP "basic authentication":
        // http://en.wikipedia.org/wiki/Basic_access_authentication
        
        $CC_CONFIG = Config::getConfig();
        
        // Decode the API key that was passed to us in the HTTP request.
        $authHeader = $this->getRequest()->getHeader("Authorization");
        $encodedRequestApiKey = substr($authHeader, strlen("Basic "));
        $encodedStoredApiKey = base64_encode($CC_CONFIG["apiKey"][0] . ":");
        
        if ($encodedRequestApiKey === $encodedStoredApiKey)
        {
            return true;
        }
        
        $this->getResponse()
            ->setHttpResponseCode(401)
            ->appendBody("ERROR: Incorrect API key.");
        
        return false;
    }
}
