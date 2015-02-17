<?php

require_once 'ProxyStorageBackend.php';

use Aws\S3\S3Client;

class ProvisioningController extends Zend_Controller_Action
{

    public function init()
    {
    }

    /**
     *
     *  The "create action" is in ProvisioningHelper because it needs to have no dependency on Zend,
     *  since when we bootstrap Zend, we already need the database set up and working (Bootstrap.php is a mess).
     *
     */

    /**
     * Delete the Airtime Pro station's files from Amazon S3
     */
    public function terminateAction()
    {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if (!RestAuth::verifyAuth(true, true, $this)) {
            return;
        }
        
        $CC_CONFIG = Config::getConfig();
        
        foreach ($CC_CONFIG["supportedStorageBackends"] as $storageBackend) {
            $proxyStorageBackend = new ProxyStorageBackend($storageBackend);
            $proxyStorageBackend->deleteAllCloudFileObjects();
        }
        
        $this->getResponse()
            ->setHttpResponseCode(200)
            ->appendBody("OK");
    }

}
