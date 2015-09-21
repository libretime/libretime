<?php

require_once "ThirdPartyService.php";

abstract class Application_Service_ThirdPartyCeleryService extends Application_Service_ThirdPartyService {

    /**
     * @var string broker exchange name for third-party tasks
     */
    protected static $_CELERY_EXCHANGE_NAME;

    /**
     * @var array map of celery identifiers to their task names
     */
    protected static $_CELERY_TASKS;

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
     * @param $task CeleryTasks the completed CeleryTasks object
     * @param $status string    Celery task status
     *
     * @throws Exception
     * @throws PropelException
     */
    public function updateTask($task, $status) {
        $task->setDbStatus($status);
        $task->save();
    }

    /**
     * Update a ThirdPartyTrackReferences object for a completed upload
     *
     * @param $task     CeleryTasks the completed CeleryTasks object
     * @param $trackId  int         ThirdPartyTrackReferences identifier
     * @param $track    object      third-party service track object
     * @param $status   string      Celery task status
     *
     * @throws Exception
     * @throws PropelException
     */
    abstract function updateTrackReference($task, $trackId, $track, $status);

}