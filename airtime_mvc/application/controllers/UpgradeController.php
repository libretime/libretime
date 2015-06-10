<?php

require_once("Upgrades.php");

class UpgradeController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        if (!RestAuth::verifyAuth(true, false, $this)) {
            return;
        }

        // Get all upgrades dynamically (in declaration order!) so we don't have to add them explicitly each time
        // TODO: explicitly sort classnames by ascending version suffix for safety
        $upgraders = getUpgrades();

        $didWePerformAnUpgrade = false;
        try 
        {
            foreach ($upgraders as $upgrader)
            {
                /** @var $upgrader AirtimeUpgrader */
                $upgrader = new $upgrader();
                if ($upgrader->checkIfUpgradeSupported())
                {
                	// pass __DIR__ to the upgrades, since __DIR__ returns parent dir of file, not executor
                    $upgrader->upgrade(__DIR__); //This will throw an exception if the upgrade fails.
                    $didWePerformAnUpgrade = true;
                    $this->getResponse()
                         ->setHttpResponseCode(200)
                         ->appendBody("Upgrade to Airtime " . $upgrader->getNewVersion() . " OK<br>"); 
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
}
