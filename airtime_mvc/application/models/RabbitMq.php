<?php
require_once 'php-amqplib/amqp.inc';
require_once 'massivescale/celery-php/celery.php';

class Application_Model_RabbitMq
{
    public static $doPush = false;

    /**
     * @var int milliseconds (for compatibility with celery) until we consider a message to have timed out
     */
    public static $_CELERY_MESSAGE_TIMEOUT = 300000;  // 5 minutes

    /**
     * @var string exchange for celery task results
     */
    public static $_CELERY_RESULTS_EXCHANGE = 'airtime-results';

    /**
     * Sets a flag to push the schedule at the end of the request.
     */
    public static function PushSchedule()
    {
        self::$doPush = true;
    }

    private static function sendMessage($exchange, $exchangeType, $autoDeleteExchange, $data, $queue="")
    {
        $CC_CONFIG = Config::getConfig();

        $conn = new AMQPConnection($CC_CONFIG["rabbitmq"]["host"],
                                         $CC_CONFIG["rabbitmq"]["port"],
                                         $CC_CONFIG["rabbitmq"]["user"],
                                         $CC_CONFIG["rabbitmq"]["password"],
                                         $CC_CONFIG["rabbitmq"]["vhost"]);

        if (!isset($conn)) {
            throw new Exception("Cannot connect to RabbitMQ server");
        }

        $channel = $conn->channel();
        $channel->access_request($CC_CONFIG["rabbitmq"]["vhost"], false, false,
            true, true);

        //I'm pretty sure we DON'T want to autodelete ANY exchanges but I'm keeping the code
        //the way it is just so I don't accidentally break anything when I add the Analyzer code in. -- Albert, March 13, 2014
        $channel->exchange_declare($exchange, $exchangeType, false, true, $autoDeleteExchange);

        $msg = new AMQPMessage($data, array('content_type' => 'text/plain'));

        $channel->basic_publish($msg, $exchange);
        $channel->close();
        $conn->close();
    }

    /**
     * Connect to the Celery daemon via amqp
     *
     * @param $config   array  the airtime configuration array
     * @param $exchange string the amqp exchange name
     * @param $queue    string the amqp queue name
     *
     * @return Celery the Celery connection object
     *
     * @throws Exception when a connection error occurs
     */
    private static function _setupCeleryExchange($config, $exchange, $queue) {
        return new Celery($config["rabbitmq"]["host"],
                          $config["rabbitmq"]["user"],
                          $config["rabbitmq"]["password"],
                          $config["rabbitmq"]["vhost"],
                          $exchange,                        // Exchange name
                          $queue,                           // Binding/queue
                          $config["rabbitmq"]["port"],
                          false,                            // Connector
                          true,                             // Persistent messages
                          self::$_CELERY_MESSAGE_TIMEOUT,   // Result expiration
                          array());                         // SSL opts
    }

    /**
     * Send an amqp message to Celery the airtime-celery daemon to perform a task
     *
     * @param $task     string the Celery task name
     * @param $exchange string the amqp exchange name
     * @param $data     array  an associative array containing arguments for the Celery task
     *
     * @return string the task identifier for the started Celery task so we can fetch the
     *                results asynchronously later
     *
     * @throws CeleryException when no message is found
     */
    public static function sendCeleryMessage($task, $exchange, $data) {
        $config  = Config::getConfig();
        $queue = $routingKey = $exchange;
        $c = self::_setupCeleryExchange($config, $exchange, $queue);  // Use the exchange name for the queue
        $result = $c->PostTask($task, $data, true, $routingKey);      // and routing key
        return $result->getId();
    }

    /**
     * Given a task name and identifier, check the Celery results queue for any
     * corresponding messages
     *
     * @param $task string the Celery task name
     * @param $id   string the Celery task identifier
     *
     * @return object the message object
     *
     * @throws CeleryException when no message is found
     */
    public static function getAsyncResultMessage($task, $id) {
        $config  = Config::getConfig();
        $queue = self::$_CELERY_RESULTS_EXCHANGE . "." . $config["stationId"];
        $c = self::_setupCeleryExchange($config, self::$_CELERY_RESULTS_EXCHANGE, $queue);
        $message = $c->getAsyncResultMessage($task, $id);

        if ($message == FALSE) {
            throw new CeleryException("Failed to get message for task $task with ID $id");
        }
        return $message;
    }

