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
     * @var ThirdPartyService third party service object
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
        $CC_CONFIG = Config::getConfig();
        $this->_baseUrl = 'http://' . $CC_CONFIG['baseUrl'] . ":" . $CC_CONFIG['basePort'] . "/";

        $this->view->layout()->disableLayout();  // Don't inject the standard Now Playing header.
        $this->_helper->viewRenderer->setNoRender(true);  // Don't use (phtml) templates
    }

    /**
     * Send user to a third-party service to authorize before being redirected
     *
     * @return void
     */
    public function authorizeAction() {
        $auth_url = $this->_service->getAuthorizeUrl();
        header('Location: ' . $auth_url);
    }

    /**
     * Called when user successfully completes third-party authorization
     * Store the returned request token for future requests
     *
     * @return void
     */
    public function redirectAction() {
        $code = $_GET['code'];
        $this->_service->requestNewAccessToken($code);
        header('Location: ' . $this->_baseUrl . 'Preference');  // Redirect back to the Preference page
    }

    /**
     * Upload the file with the given id to a third-party service
     *
     * @return void
     *
     * @throws Zend_Controller_Response_Exception thrown if upload fails for any reason
     */
    public function uploadAction() {
        $request = $this->getRequest();
        $id = $request->getParam('id');
        $this->_service->upload($id);
    }

    /**
     * Clear the previously saved request token from the preferences
     *
     * @return void
     */
    public function deauthorizeAction() {
        Application_Model_Preference::$this->_SERVICE_TOKEN_ACCESSOR("");
        header('Location: ' . $this->_baseUrl . 'Preference');  // Redirect back to the Preference page
    }

    /**
     * Poll the task queue for completed tasks associated with this service
     * Optionally accepts a specific task name as a parameter
     *
     * @return void
     */
    public function pollBrokerTaskQueueAction() {
        $request = $this->getRequest();
        $taskName = $request->getParam('task');
        $this->_service->pollBrokerTaskQueue($taskName);
    }

}