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
     * Clear the previously saved request token from the preferences
     *
     * @return void
     */
    public function deauthorizeAction() {
        $function = $this->_SERVICE_TOKEN_ACCESSOR;
        Application_Model_Preference::$function("");
        header('Location: ' . $this->_baseUrl . 'preference');  // Redirect back to the preference page
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
        header('Location: ' . $this->_baseUrl . 'preference');  // Redirect back to the preference page
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
     * Delete the file with the given id from a third-party service
     *
     * @return void
     *
     * @throws Zend_Controller_Response_Exception thrown if deletion fails for any reason
     */
    public function deleteAction() {
        $request = $this->getRequest();
        $id = $request->getParam('id');
        $this->_service->delete($id);
    }

}