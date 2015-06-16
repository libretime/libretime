<?php
require_once 'php-amqplib/amqp.inc';
require_once 'massivescale/celery-php/celery.php';

class Application_Model_RabbitMq
{
    public static $doPush = false;

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

    public static function getRmqConfigPath() {
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
        return $rmq_config_path;
    }

    public static function SendMessageToAnalyzer($tmpFilePath, $importedStorageDirectory, $originalFilename,
                                                $callbackUrl, $apiKey, $storageBackend, $filePrefix)
    {
        $config = parse_ini_file(self::getRmqConfigPath(), true);
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
