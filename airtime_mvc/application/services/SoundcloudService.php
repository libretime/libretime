<?php

/**
 * Service object for dealing with SoundCloud authorization and background tasks
 *
 * Class Application_Service_SoundcloudService
 */
class Application_Service_SoundcloudService extends Application_Service_ThirdPartyCeleryService implements OAuth2, Publish {

    /**
     * Arbitrary constant identifiers for the internal tasks array
     */

    const UPLOAD    = 'upload';
    const UPDATE    = 'update';
    const DOWNLOAD  = 'download';
    const DELETE    = 'delete';

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
    protected static $_SERVICE_NAME = SOUNDCLOUD_SERVICE_NAME;  // SoundCloud service name constant from constants.php

    /**
     * @var string exchange name for SoundCloud tasks
     */
    protected static $_CELERY_EXCHANGE_NAME = 'soundcloud';

    /**
     * @var array map of constant identifiers to Celery task names
     */
    protected static $_CELERY_TASKS = [
        self::UPLOAD      => 'soundcloud-upload',
        self::UPDATE      => 'soundcloud-update',
        self::DOWNLOAD    => 'soundcloud-download',
        self::DELETE      => 'soundcloud-delete'
    ];

    /**
     * @var array Application_Model_Preference functions for SoundCloud and their
     *            associated API parameter keys so that we can call them dynamically
     */
    private static $_SOUNDCLOUD_PREF_FUNCTIONS = array(
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

        if ($clientId && $clientSecret && $redirectUri) {
            $this->_client = new Soundcloud\Service($clientId, $clientSecret, $redirectUri);
            $accessToken = Application_Model_Preference::getSoundCloudRequestToken();
        }
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
        $file = $file->getPropelOrm();
        $trackArray = $this->_serializeTrack($file);
        foreach (self::$_SOUNDCLOUD_PREF_FUNCTIONS as $func => $param) {
            $val = Application_Model_Preference::$func();
            if (!empty($val)) {
                $trackArray[$param] = $val;
            }
        }

        return $trackArray;
    }

    /**
     * Serialize Airtime file data to send to SoundCloud
     *
     * Ignores any null fields, as these will cause the upload to throw a 422
     * Unprocessable Entity error
     *
     * TODO: Move this into a proper serializer
     *
     * @param $file CcFiles file object
     *
     * @return array the serialized data
     */
    protected function _serializeTrack($file) {
        $fileData = array(
            'title'         => $file->getDbTrackTitle(),
            'genre'         => $file->getDbGenre(),
            'bpm'           => $file->getDbBpm(),
            'release_year'  => $file->getDbYear(),
        );
        $trackArray = array();
        foreach ($fileData as $k => $v) {
            if (!empty($v)) {
                $trackArray[$k] = $v;
            }
        }
        return $trackArray;
    }

    /**
     * Upload the file with the given identifier to SoundCloud
     *
     * @param int $fileId the local CcFiles identifier
     */
    public function upload($fileId) {
        $file = Application_Model_StoredFile::RecallById($fileId);
        $data = array(
            'data' => $this->_getUploadData($file),
            'token' => $this->_accessToken,
            'file_path' => $file->getFilePaths()[0]
        );
        $this->_executeTask(static::$_CELERY_TASKS[self::UPLOAD], $data, $fileId);
    }

    /**
     * Given a track identifier, update a track on SoundCloud
     *
     * @param int $trackId a track identifier
     */
    public function update($trackId) {
        $trackRef = ThirdPartyTrackReferencesQuery::create()
            ->findOneByDbForeignId($trackId);
        $file = Application_Model_StoredFile::RecallById($trackRef->getDbFileId());
        $data = array(
            'data' => $this->_getUploadData($file),
            'token' => $this->_accessToken,
            'track_id' => $trackId
        );
        $this->_executeTask(static::$_CELERY_TASKS[self::UPDATE], $data, $trackRef->getDbFileId());
    }

    /**
     * Given a track identifier, download a track from SoundCloud
     *
     * @param int $trackId a track identifier
     */
    public function download($trackId) {
        $CC_CONFIG = Config::getConfig();
        $data = array(
            'callback_url'  => Application_Common_HTTPHelper::getStationUrl() . 'rest/media',
            'api_key'       => $apiKey = $CC_CONFIG["apiKey"][0],
            'token'         => $this->_accessToken,
            'track_id'      => $trackId
        );
        $this->_executeTask(static::$_CELERY_TASKS[self::DOWNLOAD], $data);
    }

