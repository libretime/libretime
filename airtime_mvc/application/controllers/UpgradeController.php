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

        try {
            $didWePerformAnUpgrade = UpgradeManager::doUpgrade();

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

    public function downgradeAction() {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        if (!RestAuth::verifyAuth(true, false, $this)) {
            return;
        }

        $request = $this->getRequest();
        $toVersion = $request->getParam("version");

        try {
            $downgradePerformed = UpgradeManager::doDowngrade($toVersion);

            if (!$downgradePerformed) {
                $this->getResponse()
                    ->setHttpResponseCode(200)
                    ->appendBody("No downgrade was performed. The current schema version is " . Application_Model_Preference::GetSchemaVersion() . ".<br>");
            } else {
                $this->getResponse()
                    ->setHttpResponseCode(200)
                    ->appendBody("Downgrade to Airtime schema version " . Application_Model_Preference::GetSchemaVersion() . " OK<br>");
            }
        } catch (Exception $e) {
            $this->getResponse()
                ->setHttpResponseCode(400)
                ->appendBody($e->getMessage());
        }
    }

}
