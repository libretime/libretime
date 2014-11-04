<?php

class InstallWizardController extends Zend_Controller_Action {
    public function init() {
        $this->view->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    }
    
    public function runAirtimeConfigurationAction() {
        
    }
}
