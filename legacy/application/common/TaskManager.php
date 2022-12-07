<?php

declare(strict_types=1);

/**
 * Class TaskManager.
 *
 * Background class for 'asynchronous' task management for Airtime stations
 */
final class TaskManager
{
    /**
     * @var array tasks to be run. Maps task names to a boolean value denoting
     *            whether the task has been checked/run
     */
    private $_taskList;

    /**
     * @var TaskManager singleton instance object
     */
    private static $_instance;

    /**
     * @var int TASK_INTERVAL_SECONDS how often, in seconds, to run the TaskManager tasks
     */
    public const TASK_INTERVAL_SECONDS = 30;

    /**
     * @var PDO Propel connection object
     */
    private $_con;

    /**
     * Private constructor so class is uninstantiable.
     */
    private function __construct()
    {
        foreach (TaskFactory::getTasks() as $k => $task) {
            $this->_taskList[$task] = false;
        }
    }

    /**
     * Get the singleton instance of this class.
     *
     * @return TaskManager the TaskManager instance
     */
    public static function getInstance()
    {
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
    public function runTask($taskName)
    {
        $task = TaskFactory::getTask($taskName);
        if ($task && $task->shouldBeRun()) {
            Logging::debug("running task {$taskName}");
            $task->run();
        }
        // Mark that the task has been checked/run.
        // This is important for prioritized tasks that
        // we need to run on every request (such as the
        // schema check/upgrade)
        $this->_taskList[$taskName] = true;
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
    public function runTasks()
    {
        // If there is data in auth storage, this could be a user request
        // so we should just return to avoid blocking
        if ($this->_isUserSessionRequest()) {
            return;
        }
        $this->_con = Propel::getConnection(CcPrefPeer::DATABASE_NAME);
        $this->_con->beginTransaction();

        try {
            $lock = $this->_getLock();
            if ($lock && (microtime(true) < ($lock['valstr'] + self::TASK_INTERVAL_SECONDS))) {
                // Propel caches the database connection and uses it persistently, so if we don't
                // use commit() here, we end up blocking other queries made within this request
                $this->_con->commit();

                return;
            }
            $this->_updateLock($lock);
            $this->_con->commit();
        } catch (PDOException $e) {
            // We get here if there are simultaneous requests trying to fetch the lock row
            $this->_con->rollBack();

            // Do not log 'could not obtain lock' exception
            // SQLSTATE[55P03]: Lock not available: 7 ERROR:  could not obtain lock on row in relation "cc_pref"
            if ($e->getCode() != '55P03') {
                Logging::warn($e->getMessage());
            }

            return;
        }
        foreach ($this->_taskList as $task => $hasTaskRun) {
            if (!$hasTaskRun) {
                $this->runTask($task);
            }
        }
    }

    /**
     * Check if the current session is a user request.
     *
     * @return bool true if there is a Zend_Auth object in the current session,
     *              otherwise false
     */
    private function _isUserSessionRequest()
    {
        if (!Zend_Session::isStarted()) {
            return false;
        }
        $auth = Zend_Auth::getInstance();
        $data = $auth->getStorage()->read();

        return !empty($data);
    }

    /**
     * Get the task_manager_lock from cc_pref with a row-level lock for atomicity.
     *
     * The lock is exclusive (prevent reads) and will only last for the duration
     * of the transaction. We add NOWAIT so reads on the row during the transaction
     * won't block
     *
     * @return array|bool an array containing the row values, or false on failure
     */
    private function _getLock()
    {
        $sql = "SELECT * FROM cc_pref WHERE keystr='task_manager_lock' LIMIT 1 FOR UPDATE NOWAIT";
        $st = $this->_con->prepare($sql);
        $st->execute();

        return $st->fetch();
    }

    /**
     * Update and commit the new lock value, or insert it if it doesn't exist.
     *
     * @param $lock array cc_pref lock row values
     */
    private function _updateLock($lock)
    {
        $sql = empty($lock) ? "INSERT INTO cc_pref (keystr, valstr) VALUES ('task_manager_lock', :value)"
            : "UPDATE cc_pref SET valstr=:value WHERE keystr='task_manager_lock'";
        $st = $this->_con->prepare($sql);
        $st->execute([':value' => microtime(true)]);
    }
}

/**
 * Interface AirtimeTask Interface for task operations.
 */
interface AirtimeTask
{
    /**
     * Check whether the task should be run.
     *
     * @return bool true if the task needs to be run, otherwise false
     */
    public function shouldBeRun();

    /**
     * Run the task.
     */
    public function run();
}

/**
 * Class CeleryTask.
 *
 * Checks the Celery broker task queue and runs callbacks for completed tasks
 */
class CeleryTask implements AirtimeTask
{
    /**
     * Check the ThirdPartyTrackReferences table to see if there are any pending tasks.
     *
     * @return bool true if there are pending tasks in ThirdPartyTrackReferences
     */
    public function shouldBeRun()
    {
        return !CeleryManager::isBrokerTaskQueueEmpty();
    }

    /**
     * Poll the task queue for any completed Celery tasks.
     */
    public function run()
    {
        CeleryManager::pollBrokerTaskQueue();
    }
}

/**
 * Class AutoPlaylistTask.
 *
 * Checks for shows with an autoplaylist that needs to be filled in
 */
class AutoPlaylistTask implements AirtimeTask
{
    /**
     * Checks whether or not the autoplaylist polling interval has passed.
     *
     * @return bool true if the autoplaylist polling interval has passed
     */
    public function shouldBeRun()
    {
        return AutoPlaylistManager::hasAutoPlaylistPollIntervalPassed();
    }

    /**
     *  Schedule the autoplaylist for the shows.
     */
    public function run()
    {
        AutoPlaylistManager::buildAutoPlaylist();
    }
}

/**
 * Class PodcastTask.
 *
 * Checks podcasts marked for automatic ingest and downloads any new episodes
 * since the task was last run
 */
class PodcastTask implements AirtimeTask
{
    /**
     * Check whether or not the podcast polling interval has passed.
     *
     * @return bool true if the podcast polling interval has passed
     */
    public function shouldBeRun()
    {
        $overQuota = Application_Model_Systemstatus::isDiskOverQuota();

        return !$overQuota && PodcastManager::hasPodcastPollIntervalPassed();
    }

    /**
     * Download the latest episode for all podcasts flagged for automatic ingest.
     */
    public function run()
    {
        PodcastManager::downloadNewestEpisodes();
    }
}

/**
 * Class ImportTask.
 */
class ImportCleanupTask implements AirtimeTask
{
    /**
     * Check if there are any files that have been stuck
     * in Pending status for over an hour.
     *
     * @return bool true if there are any files stuck pending,
     *              otherwise false
     */
    public function shouldBeRun()
    {
        return Application_Service_MediaService::areFilesStuckInPending();
    }

    /**
     * Clean up stuck imports by changing their import status to Failed.
     */
    public function run()
    {
        Application_Service_MediaService::clearStuckPendingImports();
    }
}

/**
 * Class StationPodcastTask.
 *
 * Checks the Station podcast rollover timer and resets allotted
 * downloads if enough time has passed (default: 1 month)
 */
class StationPodcastTask implements AirtimeTask
{
    public const STATION_PODCAST_RESET_TIMER_SECONDS = 2.628e+6;  // 1 month

    /**
     * Check whether or not the download counter for the station podcast should be reset.
     *
     * @return bool true if enough time has passed
     */
    public function shouldBeRun()
    {
        $lastReset = Application_Model_Preference::getStationPodcastDownloadResetTimer();

        return empty($lastReset) || (microtime(true) > ($lastReset + self::STATION_PODCAST_RESET_TIMER_SECONDS));
    }

    /**
     * Reset the station podcast download counter.
     */
    public function run()
    {
        Application_Model_Preference::resetStationPodcastDownloadCounter();
        Application_Model_Preference::setStationPodcastDownloadResetTimer(microtime(true));
    }
}

/**
 * Class TaskFactory Factory class to abstract task instantiation.
 */
class TaskFactory
{
    /**
     * Check if the class with the given name implements AirtimeTask.
     *
     * @param $c string class name
     *
     * @return bool true if the class $c implements AirtimeTask
     */
    private static function _isTask($c)
    {
        return array_key_exists('AirtimeTask', class_implements($c));
    }

    /**
     * Filter all declared classes to get all classes implementing the AirtimeTask interface.
     *
     * @return array all classes implementing the AirtimeTask interface
     */
    public static function getTasks()
    {
        return array_filter(get_declared_classes(), [__CLASS__, '_isTask']);
    }

    /**
     * Get an AirtimeTask based on class name.
     *
     * @param $task string name of the class implementing AirtimeTask to construct
     *
     * @return null|AirtimeTask return a task of the given type or null if no corresponding task exists
     */
    public static function getTask($task)
    {
        // Try to get a valid class name from the given string
        if (!class_exists($task)) {
            $task = str_replace(' ', '', ucwords($task)) . 'Task';
        }

        return class_exists($task) ? new $task() : null;
    }
}
