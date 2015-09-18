<?php

require_once "ThirdPartyService.php";

abstract class Application_Service_ThirdPartyCeleryService extends Application_Service_ThirdPartyService {

    /**
     * @var string broker exchange name for third-party tasks
     */
    protected static $_CELERY_EXCHANGE_NAME;

    /**
     * @var string celery task name for third-party uploads
     */
    protected static $_CELERY_UPLOAD_TASK_NAME;

    /**
     * @var string celery task name for third-party uploads
     */
    protected static $_CELERY_DOWNLOAD_TASK_NAME;

    /**
     * @var string celery task name for third-party deletion
     */
    protected static $_CELERY_DELETE_TASK_NAME;

    /**
     * Execute a Celery task with the given name and data parameters
     *
     * FIXME: Currently, downloads will not create task reference rows because they
     * don't have a valid file identifier - this means that we will never know if there
     * is an issue with the download before the callback to /rest/media is called!
     *
     * @param string $taskName the name of the celery task to execute
     * @param array $data      the data array to send as task parameters
     * @param int $fileId      the unique identifier for the file involved in the task
     */
    protected function _executeTask($taskName, $data, $fileId) {
        try {
            $brokerTaskId = CeleryManager::sendCeleryMessage($taskName,
                                                             static::$_CELERY_EXCHANGE_NAME,
                                                             $data);
            if (!empty($fileId)) {
                $this->_createTaskReference($fileId, $brokerTaskId, $taskName);
            }
        } catch (Exception $e) {
            Logging::info("Invalid request: " . $e->getMessage());
        }
    }

    /**
     * Upload the file with the given identifier to a third-party service
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
        $this->_executeTask(static::$_CELERY_UPLOAD_TASK_NAME, $data, $fileId);
    }

    /**
     * Given a track identifier, download a track from a third-party service.
     *
     * @param int $trackId a track identifier
     */
    public function download($trackId) {
        $namespace = new Zend_Session_Namespace('csrf_namespace');
        $csrfToken = $namespace->authtoken;
        $data = array(
            'callback_url' => 'http' . (empty($_SERVER['HTTPS']) ? '' : 's') . '://' . $_SERVER['HTTP_HOST'] . '/media/post?csrf_token=' . $csrfToken,
            'token' => $this->_accessToken,
            'track_id' => $trackId
        );
        // FIXME
        Logging::warn("FIXME: we can't create a task reference without a valid file ID");
        $this->_executeTask(static::$_CELERY_DOWNLOAD_TASK_NAME, $data, null);
    }

    /**
     * Delete the file with the given identifier from a third-party service
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
        $this->_executeTask(static::$_CELERY_DELETE_TASK_NAME, $data, $fileId);
    }

    /**
     * Create a CeleryTasks object for a pending task
     * TODO: should we have a database layer class to handle Propel operations?
     *
     * @param $fileId       int    CcFiles identifier
     * @param $brokerTaskId int    broker task identifier to so we can asynchronously
     *                             receive completed task messages
     * @param $taskName     string broker task name
     *
     * @throws Exception
     * @throws PropelException
     */
    protected function _createTaskReference($fileId, $brokerTaskId, $taskName) {
        $trackReferenceId = $this->createTrackReference($fileId);
        $task = new CeleryTasks();
        $task->setDbTaskId($brokerTaskId);
        $task->setDbName($taskName);
        $utc = new DateTimeZone("UTC");
        $task->setDbDispatchTime(new DateTime("now", $utc));
        $task->setDbStatus(CELERY_PENDING_STATUS);
        $task->setDbTrackReference($trackReferenceId);
        $task->save();
    }

    /**
     * Update a CeleryTasks object for a completed task
     * TODO: should we have a database layer class to handle Propel operations?
     *
     * @param $trackId int    ThirdPartyTrackReferences identifier
     * @param $track  object  third-party service track object
     * @param $status string  Celery task status
     *
     * @throws Exception
     * @throws PropelException
     */
    public function updateTrackReference($trackId, $track, $status) {
        $task = CeleryTasksQuery::create()
            ->findOneByDbTrackReference($trackId);
        $task->setDbStatus($status);
        $task->save();
    }

    /**
     * Field accessor for $_CELERY_DELETE_TASK_NAME
     *
     * @return string the Celery task name for deleting tracks from this service
     */
    public function getCeleryDeleteTaskName() {
        return static::$_CELERY_DELETE_TASK_NAME;
    }

    /**
     * Build a parameter array for the file being uploaded to a third party service
     *
     * @param $file Application_Model_StoredFile the file being uploaded
     *
     * @return array the track array to send to the third party service
     */
    abstract protected function _getUploadData($file);

}