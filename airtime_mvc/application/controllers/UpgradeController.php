<?php

class UpgradeController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        if (!$this->verifyAuth()) {
            return;
        }

        $didWePerformAnUpgrade = false;
        try {
            $upgradeManager = new UpgradeManager();
            $didWePerformAnUpgrade = $upgradeManager->doUpgrade();
            
            if (!$didWePerformAnUpgrade) {
                $this->getResponse()
                     ->setHttpResponseCode(200)
                     ->appendBody("No upgrade was performed. The current schema version is " . Application_Model_Preference::GetSchemaVersion() . ".<br>");
            } else {
                $this->getResponse()
                     ->setHttpResponseCode(200)
                     ->appendBody("Upgrade to Airtime schema version " . Application_Model_Preference::GetSchemaVersion() . " OK<br>");
            }
        } 
        catch (Exception $e) 
        {
            $this->getResponse()
                 ->setHttpResponseCode(400)
                 ->appendBody($e->getMessage());
        }
    }

    private function verifyAuth()
    {
        //The API key is passed in via HTTP "basic authentication":
        //http://en.wikipedia.org/wiki/Basic_access_authentication
        
        $CC_CONFIG = Config::getConfig();
        
        //Decode the API key that was passed to us in the HTTP request.
        $authHeader = $this->getRequest()->getHeader("Authorization");

        $encodedRequestApiKey = substr($authHeader, strlen("Basic "));
        $encodedStoredApiKey = base64_encode($CC_CONFIG["apiKey"][0] . ":");

        if ($encodedRequestApiKey !== $encodedStoredApiKey)
        {
            $this->getResponse()
                 ->setHttpResponseCode(401)
                 ->appendBody("Error: Incorrect API key.<br>");
            return false;
        }
        return true;
    }

}
