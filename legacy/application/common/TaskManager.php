<?php

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
     * @var int TASK_LOCK_KEY postgres advisory lock key used to serialize the TaskManager runs
     */
    public const TASK_LOCK_KEY = 1_634_952_052;

    /**
     * @var PDO Propel connection object
     */
    private $_con;

    /**
     * @var bool whether this request holds the TaskManager advisory lock
     */
    private $_locked = false;

    /**
     * @var bool whether unlock() was registered as a shutdown function
     */
    private $_unlockRegistered = false;

    /**
     * Private constructor so class is uninstantiable.
     */
    private function __construct()
    {
        foreach (TaskFactory::getTasks() as $task) {
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
    public function runTask($taskName): void
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
     * we acquire a non-blocking session-level advisory lock before
     * checking a timestamp each time the application is bootstrapped,
     * which, assuming enough time has passed, is updated before running
     * the tasks.
     *
     * The lock is held until the tasks are done, so that a task taking longer
     * than TASK_INTERVAL_SECONDS (e.g. a large autoplaylist build) is never run
     * concurrently by another request.
     */
    public function runTasks(): void
    {
        // If there is data in auth storage, this could be a user request
        // so we should just return to avoid blocking
        if ($this->_isUserSessionRequest()) {
            return;
        }
        $this->_con = Propel::getConnection(CcPrefPeer::DATABASE_NAME);

        try {
            if (!$this->_tryLock()) {
                // Another request is already checking/running the tasks
                return;
            }
        } catch (PDOException $e) {
            Logging::warn($e->getMessage());

            return;
        }

        try {
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
                $this->_con->rollBack();
                Logging::warn($e->getMessage());

                return;
            }
            foreach ($this->_taskList as $task => $hasTaskRun) {
                if (!$hasTaskRun) {
                    $this->runTask($task);
                }
            }
        } finally {
            $this->unlock();
        }
    }

    /**
     * Release the TaskManager advisory lock, if held.
     *
     * Public so it can be registered as a shutdown function: Propel uses persistent
     * connections, so a session-level lock is not released when the request dies
     * (e.g. max_execution_time) and would block the TaskManager in every other worker.
     */
    public function unlock(): void
    {
        if (!$this->_locked) {
            return;
        }

        try {
            // A failed task may have left an aborted transaction behind
            if ($this->_con->isInTransaction()) {
                $this->_con->forceRollBack();
            }
            $st = $this->_con->prepare('SELECT pg_advisory_unlock(:key)');
            $st->execute([':key' => self::TASK_LOCK_KEY]);
            $this->_locked = false;
        } catch (Throwable $e) {
            Logging::error('could not release the task manager lock: ' . $e->getMessage());
        }
    }

    /**
     * Check if the current session is a user request.
     *
     * @return bool true if there is a Zend_Auth object in the current session,
     *              otherwise false
     */
    private function _isUserSessionRequest(): bool
    {
        if (!Zend_Session::isStarted()) {
            return false;
        }
        $auth = Zend_Auth::getInstance();
        $data = $auth->getStorage()->read();

        return !empty($data);
    }

    /**
     * Try to acquire the TaskManager advisory lock without blocking.
     *
     * The lock is held by the database session until unlock() is called. Unlike a
     * row-level lock with NOWAIT, failing to acquire the lock does not raise an error
     * (which would abort the transaction and be logged by postgres).
     *
     * @return bool true if the lock was acquired, otherwise false
     */
    private function _tryLock(): bool
    {
        $st = $this->_con->prepare('SELECT pg_try_advisory_lock(:key)');
        $st->execute([':key' => self::TASK_LOCK_KEY]);
        if (!$st->fetchColumn()) {
            return false;
        }

        $this->_locked = true;
        if (!$this->_unlockRegistered) {
            register_shutdown_function([$this, 'unlock']);
            $this->_unlockRegistered = true;
        }

        return true;
    }

    /**
     * Get the task_manager_lock from cc_pref.
     *
     * Must be called while holding the TaskManager advisory lock.
     *
     * @return array|bool an array containing the row values, or false on failure
     */
    private function _getLock()
    {
        $sql = "SELECT * FROM cc_pref WHERE keystr='task_manager_lock' LIMIT 1";
        $st = $this->_con->prepare($sql);
        $st->execute();

        return $st->fetch();
    }

    /**
     * Update and commit the new lock value, or insert it if it doesn't exist.
     *
     * @param $lock array cc_pref lock row values
     */
    private function _updateLock($lock): void
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
    public function run(): void
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
    public function shouldBeRun(): bool
    {
        $overQuota = Application_Model_Systemstatus::isDiskOverQuota();

        return !$overQuota && PodcastManager::hasPodcastPollIntervalPassed();
    }

    /**
     * Download the latest episode for all podcasts flagged for automatic ingest.
     */
    public function run(): void
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
    public function run(): void
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
    public function shouldBeRun(): bool
    {
        $lastReset = Application_Model_Preference::getStationPodcastDownloadResetTimer();

        return empty($lastReset) || (microtime(true) > ($lastReset + self::STATION_PODCAST_RESET_TIMER_SECONDS));
    }

    /**
     * Reset the station podcast download counter.
     */
    public function run(): void
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
    private static function _isTask($c): bool
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
        return array_filter(get_declared_classes(), [self::class, '_isTask']);
    }

    /**
     * Get an AirtimeTask based on class name.
     *
     * @param $task string name of the class implementing AirtimeTask to construct
     *
     * @return null|AirtimeTask return a task of the given type or null if no corresponding task exists
     */
    public static function getTask($task): ?AirtimeTask
    {
        // Try to get a valid class name from the given string
        if (!class_exists($task)) {
            $task = str_replace(' ', '', ucwords($task)) . 'Task';
        }

        return class_exists($task) ? new $task() : null;
    }
}
