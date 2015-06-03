<?php

/**
 * Class ThirdPartyService generic superclass for third-party services
 */
abstract class ThirdPartyService {

    /**
     * @var string service name to store in ThirdPartyTrackReferences database
     */
    protected $_SERVICE_NAME = '';

    /**
     * @var string base URI for third-party tracks
     */
    protected $_THIRD_PARTY_TRACK_URI = '';

    /**
     * Upload the file with the given identifier to a third-party service
     *
     * @param int $fileId the local CcFiles identifier
     */
    abstract function upload($fileId);

    /**
     * Create a ThirdPartyTrackReferences and save it to the database
     *
     * @param $fileId int    local CcFiles identifier
     * @param $track  object third-party service track object
     *
     * @throws Exception
     * @throws PropelException
     */
    protected function _createTrackReference($fileId, $track) {
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
        $ref->save();
    }

    /**
     * Remove a ThirdPartyTrackReferences from the database.
     * This is necessary if the track was removed from the service
     * or the foreign id in our database is incorrect
     *
     * @param $fileId int local CcFiles identifier
     *
     * @throws Exception
     * @throws PropelException
     */
    public function removeTrackReference($fileId) {
        $ref = ThirdPartyTrackReferencesQuery::create()
            ->filterByDbService($this->_SERVICE_NAME)
            ->findOneByDbFileId($fileId);
        $ref->delete();
    }

    /**
     * Given a CcFiles identifier for a file that's been uploaded to a third-party service,
     * return the third-party identifier for the remote file
     *
     * @param int $fileId the local CcFiles identifier
     *
     * @return int the service foreign identifier
     */
    public function getServiceId($fileId) {
        $ref = ThirdPartyTrackReferencesQuery::create()
            ->filterByDbService($this->_SERVICE_NAME)
            ->findOneByDbFileId($fileId); // There shouldn't be duplicates!
        return is_null($ref) ? 0 : $ref->getDbForeignId();
    }

    /**
     * Given a CcFiles identifier for a file that's been uploaded to a third-party service,
     * return a link to the remote file
     *
     * @param int $fileId the local CcFiles identifier
     *
     * @return string the link to the remote file
     */
    public function getLinkToFile($fileId) {
        $serviceId = $this->getServiceId($fileId);
        return $serviceId > 0 ? $this->_THIRD_PARTY_TRACK_URI . $serviceId : '';
    }

    /**
     * Check whether an OAuth access token exists for the third-party client
     *
     * @return bool true if an access token exists, otherwise false
     */
    abstract function hasAccessToken();

    /**
     * Get the OAuth authorization URL
     *
     * @return string the authorization URL
     */
    abstract function getAuthorizeUrl();

    /**
     * Request a new OAuth access token from a third-party service and store it in CcPref
     *
     * @param $code string exchange authorization code for access token
     */
    abstract function requestNewAccessToken($code);

    /**
     * Regenerate the third-party client's OAuth access token
     */
    abstract function accessTokenRefresh();

}