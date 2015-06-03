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
     * Disable controller rendering and initialize
     */
    public function init() {
        $CC_CONFIG = Config::getConfig();
        $this->_baseUrl = 'http://' . $CC_CONFIG['baseUrl'] . ":" . $CC_CONFIG['basePort'] . "/";

        $this->view->layout()->disableLayout(); // Don't inject the standard Now Playing header.
        $this->_helper->viewRenderer->setNoRender(true); // Don't use (phtml) templates
    }

    /**
     * Send user to a third-party service to authorize before being redirected
     *
     * @return void
     */
    abstract function authorizeAction();

    /**
     * Called when user successfully completes third-party authorization.
     * Store the returned request token for future requests.
     *
     * @return void
     */
    abstract function redirectAction();

    /**
     * Upload the file with the given id to a third-party service.
     *
     * @return void
     *
     * @throws Zend_Controller_Response_Exception thrown if upload fails for any reason
     */
    abstract function uploadAction();

    /**
     * Clear the previously saved request token from the preferences.
     *
     * @return void
     */
    abstract function deauthorizeAction();

}