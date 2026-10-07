<?php

/**
 * Records what the test tasks observed while running.
 */
class TaskManagerTestRecorder
{
    /** @var string[] names of the tasks that ran, in order */
    public static $ran = [];

    /** @var null|bool whether another session could acquire the lock while a task was running */
    public static $lockAvailableDuringRun;

    /** @var null|PDO connection used to look at the lock from another session */
    public static $otherSession;

    public static function reset(): void
    {
        self::$ran = [];
        self::$lockAvailableDuringRun = null;
        self::$otherSession = null;
    }
}

class TaskManagerTestOkTask implements AirtimeTask
{
    public function shouldBeRun()
    {
        return true;
    }

    public function run(): void
    {
        TaskManagerTestRecorder::$ran[] = self::class;
    }
}

class TaskManagerTestFailingTask implements AirtimeTask
{
    public function shouldBeRun()
    {
        return true;
    }

    public function run(): void
    {
        TaskManagerTestRecorder::$ran[] = self::class;

        throw new PDOException('SQLSTATE[40P01]: Deadlock detected');
    }
}

class TaskManagerTestUnfinishedTransactionTask implements AirtimeTask
{
    public function shouldBeRun()
    {
        return true;
    }

    public function run(): void
    {
        TaskManagerTestRecorder::$ran[] = self::class;

        // Leave an aborted transaction behind, as a task failing half way would
        $con = Propel::getConnection(CcPrefPeer::DATABASE_NAME);
        $con->beginTransaction();

        try {
            $con->exec('SELECT * FROM table_that_does_not_exist');
        } catch (PDOException $e) {
            throw new RuntimeException('task failed', 0, $e);
        }
    }
}

class TaskManagerTestLockProbeTask implements AirtimeTask
{
    public function shouldBeRun()
    {
        return true;
    }

    public function run(): void
    {
        TaskManagerTestRecorder::$ran[] = self::class;

        $st = TaskManagerTestRecorder::$otherSession->prepare('SELECT pg_try_advisory_lock(:key)');
        $st->execute([':key' => TaskManager::TASK_LOCK_KEY]);
        TaskManagerTestRecorder::$lockAvailableDuringRun = (bool) $st->fetchColumn();
        if (TaskManagerTestRecorder::$lockAvailableDuringRun) {
            TaskManagerTestRecorder::$otherSession
                ->prepare('SELECT pg_advisory_unlock(:key)')
                ->execute([':key' => TaskManager::TASK_LOCK_KEY]);
        }
    }
}

/**
 * @internal
 *
 * @coversNothing
 */
class TaskManagerTest extends PHPUnit_Framework_TestCase
{
    /** @var PDO a separate database session, like another php worker would have */
    private $otherSession;

