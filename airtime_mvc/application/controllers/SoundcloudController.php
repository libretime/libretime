<?php

class SoundcloudController extends ThirdPartyController implements OAuth2Controller {

    /**
     * @var Application_Service_SoundcloudService
     */
    protected $_service;

    /**
     * Set up SoundCloud access variables.
     *
     * @return void
     */
    public function init() {
        parent::init();
        $this->_service = new Application_Service_SoundcloudService();
    }

    /**
     * Upload the file with the given id to SoundCloud
     *
     * @return void
     *
     * @throws Zend_Controller_Response_Exception thrown if upload fails for any reason
     */
    public function uploadAction() {
        $id = $this->getRequest()->getParam('id');
        $this->_service->upload($id);
    }

    /**
     * Update the file with the given id on SoundCloud
     *
     * @return void
     *
     * @throws Zend_Controller_Response_Exception thrown if upload fails for any reason
     */
    public function updateAction() {
        $id = $this->getRequest()->getParam('id');
        $this->_service->update($id);
    }

    /**
     * Download the file with the given id from SoundCloud
     *
     * @return void
     *
     * @throws Zend_Controller_Response_Exception thrown if download fails for any reason
     */
    public function downloadAction() {
        $id = $this->getRequest()->getParam('id');
        $this->_service->download($id);
    }

    /**
     * Delete the file with the given id from SoundCloud
     *
     * @return void
     *
     * @throws Zend_Controller_Response_Exception thrown if deletion fails for any reason
     */
    public function deleteAction() {
        $id = $this->getRequest()->getParam('id');
        $this->_service->delete($id);
    }

    /**
     * Send user to SoundCloud to authorize before being redirected
     *
     * @return void
     */
    public function authorizeAction() {
        $auth_url = $this->_service->getAuthorizeUrl();
        header('Location: ' . $auth_url);
    }

    /**
     * Clear the previously saved request token from preferences
     *
     * @return void
     */
    public function deauthorizeAction() {
        Application_Model_Preference::setSoundCloudRequestToken("");
        header('Location: ' . $this->_baseUrl . 'preference');  // Redirect back to the preference page
    }

    /**
     * Called when user successfully completes SoundCloud authorization
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
     * Fetch the permalink to a file on SoundCloud and redirect to it.
     *
     * @return void
     */
    public function viewOnSoundCloudAction() {
        $id = $this->getRequest()->getParam('id');
        try {
            $soundcloudLink = $this->_service->getLinkToFile($id);
            header('Location: ' . $soundcloudLink);
        } catch (Soundcloud\Exception\InvalidHttpResponseCodeException $e) {
            // Redirect to a 404 so the user knows something went wrong
            header('Location: ' . $this->_baseUrl . 'error/error-404');
        }
    }

}
