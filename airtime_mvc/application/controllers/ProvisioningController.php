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
     * Endpoint to change Airtime preferences remotely.
     * Mainly for use with the dashboard right now.
     */
    public function changeAction() {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if (!RestAuth::verifyAuth(true, false, $this)) {
            return;
        }

        try {
            // This is hacky and should be genericized
            if (isset($_POST['station_name'])) {
                Application_Model_Preference::SetStationName($_POST['station_name']);
            }
            if (isset($_POST['description'])) {
                Application_Model_Preference::SetStationDescription($_POST['description']);
            }
            if (isset($_POST['provisioning_status'])) {
                Application_Model_Preference::setProvisioningStatus($_POST['provisioning_status']);
            }
            if (isset($_POST['icecast_pass'])) {
                Application_Model_Preference::setDefaultIcecastPassword($_POST['icecast_pass']);
            }
        } catch (Exception $e) {
            $this->getResponse()
                ->setHttpResponseCode(400)
                ->appendBody("ERROR: " . $e->getMessage());
            Logging::error($e->getMessage());
            echo $e->getMessage() . PHP_EOL;
            return;
        }

        $this->getResponse()
            ->setHttpResponseCode(200)
            ->appendBody("OK");
    }

    /**
     * Delete the Airtime Pro station's files from Amazon S3
     *
     * FIXME: When we deploy this next time, we should ensure that
     *        this function can only be accessed with POST requests!
     */
    public function terminateAction()
    {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if (!RestAuth::verifyAuth(true, false, $this)) {
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
