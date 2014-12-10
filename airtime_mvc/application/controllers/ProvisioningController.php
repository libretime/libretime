<?php

use Aws\S3\S3Client;

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
        
        //TODO - don't hardcode this here. maybe set it in $CC_CONFIG
        $cloudFiles = array(
            "amazon_S3" => array()
        );
        
        $CC_CONFIG = Config::getConfig();
        
        //TODO - dynamically select the storage backend credentials here
        $s3Client = S3Client::factory(array(
            'key' => $CC_CONFIG["amazon_S3"]['api_key'],
            'secret' => $CC_CONFIG["amazon_S3"]['api_key_secret'],
        ));
        
        $cloudFilePager = CloudFileQuery::create()
            ->paginate($page=1, $maxPerPage=1000);
        
        if ($cloudFilePager->haveToPaginate()) {
            $numPages = $cloudFilePager->getLastPage();
            $currentPage = 1;
            while ($currentPage <= $numPages) {
                $cloudFilePager = CloudFileQuery::create()
                    ->paginate($page = $currentPage, $maxPerPage = 1000);
                
                //TODO - delete objects here
                
                $currentPage += 1;
            }
        } else {
            //TODO - move this into function so it can be reused above
            foreach ($cloudFilePager->getResults() as $cloudFile) {
                array_push($cloudFiles[$cloudFile->getStorageBackend()],
                    array("Key" => $cloudFile->getResourceId()));

                $result = $s3Client->deleteObjects(array(
                    "Bucket" => $CC_CONFIG["amazon_S3"]["bucket"],
                    "Objects" => $cloudFiles["amazon_S3"]));
            }
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
