<?php

use Celery\Celery;
use Celery\CeleryException;
use Celery\CeleryTimeoutException;

class CeleryManager
{
    /**
     * @var int milliseconds (for compatibility with celery) until we consider a message to have timed out
     */
    private static $_CELERY_MESSAGE_TIMEOUT = 900000;  // 15 minutes

    /**
     * We have to use celeryresults (the default results exchange) because php-celery
     * doesn't support named results exchanges.
     *
     * @var string exchange for celery task results
     */
    private static $_CELERY_RESULTS_EXCHANGE = 'celeryresults';

    /**
     * @var PropelCollection cache of any pending CeleryTasks results for a service or task
     */
    private static $_pendingTasks;

    /**
     * Connect to the Celery daemon via amqp.
     *
     * @param $config   array  the airtime configuration array
     * @param $exchange string the amqp exchange name
     * @param $queue    string the amqp queue name
     *
     * @return Celery the Celery connection object
     *
     * @throws Exception when a connection error occurs
     */
    private static function _setupCeleryExchange($config, $exchange, $queue)
    {
        return new Celery(
            $config['rabbitmq']['host'],
            $config['rabbitmq']['user'],
            $config['rabbitmq']['password'],
            $config['rabbitmq']['vhost'],
            $exchange,
            $queue,
            $config['rabbitmq']['port'],
            false,
            self::$_CELERY_MESSAGE_TIMEOUT
        );  // Result expiration
    }

    /**
     * Send an amqp message to Celery the airtime-celery daemon to perform a task.
     *
     * @param $task     string the Celery task name
     * @param $exchange string the amqp exchange name
     * @param $data     array  an associative array containing arguments for the Celery task
     *
     * @return string the task identifier for the started Celery task so we can fetch the
     *                results asynchronously later
     */
    public static function sendCeleryMessage($task, $exchange, $data)
    {
        $config = Config::getConfig();
        $queue = $routingKey = $exchange;
        $c = self::_setupCeleryExchange($config, $exchange, $queue);  // Use the exchange name for the queue
        $result = $c->PostTask($task, $data, true, $routingKey);      // and routing key

        return $result->getId();
    }

    /**
     * Given a task name and identifier, check the Celery results queue for any
     * corresponding messages.
     *
     * @param $task CeleryTasks the Celery task object
     *
     * @return array the message response array
     *
     * @throws CeleryException        when no message is found
     * @throws CeleryTimeoutException when no message is found and more than
     *                                $_CELERY_MESSAGE_TIMEOUT milliseconds have passed
     */
    private static function getAsyncResultMessage($task)
    {
        $config = Config::getConfig();
        $queue = self::$_CELERY_RESULTS_EXCHANGE . '.' . $task;
        $c = self::_setupCeleryExchange($config, self::$_CELERY_RESULTS_EXCHANGE, $queue);
        $message = $c->getAsyncResultMessage($task->getDbName(), $task->getDbTaskId());

        // If the message isn't ready yet (Celery hasn't finished the task), throw an exception.
        if ($message == false) {
            if (static::_checkMessageTimeout($task)) {
                // If the task times out, mark it as failed. We don't want to remove the
                // track reference here in case it was a deletion that failed, for example.
                $task->setDbStatus(CELERY_FAILED_STATUS)->save();

                throw new CeleryTimeoutException('Celery task ' . $task->getDbName() . ' with ID ' . $task->getDbTaskId() . ' timed out');
            }

            // The message hasn't timed out, but it's still false, which means it hasn't been
            // sent back from Celery yet.
            throw new CeleryException('Waiting on Celery task ' . $task->getDbName() . ' with ID ' . $task->getDbTaskId());
        }

        return $message;
    }

    /**
     * Check to see if there are any pending tasks for this service.
     *
     * @param string $taskName    the name of the task to poll for
     * @param string $serviceName the name of the service to poll for
     *
     * @return bool true if there are any pending tasks, otherwise false
     */
    public static function isBrokerTaskQueueEmpty($taskName = '', $serviceName = '')
    {
        self::$_pendingTasks = static::_getPendingTasks($taskName, $serviceName);

        return empty(self::$_pendingTasks);
    }

