<?php

/**
 * Class TaskManager
 */
final class TaskManager {

    /**
     * @var array tasks to be run
     */
    protected $_taskList = [
        AirtimeTask::SOUNDCLOUD,
        AirtimeTask::UPGRADE
    ];

    /**
     * @var TaskManager singleton instance object
     */
    protected static $_instance;

    /**
     * Private constructor so class is uninstantiable
     */
    private function __construct() {
    }

    /**
     * Get the singleton instance of this class
     *
     * @return TaskManager the TaskManager instance
     */
    public static function getInstance() {
        if (!self::$_instance) {
            self::$_instance = new TaskManager();
        }
        return self::$_instance;
    }

    /**
     * Run all tasks that need to be run
     */
    public function runTasks() {
        foreach ($this->_taskList as $task) {
            $task = TaskFactory::getTask($task);
            assert(is_subclass_of($task, 'AirtimeTask'));  // Sanity check
            /** @var $task AirtimeTask */
            if ($task && $task->shouldBeRun()) $task->run();
        }
    }

}

/**
 * Interface AirtimeTask Interface for task operations - also acts as task type ENUM
 */
interface AirtimeTask {

    /**
     * PHP doesn't have ENUMs so declare them as interface constants
     * Task types - values don't really matter as long as they're unique
     */

    const SOUNDCLOUD = "soundcloud";
    const UPGRADE = "upgrade";

    /**
     * Check whether the task should be run
     *
     * @return bool true if the task needs to be run, otherwise false
     */
    public function shouldBeRun();

    /**
     * Run the task
     *
     * @return void
     */
    public function run();

}

/**
 * Class TaskFactory Factory class to abstract task instantiation
 */
class TaskFactory {

    /**
     * Get an AirtimeTask based on a task type
     *
     * @param $task string the task type; uses AirtimeTask constants as an ENUM
     *
     * @return AirtimeTask|null return a task of the given type or null if no corresponding
     *                          task exists or is implemented
     */
    public static function getTask($task) {
        switch($task) {
            case AirtimeTask::SOUNDCLOUD:
                return new SoundcloudUploadTask();
            case AirtimeTask::UPGRADE:
                return new UpgradeTask();
        }
        return null;
    }

}

/**
 * Class UpgradeTask
 */
class UpgradeTask implements AirtimeTask {

    /**
     * Check the current Airtime schema version to see if an upgrade should be run
     *
     * @return bool true if an upgrade is needed
     */
    public function shouldBeRun() {
        return UpgradeManager::checkIfUpgradeIsNeeded();
    }

    /**
     * Run all upgrades above the current schema version
     */
    public function run() {
        UpgradeManager::doUpgrade();
    }

}

/**
 * Class SoundcloudUploadTask
 */
class SoundcloudUploadTask implements AirtimeTask {

    /**
     * @var SoundcloudService
     */
    protected $_service;

    public function __construct() {
        $this->_service = new SoundcloudService();
    }

    /**
     * Check the ThirdPartyTrackReferences table to see if there are any pending SoundCloud tasks
     *
     * @return bool true if there are pending tasks in ThirdPartyTrackReferences
     */
    public function shouldBeRun() {
        return !$this->_service->isBrokerTaskQueueEmpty();
    }

    /**
     * Poll the task queue for any completed Celery tasks
     */
    public function run() {
        $this->_service->pollBrokerTaskQueue();
    }

}