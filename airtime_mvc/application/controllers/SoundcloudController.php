<?php

require_once "ThirdPartyController.php";
require_once "ise/php-soundcloud/src/Soundcloud/Service.php";

class SoundcloudController extends ThirdPartyController {

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
