<?php

require_once "ThirdPartyService.php";

class SoundcloudService extends ThirdPartyService {

    /**
     * @var string service access token for accessing remote API
     */
    protected $_accessToken;

    /**
     * @var Soundcloud\Service SoundCloud API wrapper object
     */
    private $_client;

    /**
     * @var string service name to store in ThirdPartyTrackReferences database
     */
    protected $_SERVICE_NAME = 'SoundCloud';

    /**
     * @var string base URI for SoundCloud tracks
     */
    protected $_THIRD_PARTY_TRACK_URI = 'http://api.soundcloud.com/tracks/';

    /**
     * @var string exchange name for SoundCloud tasks
     */
    protected $_CELERY_EXCHANGE_NAME = 'soundcloud-uploads';

    /**
     * @var string celery task name for third party uploads
     */
    protected $_CELERY_UPLOAD_TASK_NAME = 'upload-to-soundcloud';

    /**
     * @var array Application_Model_Preference functions for SoundCloud and their
     *            associated API parameter keys so that we can call them dynamically
     */
    private $_SOUNDCLOUD_PREF_FUNCTIONS = array(
        "getDefaultSoundCloudLicenseType" => "license",
        "getDefaultSoundCloudSharingType" => "sharing"
    );

    /**
     * Initialize the service
     */
    public function __construct() {
        $CC_CONFIG      = Config::getConfig();
        $clientId       = $CC_CONFIG['soundcloud-client-id'];
        $clientSecret   = $CC_CONFIG['soundcloud-client-secret'];
        $redirectUri    = $CC_CONFIG['soundcloud-redirect-uri'];

        $this->_client = new Soundcloud\Service($clientId, $clientSecret, $redirectUri);
        $accessToken = Application_Model_Preference::getSoundCloudRequestToken();
        if (!empty($accessToken)) {
            $this->_accessToken = $accessToken;
            $this->_client->setAccessToken($accessToken);
        }
    }

    /**
     * Build a parameter array for the track being uploaded to SoundCloud
     *
     * @param $file Application_Model_StoredFile the file being uploaded
     *
     * @return array the track array to send to SoundCloud
     */
    protected function _getUploadData($file) {
        $trackArray = array(
            'title' => $file->getName(),
        );
        foreach ($this->_SOUNDCLOUD_PREF_FUNCTIONS as $func => $param) {
            $val = Application_Model_Preference::$func();
            if (!empty($val)) {
                $trackArray[$param] = $val;
            }
        }

        return $trackArray;
    }

    /**
     * Update a ThirdPartyTrackReferences object for a completed upload
     *
     * @param $fileId int    local CcFiles identifier
     * @param $track  object third-party service track object
     *
     * @throws Exception
     * @throws PropelException
     */
    protected function _addOrUpdateTrackReference($fileId, $track) {
        // First, check if the track already has an entry in the database
        $ref = ThirdPartyTrackReferencesQuery::create()
            ->filterByDbService($this->_SERVICE_NAME)
            ->findOneByDbFileId($fileId);
        if (is_null($ref)) {
            $ref = new ThirdPartyTrackReferences();
        }
        $ref->setDbService($this->_SERVICE_NAME);
        $ref->setDbForeignId($track->id);
        $ref->setDbFileId($fileId);
        $ref->setDbStatus($track->state);
        // Null the broker task fields because we no longer need them
        $ref->setDbBrokerTaskId(NULL);
        $ref->setDbBrokerTaskName(NULL);
        $ref->setDbBrokerTaskDispatchTime(NULL);
        $ref->save();
    }

    /**
     * Given a CcFiles identifier for a file that's been uploaded to SoundCloud,
     * return a link to the remote file
     *
     * @param int $fileId the local CcFiles identifier
     *
     * @return string the link to the remote file
     */
    public function getLinkToFile($fileId) {
        $serviceId = $this->getServiceId($fileId);
        // If we don't find a record for the file we'll get 0 back for the id
        if ($serviceId == 0) { return ''; }
        $track = json_decode($this->_client->get('tracks/' . $serviceId));
        return $track->permalink_url;
    }

    /**
     * Check whether an access token exists for the SoundCloud client
     *
     * @return bool true if an access token exists, otherwise false
     */
    public function hasAccessToken() {
        return !empty($this->_accessToken);
    }

    /**
     * Get the SoundCloud authorization URL
     *
     * @return string the authorization URL
     */
    public function getAuthorizeUrl() {
        // Pass the current URL in the state parameter in order to preserve it
        // in the redirect. This allows us to create a singular script to redirect
        // back to any station the request comes from.
        $url = urlencode('http'.(empty($_SERVER['HTTPS'])?'':'s').'://'.$_SERVER['HTTP_HOST'].'/soundcloud/redirect');
        return $this->_client->getAuthorizeUrl(array("state" => $url));
    }

    /**
     * Request a new access token from SoundCloud and store it in CcPref
     *
     * @param $code string exchange authorization code for access token
     */
    public function requestNewAccessToken($code) {
        // Get a non-expiring access token
        $response = $this->_client->accessToken($code, $postData = array('scope' => 'non-expiring'));
        $accessToken = $response['access_token'];
        Application_Model_Preference::setSoundCloudRequestToken($accessToken);
        $this->_accessToken = $accessToken;
    }

    /**
     * Regenerate the SoundCloud client's access token
     *
     * @throws Soundcloud\Exception\InvalidHttpResponseCodeException
     *         thrown when attempting to regenerate a stale token
     */
    public function accessTokenRefresh() {
        assert($this->hasAccessToken());
        try {
            $accessToken = $this->_accessToken;
            $this->_client->accessTokenRefresh($accessToken);
        } catch(Soundcloud\Exception\InvalidHttpResponseCodeException $e) {
            // If we get here, then that means our token is stale, so remove it
            // Because we're using non-expiring tokens, we shouldn't get here (!)
            Application_Model_Preference::setSoundCloudRequestToken("");
        }
    }

}