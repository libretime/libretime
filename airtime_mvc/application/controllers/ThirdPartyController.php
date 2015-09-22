<?php

/**
 * Class ThirdPartyController abstract superclass for third-party service authorization
 */
abstract class ThirdPartyController extends Zend_Controller_Action {

    /**
     * @var string base url and port for redirection
     */
    protected $_baseUrl;

    /**
     * @var Application_Service_ThirdPartyService third party service object
     */
    protected $_service;

    /**
     * @var string Application_Model_Preference service request token accessor function name
     */
    protected $_SERVICE_TOKEN_ACCESSOR;

    /**
     * Disable controller rendering and initialize
     */
    public function init() {
        $this->_baseUrl = Application_Common_HTTPHelper::getStationUrl();

        $this->view->layout()->disableLayout();  // Don't inject the standard Now Playing header.
        $this->_helper->viewRenderer->setNoRender(true);  // Don't use (phtml) templates
    }

}