    public static function SendMessageToPypo($event_type, $md)
    {
        $md["event_type"] = $event_type;

        $exchange = 'airtime-pypo';
        $data = json_encode($md, JSON_FORCE_OBJECT);
        self::sendMessage($exchange, 'direct', true, $data);
    }

    public static function SendMessageToMediaMonitor($event_type, $md)
    {
        $md["event_type"] = $event_type;

        $exchange = 'airtime-media-monitor';
        $data = json_encode($md);
        self::sendMessage($exchange, 'direct', true, $data);
    }

    public static function SendMessageToShowRecorder($event_type)
    {
        $exchange = 'airtime-pypo';

        $now = new DateTime("@".time()); //in UTC timezone
        $end_timestamp = new DateTime("@".(time() + 3600*2)); //in UTC timezone

        $temp = array();
        $temp['event_type'] = $event_type;
        $temp['server_timezone'] = Application_Model_Preference::GetTimezone();
        if ($event_type == "update_recorder_schedule") {
            $temp['shows'] = Application_Model_Show::getShows($now,
                $end_timestamp, $onlyRecord=true);
        }
        $data = json_encode($temp);

        self::sendMessage($exchange, 'direct', true, $data);
    }

    public static function SendMessageToAnalyzer($tmpFilePath, $importedStorageDirectory, $originalFilename,
                                                $callbackUrl, $apiKey, $storageBackend, $filePrefix)
    {
        //Hack for Airtime Pro. The RabbitMQ settings for communicating with airtime_analyzer are global
        //and shared between all instances on Airtime Pro.
        $CC_CONFIG = Config::getConfig();        
        $devEnv = "production"; //Default
        if (array_key_exists("dev_env", $CC_CONFIG)) {
            $devEnv = $CC_CONFIG["dev_env"];
        }
        $rmq_config_path = "/etc/airtime-saas/".$devEnv."/rabbitmq-analyzer.ini";
        if (!file_exists($rmq_config_path)) {
            // If the dev env specific rabbitmq-analyzer.ini doesn't exist default
            // to the production rabbitmq-analyzer.ini
            $rmq_config_path = "/etc/airtime-saas/production/rabbitmq-analyzer.ini";
        }
        $config = parse_ini_file($rmq_config_path, true);
        $conn = new AMQPConnection($config["rabbitmq"]["host"],
                $config["rabbitmq"]["port"],
                $config["rabbitmq"]["user"],
                $config["rabbitmq"]["password"],
                $config["rabbitmq"]["vhost"]);
        
        $exchange = 'airtime-uploads';
        $exchangeType = 'topic';
        $queue = 'airtime-uploads';
        $autoDeleteExchange = false;
        $data['tmp_file_path'] = $tmpFilePath;
        $data['storage_backend'] = $storageBackend;
        $data['import_directory'] = $importedStorageDirectory;
        $data['original_filename'] = $originalFilename;
        $data['callback_url'] = $callbackUrl;
        $data['api_key'] = $apiKey;

        // We add a prefix to the resource name so files are not all placed
        // under the root folder. We do this in case we need to restore a 
        // customer's file/s; File restoration is done via the S3 Browser
        // client. The client will hang if there are too many files under the
        // same folder.
        $data['file_prefix'] = $filePrefix;
        
        $jsonData = json_encode($data);
        //self::sendMessage($exchange, 'topic', false, $jsonData, 'airtime-uploads');
                
        if (!isset($conn)) {
            throw new Exception("Cannot connect to RabbitMQ server");
        }
        
        $channel = $conn->channel();
        $channel->access_request($config["rabbitmq"]["vhost"], false, false,
                true, true);
        
        //I'm pretty sure we DON'T want to autodelete ANY exchanges but I'm keeping the code
        //the way it is just so I don't accidentally break anything when I add the Analyzer code in. -- Albert, March 13, 2014
        $channel->exchange_declare($exchange, $exchangeType, false, true, $autoDeleteExchange);
        
        $msg = new AMQPMessage($jsonData, array('content_type' => 'text/plain'));
        
        $channel->basic_publish($msg, $exchange);
        $channel->close();
        $conn->close();
        
    }
    
    
    public static function SendMessageToHaproxyConfigDaemon($md){
        //XXX: This function has been deprecated and is no longer needed
    }

}
