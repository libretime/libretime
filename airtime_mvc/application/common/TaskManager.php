<?php

/**
 * Class TaskManager
 *
 * When adding a new task, the new AirtimeTask class will also need to be added
 * as a class constant and to the array in TaskFactory
 */
final class TaskManager {

    /**
     * @var array tasks to be run. Maps task names to a boolean value denoting
     *            whether the task has been checked/run
     */
    protected $_taskList;

    /**
     * @var TaskManager singleton instance object
     */
    protected static $_instance;

    /**
     * @var int TASK_INTERVAL_SECONDS how often, in seconds, to run the TaskManager tasks
     */
    const TASK_INTERVAL_SECONDS = 30;

    /**
     *
     * @var $con PDO Propel connection object
     */
    private $_con;

    /**
     * Private constructor so class is uninstantiable
     */
    private function __construct() {
        foreach (array_keys(TaskFactory::$tasks) as $v) {
            $this->_taskList[$v] = false;
        }
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
     * Run a single task.
     *
     * @param string $taskName the ENUM name of the task to be run
     */
    public function runTask($taskName) {
        $task = TaskFactory::getTask($taskName);
        if ($task && $task->shouldBeRun()) {
            $task->run();
        }
        $this->_taskList[$taskName] = true;  // Mark that the task has been checked/run.
                                             // This is important for prioritized tasks that
                                             // we need to run on every request (such as the
                                             // schema check/upgrade)
    }

    /**
     * Run all tasks that need to be run.
     *
     * To prevent blocking and making too many requests to the database,
     * we implement a row-level, non-blocking, read-protected lock on a
     * timestamp that we check each time the application is bootstrapped,
     * which, assuming enough time has passed, is updated before running
     * the tasks.
     */
    public function runTasks() {
        // If there is data in auth storage, this could be a user request
        // so we should just return to avoid blocking
        if ($this->_isUserSessionRequest()) {
            return;
        }
        $this->_con = Propel::getConnection(CcPrefPeer::DATABASE_NAME);
        $this->_con->beginTransaction();
        try {
            $lock = $this->_getLock();
            if ($lock && (microtime(true) < $lock['valstr'] + self::TASK_INTERVAL_SECONDS)) {
                // Propel caches the database connection and uses it persistently, so if we don't
                // use commit() here, we end up blocking other queries made within this request
                $this->_con->commit();
                return;
            }
            $this->_updateLock($lock);
            $this->_con->commit();
        } catch (Exception $e) {
            // We get here if there are simultaneous requests trying to fetch the lock row
            $this->_con->rollBack();
            Logging::warn($e->getMessage());
            return;
        }
        foreach ($this->_taskList as $task => $hasTaskRun) {
            if (!$hasTaskRun) {
                $this->runTask($task);
            }
        }
    }

    /**
     * Check if the current session is a user request
     *
     * @return bool true if there is a Zend_Auth object in the current session,
     *              otherwise false
     */
    private function _isUserSessionRequest() {
        if (!Zend_Session::isStarted()) {
            return false;
        }
        $auth = Zend_Auth::getInstance();
        $data = $auth->getStorage()->read();
        return !empty($data);
    }

    /**
     * Get the task_manager_lock from cc_pref with a row-level lock for atomicity
     *
     * The lock is exclusive (prevent reads) and will only last for the duration
     * of the transaction. We add NOWAIT so reads on the row during the transaction
     * won't block
     *
     * @return array|bool an array containing the row values, or false on failure
     */
    private function _getLock() {
        $sql = "SELECT * FROM cc_pref WHERE keystr='task_manager_lock' LIMIT 1 FOR UPDATE NOWAIT";
        $st = $this->_con->prepare($sql);
        $st->execute();
        return $st->fetch();
    }

    /**
     * Update and commit the new lock value, or insert it if it doesn't exist
     *
     * @param $lock array cc_pref lock row values
     */
    private function _updateLock($lock) {
        $sql = empty($lock) ? "INSERT INTO cc_pref (keystr, valstr) VALUES ('task_manager_lock', :value)"
            : "UPDATE cc_pref SET valstr=:value WHERE keystr='task_manager_lock'";
        $st = $this->_con->prepare($sql);
        $st->execute(array(":value" => microtime(true)));
    }

}

/**
 * Interface AirtimeTask Interface for task operations
 */
interface AirtimeTask {

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
 * Class CeleryTask
 */
class CeleryTask implements AirtimeTask {

    /**
     * Check the ThirdPartyTrackReferences table to see if there are any pending tasks
     *
     * @return bool true if there are pending tasks in ThirdPartyTrackReferences
     */
    public function shouldBeRun() {
        return !CeleryManager::isBrokerTaskQueueEmpty();
    }

    /**
     * Poll the task queue for any completed Celery tasks
     */
    public function run() {
        CeleryManager::pollBrokerTaskQueue();
    }

}

/**
 * Class PodcastTask
 */
class PodcastTask implements AirtimeTask {

    /**
     * Check whether or not the podcast polling interval has passed
     *
     * @return bool true if the podcast polling interval has passed
     */
    public function shouldBeRun() {
        return PodcastManager::hasPodcastPollIntervalPassed();
    }

    /**
     * Download the latest episode for all podcasts flagged for automatic ingest
     */
    public function run() {
        PodcastManager::downloadNewestEpisodes();
    }

}

/**
 * Class StationPodcastTask
 */
class StationPodcastTask implements AirtimeTask {

    const STATION_PODCAST_RESET_TIMER_SECONDS = 2.628e+6;  // 1 month XXX: should we use datetime roll for this instead?

    /**
     * Check whether or not the download counter for the station podcast should be reset
     *
     * @return bool true if enough time has passed
     */
    public function shouldBeRun() {
        $lastReset = Application_Model_Preference::getStationPodcastDownloadResetTimer();
        return empty($lastReset) || (microtime(true) > $lastReset + self::STATION_PODCAST_RESET_TIMER_SECONDS);
    }

    /**
     * Reset the station podcast download counter
     */
    public function run() {
        Application_Model_Preference::resetStationPodcastDownloadCounter();
    }

}

/**
 * Class TaskFactory Factory class to abstract task instantiation
 */
class TaskFactory {

    /**
     * PHP doesn't have ENUMs so declare them as interface constants
     * Task types - values don't really matter as long as they're unique
     */

    const UPGRADE           = "upgrade";
    const CELERY            = "celery";
    const PODCAST           = "podcast";
    const STATION_PODCAST   = "station-podcast";

    /**
     * @var array map of arbitrary identifiers to class names to be instantiated reflectively
     */
    public static $tasks = array(
        "upgrade"           => "UpgradeTask",
        "celery"            => "CeleryTask",
        "podcast"           => "PodcastTask",
        "station-podcast"   => "StationPodcastTask",
    );

    /**
     * Get an AirtimeTask based on a task type
     *
     * @param $task string the task type; uses AirtimeTask constants as an ENUM
     *
     * @return AirtimeTask|null return a task of the given type or null if no corresponding
     *                          task exists or is implemented
     */
    public static function getTask($task) {
        return new self::$tasks[$task]();
    }

}