    public function setUp(): void
    {
        TestHelper::installTestDatabase();
        TestHelper::setupZendBootstrap();
        parent::setUp();

        // The task manager does not run for logged in users
        Zend_Auth::getInstance()->clearIdentity();

        Propel::getConnection(CcPrefPeer::DATABASE_NAME)
            ->exec("DELETE FROM cc_pref WHERE keystr = 'task_manager_lock'");

        // Propel uses a persistent connection, so this one must not be persistent
        // to get a different database session.
        $config = Config::getConfig();
        $this->otherSession = new PDO(
            "pgsql:host={$config['dsn']['host']};port={$config['dsn']['port']};dbname={$config['dsn']['database']}",
            $config['dsn']['username'],
            $config['dsn']['password'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        TaskManagerTestRecorder::reset();
        TaskManagerTestRecorder::$otherSession = $this->otherSession;
    }

    public function tearDown(): void
    {
        $this->otherSession = null;
        TaskManagerTestRecorder::reset();
        parent::tearDown();
    }

    /**
     * Build a TaskManager running only the given tasks.
     *
     * @param string[] $tasks
     */
    private function createTaskManager(array $tasks): TaskManager
    {
        $class = new ReflectionClass(TaskManager::class);
        $taskManager = $class->newInstanceWithoutConstructor();

        $taskList = $class->getProperty('_taskList');
        $taskList->setValue($taskManager, array_fill_keys($tasks, false));

        return $taskManager;
    }

    private function getLockTimestamp()
    {
        return $this->otherSession
            ->query("SELECT valstr FROM cc_pref WHERE keystr = 'task_manager_lock'")
            ->fetchColumn();
    }

    private function setLockTimestamp(float $value): void
    {
        $this->otherSession
            ->prepare("INSERT INTO cc_pref (keystr, valstr) VALUES ('task_manager_lock', :value)")
            ->execute([':value' => $value]);
    }

    private function isLockAvailable(): bool
    {
        $st = $this->otherSession->prepare('SELECT pg_try_advisory_lock(:key)');
        $st->execute([':key' => TaskManager::TASK_LOCK_KEY]);
        $acquired = (bool) $st->fetchColumn();
        if ($acquired) {
            $this->otherSession
                ->prepare('SELECT pg_advisory_unlock(:key)')
                ->execute([':key' => TaskManager::TASK_LOCK_KEY]);
        }

        return $acquired;
    }

    public function testRunTasks(): void
    {
        $this->createTaskManager([TaskManagerTestOkTask::class])->runTasks();

        $this->assertEquals([TaskManagerTestOkTask::class], TaskManagerTestRecorder::$ran);
        $this->assertNotSame(false, $this->getLockTimestamp());
        $this->assertTrue($this->isLockAvailable());
    }

    public function testRunTasksSkippedWhenIntervalNotPassed(): void
    {
        $lastRun = microtime(true);
        $this->setLockTimestamp($lastRun);

        $this->createTaskManager([TaskManagerTestOkTask::class])->runTasks();

        $this->assertEquals([], TaskManagerTestRecorder::$ran);
        $this->assertEquals($lastRun, (float) $this->getLockTimestamp(), '', 0.001);
        $this->assertTrue($this->isLockAvailable());
    }

    public function testRunTasksWhenIntervalPassed(): void
    {
        $lastRun = microtime(true) - TaskManager::TASK_INTERVAL_SECONDS - 1;
        $this->setLockTimestamp($lastRun);

        $this->createTaskManager([TaskManagerTestOkTask::class])->runTasks();

        $this->assertEquals([TaskManagerTestOkTask::class], TaskManagerTestRecorder::$ran);
        $this->assertGreaterThan($lastRun, (float) $this->getLockTimestamp());
    }

    public function testRunTasksSkippedWhenLockedByAnotherSession(): void
    {
        $this->otherSession
            ->prepare('SELECT pg_advisory_lock(:key)')
            ->execute([':key' => TaskManager::TASK_LOCK_KEY]);

        try {
            $this->createTaskManager([TaskManagerTestOkTask::class])->runTasks();
        } finally {
            $this->otherSession
                ->prepare('SELECT pg_advisory_unlock(:key)')
                ->execute([':key' => TaskManager::TASK_LOCK_KEY]);
        }

        $this->assertEquals([], TaskManagerTestRecorder::$ran);
        $this->assertFalse($this->getLockTimestamp());
    }

    public function testLockIsHeldWhileTasksAreRunning(): void
    {
        $this->createTaskManager([TaskManagerTestLockProbeTask::class])->runTasks();

        $this->assertEquals([TaskManagerTestLockProbeTask::class], TaskManagerTestRecorder::$ran);
        $this->assertFalse(
            TaskManagerTestRecorder::$lockAvailableDuringRun,
            'another session must not be able to run the tasks while they are running'
        );
        $this->assertTrue($this->isLockAvailable());
    }

    public function testLockReleasedWhenTaskFails(): void
    {
        try {
            $this->createTaskManager([
                TaskManagerTestFailingTask::class,
                TaskManagerTestOkTask::class,
            ])->runTasks();
            $this->fail('the task exception should be raised');
        } catch (PDOException $e) {
            $this->assertEquals('SQLSTATE[40P01]: Deadlock detected', $e->getMessage());
        }

        $this->assertEquals([TaskManagerTestFailingTask::class], TaskManagerTestRecorder::$ran);
        $this->assertTrue($this->isLockAvailable());
    }

    public function testLockReleasedWhenTaskLeftUnfinishedTransaction(): void
    {
        try {
            $this->createTaskManager([TaskManagerTestUnfinishedTransactionTask::class])->runTasks();
            $this->fail('the task exception should be raised');
        } catch (RuntimeException $e) {
            $this->assertEquals('task failed', $e->getMessage());
        }

        $this->assertEquals([TaskManagerTestUnfinishedTransactionTask::class], TaskManagerTestRecorder::$ran);
        $this->assertFalse(Propel::getConnection(CcPrefPeer::DATABASE_NAME)->isInTransaction());
        $this->assertTrue($this->isLockAvailable());
    }

    public function testUnlockIsIdempotent(): void
    {
        $taskManager = $this->createTaskManager([TaskManagerTestOkTask::class]);
        $taskManager->runTasks();

        // Called again by the shutdown function
        $taskManager->unlock();

        $this->assertTrue($this->isLockAvailable());
    }
}