    /**
     * Delete the file with the given identifier from SoundCloud
     *
     * @param int $fileId the local CcFiles identifier
     *
     * @throws ServiceNotFoundException when a $fileId with no corresponding
     *                                  service identifier is given
     */
    public function delete($fileId) {
        $serviceId = $this->getServiceId($fileId);
        if ($serviceId == 0) {
            throw new ServiceNotFoundException("No service ID found for file with ID $fileId");
        }
        $data = array(
            'token' => $this->_accessToken,
            'track_id' => $serviceId
        );
        $this->_executeTask(static::$_CELERY_TASKS[self::DELETE], $data, $fileId);
    }

    /**
     * Update a ThirdPartyTrackReferences object for a completed upload
     *
     * TODO: should we have a database layer class to handle Propel operations?
     *
     * @param $task     CeleryTasks the completed CeleryTasks object
     * @param $trackId  int         ThirdPartyTrackReferences identifier
     * @param $track    object      third-party service track object
     * @param $status   string      Celery task status
     *
     * @return ThirdPartyTrackReferences the updated ThirdPartyTrackReferences object
     *                                   or null if the task was a DELETE
     *
     * @throws Exception
     * @throws PropelException
     */
    public function updateTrackReference($task, $trackId, $track, $status) {
        $ref = parent::updateTrackReference($task, $trackId, $track, $status);
        // TODO: fetch any additional SoundCloud parameters we want to store
        // Only set the SoundCloud fields if the task was successful
        if ($status == CELERY_SUCCESS_STATUS) {
            // If the task was to delete the file from SoundCloud, remove the reference
            if ($task->getDbName() == static::$_CELERY_TASKS[self::DELETE]) {
                $this->removeTrackReference($ref->getDbFileId());
                return null;
            }
            $ref->setDbForeignId($track->id);  // SoundCloud identifier
            if (isset($track->fileid)) {
                $ref->setDbFileId($track->fileid);  // For downloads, set the cc_files ID
            }
        }
        // TODO: set SoundCloud upload status?
        // $ref->setDbStatus($status);
        $ref->save();
        return $ref;
    }

    /**
     * Given a CcFiles identifier for a file that's been uploaded to SoundCloud,
     * return a link to the remote file
     *
     * @param int $fileId the local CcFiles identifier
     *
     * @return string the link to the remote file
     *
     * @throws Soundcloud\Exception\InvalidHttpResponseCodeException when SoundCloud returns a 4xx/5xx response
     */
    public function getLinkToFile($fileId) {
        $serviceId = $this->getServiceId($fileId);
        // If we don't find a record for the file we'll get 0 back for the id
        if ($serviceId == 0) { return ''; }
        try {
            $track = json_decode($this->_client->get('tracks/' . $serviceId));
        } catch (Soundcloud\Exception\InvalidHttpResponseCodeException $e) {
            // If we end up here it means the track was removed from SoundCloud
            // or the foreign id in our database is incorrect, so we should just
            // get rid of the database record
            Logging::warn("Error retrieving track data from SoundCloud: " . $e->getMessage());
            $this->removeTrackReference($fileId);
            throw $e;  // Throw the exception up to the controller so we can redirect to a 404
        }
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
        $url = urlencode(Application_Common_HTTPHelper::getStationUrl() . '/soundcloud/redirect');
        return $this->_client->getAuthorizeUrl(array("state" => $url, "scope" => "non-expiring"));
    }

    /**
     * Request a new access token from SoundCloud and store it in CcPref
     *
     * @param $code string exchange authorization code for access token
     */
    public function requestNewAccessToken($code) {
        // Get a non-expiring access token
        $response = $this->_client->accessToken($code);
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

    /**
     * Publishing interface proxy
     *
     * Publish the file with the given file ID to SoundCloud
     *
     * @param int $fileId ID of the file to be published
     */
    public function publish($fileId) {
        $this->upload($fileId);
    }

    /**
     * Publishing interface proxy
     *
     * Unpublish the file with the given file ID from SoundCloud
     *
     * @param int $fileId ID of the file to be unpublished
     *
     * @throws ServiceNotFoundException when a $fileId with no corresponding
     *                                  service identifier is given
     */
    public function unpublish($fileId) {
        $this->delete($fileId);
    }

    /**
     * Fetch the publication status for the file with the given ID
     *
     * @param int $fileId the ID of the file to check
     *
     * @return int 1 if the file has been published,
     *             0 if the file has yet to be published,
     *             -1 if the file is in a pending state,
     *             2 if the source is unreachable (disconnected)
     */
    public function getPublishStatus($fileId) {
        if (!$this->hasAccessToken()) { return 2; }
        return ($this->referenceExists($fileId));
    }
}