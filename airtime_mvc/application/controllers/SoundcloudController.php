<?php

require_once "ThirdPartyController.php";
require_once "ise/php-soundcloud/src/Soundcloud/Service.php";

class SoundcloudController extends ThirdPartyController implements OAuth2Controller {

    /**
     * @var SoundcloudService
     */
    protected $_service;

    /**
     * @var string Application_Model_Preference service request token accessor function name
     */
    protected $_SERVICE_TOKEN_ACCESSOR = 'setSoundCloudRequestToken';

    /**
     * Set up SoundCloud access variables.
     */
    public function init() {
        parent::init();
        $this->_service = new SoundcloudService();
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
        $function = $this->_SERVICE_TOKEN_ACCESSOR;
        Application_Model_Preference::$function("");
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
     */
    public function viewOnSoundCloudAction() {
        $request = $this->getRequest();
        $id = $request->getParam('id');
        try {
            $soundcloudLink = $this->_service->getLinkToFile($id);
            header('Location: ' . $soundcloudLink);
        } catch (Soundcloud\Exception\InvalidHttpResponseCodeException $e) {
            // Redirect to a 404 so the user knows something went wrong
            header('Location: ' . $this->_baseUrl . 'error/error-404');
        }
    }

}
