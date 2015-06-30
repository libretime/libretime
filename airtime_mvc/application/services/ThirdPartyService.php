<?php

/**
 * Class ServiceNotFoundException
 */
class ServiceNotFoundException extends Exception {}

/**
 * Class ThirdPartyService generic superclass for third-party services
 */
abstract class ThirdPartyService {

    /**
     * @var string service access token for accessing third-party API
     */
    protected $_accessToken;

    /**
     * @var string service name to store in ThirdPartyTrackReferences database
     */
    protected static $_SERVICE_NAME;

    /**
     * @var string base URI for third-party tracks
     */
    protected static $_THIRD_PARTY_TRACK_URI;

    /**
     * Create a ThirdPartyTrackReferences object for a track that's been uploaded
     * to an external service
     * TODO: should we have a database layer class to handle Propel operations?
     *
     * @param $fileId int local CcFiles identifier
     *
     * @return string the new ThirdPartyTrackReferences identifier
     *
     * @throws Exception
     * @throws PropelException
     */
    public function createTrackReference($fileId) {
        // First, check if the track already has an entry in the database
        $ref = ThirdPartyTrackReferencesQuery::create()
            ->filterByDbService(static::$_SERVICE_NAME)
            ->findOneByDbFileId($fileId);
        if (is_null($ref)) {
            $ref = new ThirdPartyTrackReferences();
        }
        $ref->setDbService(static::$_SERVICE_NAME);
        // TODO: implement service-specific statuses?
        // $ref->setDbStatus(CELERY_PENDING_STATUS);
        $ref->setDbFileId($fileId);
        $ref->save();
        return $ref->getDbId();
    }

    /**
     * Remove a ThirdPartyTrackReferences from the database.
     * This is necessary if the track was removed from the service
     * or the foreign id in our database is incorrect
     *
     * @param $fileId int cc_files identifier
     *
     * @throws Exception
     * @throws PropelException
     */
    public function removeTrackReference($fileId) {
        $ref = ThirdPartyTrackReferencesQuery::create()
            ->filterByDbService(static::$_SERVICE_NAME)
            ->findOneByDbFileId($fileId);
        $ref->delete();
    }

    /**
     * Given a CcFiles identifier for a file that's been uploaded to a third-party service,
     * return the third-party identifier for the remote file
     *
     * @param int $fileId the cc_files identifier
     *
     * @return string the service foreign identifier
     */
    public function getServiceId($fileId) {
        $ref = ThirdPartyTrackReferencesQuery::create()
            ->filterByDbService(static::$_SERVICE_NAME)
            ->findOneByDbFileId($fileId);  // There shouldn't be duplicates!
        return empty($ref) ? '' : $ref->getDbForeignId();
    }

    /**
     * Check if a reference exists for a given CcFiles identifier
     *
     * @param int $fileId the cc_files identifier
     *
     * @return string the service foreign identifier
     */
    public function referenceExists($fileId) {
        $ref = ThirdPartyTrackReferencesQuery::create()
            ->filterByDbService(static::$_SERVICE_NAME)
            ->findOneByDbFileId($fileId);  // There shouldn't be duplicates!
        if (!empty($ref)) {
            $task = CeleryTasksQuery::create()
                ->findOneByDbTrackReference($ref->getDbId());
            return $task->getDbStatus() != CELERY_FAILED_STATUS;
        }
        return false;
    }

    /**
     * Given a CcFiles identifier for a file that's been uploaded to a third-party service,
     * return a link to the remote file
     *
     * @param int $fileId the cc_files identifier
     *
     * @return string the link to the remote file
     */
    public function getLinkToFile($fileId) {
        $serviceId = $this->getServiceId($fileId);
        return empty($serviceId) ? '' : static::$_THIRD_PARTY_TRACK_URI . $serviceId;
    }

    /**
     * Upload the file with the given identifier to a third-party service
     *
     * @param int $fileId the cc_files identifier
     */
    abstract function upload($fileId);

    /**
     * Delete the file with the given identifier from a third-party service
     *
     * @param int $fileId the cc_files identifier
     *
     * @throws ServiceNotFoundException when a $fileId with no corresponding
     *                                  service identifier is given
     */
    abstract function delete($fileId);

    /**
     * Update a ThirdPartyTrackReferences object for a completed task
     *
     * @param $trackId int    ThirdPartyTrackReferences identifier
     * @param $track  object  third-party service track object
     * @param $status string  Celery task status
     *
     * @throws Exception
     * @throws PropelException
     */
    abstract function updateTrackReference($trackId, $track, $status);

}