    /**
     * Poll the message queue for this service to see if any tasks with the given name have completed.
     *
     * If we find any completed tasks, adjust the ThirdPartyTrackReferences table accordingly
     *
     * If no task name is passed, we poll all tasks for this service
     *
     * @param string $taskName    the name of the task to poll for
     * @param string $serviceName the name of the service to poll for
     */
    public static function pollBrokerTaskQueue($taskName = '', $serviceName = '')
    {
        $pendingTasks = empty(self::$_pendingTasks)
            ? static::_getPendingTasks($taskName, $serviceName)
            : self::$_pendingTasks;
        foreach ($pendingTasks as $task) {
            try {
                $message = static::_getTaskMessage($task);
                static::_processTaskMessage($task, $message);
            } catch (CeleryTimeoutException $e) {
                Logging::warn($e->getMessage());
            } catch (CeleryException $e) {
                // Don't log these - they end up clogging up the logs
            } catch (Exception $e) {
                // Because $message->result can be either an object or a string, sometimes
                // we get a json_decode error and end up here
                Logging::info($e->getMessage());
            }
        }
    }

    /**
     * Return a collection of all pending CeleryTasks for this service or task.
     *
     * @param string $taskName    the name of the task to find
     * @param string $serviceName the name of the service to find
     *
     * @return PropelCollection any pending CeleryTasks results for this service
     *                          or task if taskName is provided
     */
    protected static function _getPendingTasks($taskName, $serviceName)
    {
        $query = CeleryTasksQuery::create()
            ->filterByDbStatus(CELERY_PENDING_STATUS)
            ->filterByDbTaskId('', Criteria::NOT_EQUAL);
        if (!empty($taskName)) {
            $query->filterByDbName($taskName);
        }
        if (!empty($serviceName)) {
            $query->useThirdPartyTrackReferencesQuery()
                ->filterByDbService($serviceName)->endUse();
        }

        return $query->joinThirdPartyTrackReferences()
            ->with('ThirdPartyTrackReferences')->find();
    }

    /**
     * Get a Celery task message from the results queue.
     *
     * @param $task CeleryTasks the Celery task object
     *
     * @return object the task message object
     *
     * @throws CeleryException        when the result message for this task is still pending
     * @throws CeleryTimeoutException when the result message for this task no longer exists
     */
    protected static function _getTaskMessage($task)
    {
        $message = self::getAsyncResultMessage($task);

        return json_decode($message['body']);
    }

    /**
     * Process a message from the results queue.
     *
     * @param $task    CeleryTasks  Celery task object
     * @param $message mixed        async message object from php-celery
     */
    protected static function _processTaskMessage($task, $message)
    {
        $ref = $task->getThirdPartyTrackReferences();  // ThirdPartyTrackReferences join
        $service = CeleryServiceFactory::getService($ref->getDbService());
        $service->updateTrackReference($task, $ref->getDbId(), json_decode($message->result), $message->status);
    }

    /**
     * Check if a task message has been unreachable for more our timeout time.
     *
     * @param $task CeleryTasks the Celery task object
     *
     * @return bool true if the dispatch time is empty or it's been more than our timeout time
     *              since the message was dispatched, otherwise false
     */
    protected static function _checkMessageTimeout($task)
    {
        $utc = new DateTimeZone('UTC');
        $dispatchTime = new DateTime($task->getDbDispatchTime(), $utc);
        $now = new DateTime('now', $utc);
        $timeoutSeconds = self::$_CELERY_MESSAGE_TIMEOUT / 1000;  // Convert from milliseconds
        $timeoutInterval = new DateInterval('PT' . $timeoutSeconds . 'S');

        return empty($dispatchTime) || $dispatchTime->add($timeoutInterval) <= $now;
    }
}
