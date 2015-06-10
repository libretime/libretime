<?php

/**
 * Class ThirdPartyService generic superclass for third-party services
 */
abstract class ThirdPartyService {

    /**
     * @var string service access token for accessing remote API
     */
    protected $_accessToken;

    /**
     * @var string service name to store in ThirdPartyTrackReferences database
     */
    protected $_SERVICE_NAME;

    /**
     * @var string base URI for third-party tracks
     */
    protected $_THIRD_PARTY_TRACK_URI;

    /**
     * @var string broker exchange name for third party tasks
     */
    protected $_CELERY_EXCHANGE_NAME = 'default';

    /**
     * @var string celery task name for third party uploads
     */
    protected $_CELERY_UPLOAD_TASK_NAME = 'upload';

    /**
     * @var string status string for pending tasks
     */
    protected $_PENDING_STATUS = 'PENDING';

    /**
     * @var string status string for failed tasks
     */
    protected $_FAILED_STATUS = 'FAILED';

    /**
     * Upload the file with the given identifier to a third-party service
     *
     * @param int $fileId the local CcFiles identifier
     *
     * @throws Exception thrown when the upload fails for any reason
     */
    public function upload($fileId) {
        $file = Application_Model_StoredFile::RecallById($fileId);
        $data = array(
            'data' => $this->_getUploadData($file),
            'token' => $this->_accessToken,
            'file_path' => $file->getFilePaths()[0]
        );
        try {
            $brokerTaskId = Application_Model_RabbitMq::sendCeleryMessage($this->_CELERY_UPLOAD_TASK_NAME,
                                                                          $this->_CELERY_EXCHANGE_NAME,
                                                                          $data);
            $this->_createTaskReference($fileId, $brokerTaskId, $this->_CELERY_UPLOAD_TASK_NAME);
        } catch(Exception $e) {
            Logging::info("Invalid request: " . $e->getMessage());
            // We should only get here if we have an access token, so attempt to refresh
            $this->accessTokenRefresh();
        }
    }

    /**
     * Create a ThirdPartyTrackReferences object for a pending task
     *
     * @param $fileId       int    local CcFiles identifier
     * @param $brokerTaskId int    broker task identifier to so we can asynchronously
     *                             receive completed task messages
     * @param $taskName     string broker task name
     *
     * @throws Exception
     * @throws PropelException
     */
    protected function _createTaskReference($fileId, $brokerTaskId, $taskName) {
        // First, check if the track already has an entry in the database
        $ref = ThirdPartyTrackReferencesQuery::create()
            ->filterByDbService($this->_SERVICE_NAME)
            ->findOneByDbFileId($fileId);
        if (is_null($ref)) {
            $ref = new ThirdPartyTrackReferences();
        }
        $ref->setDbService($this->_SERVICE_NAME);
        $ref->setDbBrokerTaskId($brokerTaskId);
        $ref->setDbBrokerTaskName($taskName);
        $utc = new DateTimeZone("UTC");
        $ref->setDbBrokerTaskDispatchTime(new DateTime("now", $utc));
        $ref->setDbFileId($fileId);
        $ref->setDbStatus($this->_PENDING_STATUS);
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
     * Poll the message queue for this service to see if any tasks with the given name have completed
     * If we find any completed tasks, adjust the ThirdPartyTrackReferences table accordingly
     * If no task name is passed, we poll all tasks for this service
     *
     * @param string $taskName the name of the task to poll for
     */
    public function pollBrokerTaskQueue($taskName="") {
        $pendingTasks = $this->_getPendingTasks($taskName);
        foreach ($pendingTasks as $task) {
            try {
                $result = $this->_getTaskResult($task);
                Logging::info(json_decode($result));
                $this->_addOrUpdateTrackReference($task->getDbFileId(), json_decode($result));
            } catch(CeleryException $e) {
                Logging::info("Couldn't retrieve task message for task " . $task->getDbBrokerTaskName()
                              . " with ID " . $task->getDbBrokerTaskId() . ": " . $e->getMessage());
                if ($this->_checkMessageTimeout($task)) {
                    $task->setDbStatus($this->_FAILED_STATUS);
                    $task->save();
                }
            }
        }
    }

    /**
     * Return a collection of all pending ThirdPartyTrackReferences to tasks for this service or task
     *
     * @param string $taskName the name of the task to look for
     *
     * @return PropelCollection any pending ThirdPartyTrackReferences results for this service
     *                          or task if taskName is provided
     */
    protected function _getPendingTasks($taskName) {
        $query = ThirdPartyTrackReferencesQuery::create()
            ->filterByDbService($this->_SERVICE_NAME)
            ->filterByDbStatus($this->_PENDING_STATUS)
            ->filterByDbBrokerTaskId('', Criteria::NOT_EQUAL);
        if (!empty($taskName)) {
            $query->filterByDbBrokerTaskName($taskName);
        }
        return $query->find();
    }

    /**
     * Get the result from a celery task message in the results queue
     * If the task message no longer exists, remove it from the track references table
     *
     * @param $task ThirdPartyTrackReferences the track reference object
     *
     * @return array the results from the task message
     *
     * @throws CeleryException when the result message for this task no longer exists
     */
    protected function _getTaskResult($task) {
        $message = Application_Model_RabbitMq::getAsyncResultMessage($task->getDbBrokerTaskName(),
                                                                     $task->getDbBrokerTaskId());
        return json_decode($message['body'])->result;  // The actual result message from the service
    }

    /**
     * Check if a task message has been unreachable for more our timeout time
     *
     * @param $task ThirdPartyTrackReferences the track reference object
     *
     * @return bool true if the dispatch time is empty or it's been more than our timeout time
     *              since the message was dispatched, otherwise false
     */
    protected function _checkMessageTimeout($task) {
        $utc = new DateTimeZone("UTC");
        $dispatchTime = new DateTime($task->getDbBrokerTaskDispatchTime(), $utc);
        $now = new DateTime("now", $utc);
        $timeoutSeconds = Application_Model_RabbitMq::$_CELERY_MESSAGE_TIMEOUT / 1000;  // Convert from milliseconds
        $timeoutInterval = new DateInterval("PT" . $timeoutSeconds . "S");
        return (empty($dispatchTime) || $dispatchTime->add($timeoutInterval) <= $now);
    }

    /**
     * Build a parameter array for the file being uploaded to a third party service
     *
     * @param $file Application_Model_StoredFile the file being uploaded
     *
     * @return array the track array to send to the third party service
     */
    abstract protected function _getUploadData($file);

    /**
     * Update a ThirdPartyTrackReferences object for a completed task
     *
     * @param $fileId int    local CcFiles identifier
     * @param $track  object third-party service track object
     *
     * @throws Exception
     * @throws PropelException
     */
    abstract protected function _addOrUpdateTrackReference($fileId, $track);

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