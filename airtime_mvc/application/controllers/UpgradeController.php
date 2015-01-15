<?php

require_once("Upgrades.php");

class UpgradeController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        if (!$this->verifyAuth()) {
            return;
        }

        $upgraders = array();
        array_push($upgraders, new AirtimeUpgrader252());
        /* These upgrades do not apply to open source Airtime yet.
        array_push($upgraders, new AirtimeUpgrader253());
        array_push($upgraders, new AirtimeUpgrader254());
        */
        $didWePerformAnUpgrade = false;
        try 
        {
            for ($i = 0; $i < count($upgraders); $i++)
            {
                $upgrader = $upgraders[$i];
                if ($upgrader->checkIfUpgradeSupported())
                {
                	// pass __DIR__ to the upgrades, since __DIR__ returns parent dir of file, not executor
                    $upgrader->upgrade(__DIR__); //This will throw an exception if the upgrade fails.
                    $didWePerformAnUpgrade = true;
                    $this->getResponse()
                         ->setHttpResponseCode(200)
                         ->appendBody("Upgrade to Airtime " . $upgrader->getNewVersion() . " OK<br>"); 
                    $i = 0; //Start over, in case the upgrade handlers are not in ascending order.
                }
            }
            
            if (!$didWePerformAnUpgrade)
            {
                $this->getResponse()
                	 ->setHttpResponseCode(200)
                	 ->appendBody("No upgrade was performed. The current Airtime version is " . AirtimeUpgrader::getCurrentVersion() . ".<br>");
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
