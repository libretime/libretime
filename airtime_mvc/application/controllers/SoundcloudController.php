<?php

require_once "ThirdPartyController.php";
require_once "ise/php-soundcloud/src/Soundcloud/Service.php";

class SoundcloudController extends ThirdPartyController {

    /**
     * @var SoundcloudService
     */
    private $_soundcloudService;

    /**
     * Set up SoundCloud access variables.
     */
    public function init() {
        parent::init();
        $this->_soundcloudService = new SoundcloudService();
    }

    /**
     * Send user to SoundCloud to authorize before being redirected
     */
    public function authorizeAction() {
        $auth_url = $this->_soundcloudService->getAuthorizeUrl();
        header('Location: ' . $auth_url);
    }

    /**
     * Called when user successfully completes SoundCloud authorization.
     * Store the returned request token for future requests.
     */
    public function redirectAction() {
        $code = $_GET['code'];
        $this->_soundcloudService->requestNewAccessToken($code);
        header('Location: ' . $this->_baseUrl . 'Preference'); // Redirect back to the Preference page
    }

    /**
     * Fetch the permalink to a file on SoundCloud and redirect to it.
     */
    public function viewOnSoundCloudAction() {
        $request = $this->getRequest();
        $id = $request->getParam('id');
        try {
            $soundcloudLink = $this->_soundcloudService->getLinkToFile($id);
            header('Location: ' . $soundcloudLink);
        } catch (Soundcloud\Exception\InvalidHttpResponseCodeException $e) {
            // If we end up here it means the track was removed from SoundCloud
            // or the foreign id in our database is incorrect, so we should just
            // get rid of the database record
            Logging::warn("Error retrieving track data from SoundCloud: " . $e->getMessage());
            $this->_soundcloudService->removeTrackReference($id);
            // Redirect to a 404 so the user knows something went wrong
            header('Location: ' . $this->_baseUrl . 'error/error-404'); // Redirect back to the Preference page
        }
    }

    /**
     * Upload the file with the given id to SoundCloud.
     *
     * @throws Zend_Controller_Response_Exception thrown if upload fails for any reason
     */
    public function uploadAction() {
        $request = $this->getRequest();
        $id = $request->getParam('id');
        $this->_soundcloudService->upload($id);
    }

    /**
     * Clear the previously saved request token from the preferences.
     */
    public function deauthorizeAction() {
        Application_Model_Preference::setSoundCloudRequestToken("");
        header('Location: ' . $this->_baseUrl . 'Preference'); // Redirect back to the Preference page
    }

}
