<?php

abstract class Application_Service_ThirdPartyCeleryService extends Application_Service_ThirdPartyService
{
    /**
     * @var string broker exchange name for third-party tasks
     */
    protected static $_CELERY_EXCHANGE_NAME;

    /**
     * @var array map of celery identifiers to their task names
     */
    protected static $_CELERY_TASKS;

    /**
     * Execute a Celery task with the given name and data parameters.
     *
     * @param string $taskName the name of the celery task to execute
     * @param array  $data     the data array to send as task parameters
     * @param int    $fileId   the unique identifier for the file involved in the task
     *
     * @return CeleryTasks the created task
     *
     * @throws Exception
     */
    protected function _executeTask($taskName, $data, $fileId = null)
    {
        try {
            $brokerTaskId = CeleryManager::sendCeleryMessage(
                $taskName,
                static::$_CELERY_EXCHANGE_NAME,
                $data
            );

            return $this->_createTaskReference($fileId, $brokerTaskId, $taskName);
        } catch (Exception $e) {
            Logging::error('Invalid request: ' . $e->getMessage());

            throw $e;
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
     * @return CeleryTasks the created task
     *
     * @throws Exception
     * @throws PropelException
     */
    protected function _createTaskReference($fileId, $brokerTaskId, $taskName)
    {
        $trackReferenceId = $this->createTrackReference($fileId);
        $task = new CeleryTasks();
        $task->setDbTaskId($brokerTaskId);
        $task->setDbName($taskName);
        $utc = new DateTimeZone('UTC');
        $task->setDbDispatchTime(new DateTime('now', $utc));
        $task->setDbStatus(CELERY_PENDING_STATUS);
        $task->setDbTrackReference($trackReferenceId);
        $task->save();

        return $task;
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
    public function updateTask($task, $status)
    {
        $task->setDbStatus($status);
        $task->save();
    }

    /**
     * Update a ThirdPartyTrackReferences object for a completed upload.
     *
     * Manipulation and use of the track object is left up to child implementations
     *
     * @param $task     CeleryTasks the completed CeleryTasks object
     * @param $trackId  int         ThirdPartyTrackReferences identifier
     * @param $result   mixed       Celery task result message
     * @param $status   string      Celery task status
     *
     * @return ThirdPartyTrackReferences the updated ThirdPartyTrackReferences object
     *
     * @throws Exception
     * @throws PropelException
     */
    public function updateTrackReference($task, $trackId, $result, $status)
    {
        static::updateTask($task, $status);
        $ref = ThirdPartyTrackReferencesQuery::create()
            ->findOneByDbId($trackId);
        if (is_null($ref)) {
            $ref = new ThirdPartyTrackReferences();
        }
        $ref->setDbService(static::$_SERVICE_NAME);
        $utc = new DateTimeZone('UTC');
        $ref->setDbUploadTime(new DateTime('now', $utc));
        $ref->save();

        return $ref;
    }
}
