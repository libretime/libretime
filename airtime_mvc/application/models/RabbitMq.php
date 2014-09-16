<?php
require_once 'php-amqplib/amqp.inc';

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

    public static function SendMessageToAnalyzer($tmpFilePath, $importedStorageDirectory, $originalFilename,
                                                $callbackUrl, $apiKey)
    {
        //Hack for Airtime Pro. The RabbitMQ settings for communicating with airtime_analyzer are global
        //and shared between all instances on Airtime Pro.
        $CC_CONFIG = Config::getConfig();        
        $devEnv = "production"; //Default
        if (array_key_exists("dev_env", $CC_CONFIG)) {
            $devEnv = $CC_CONFIG["dev_env"];
        }
        $config = parse_ini_file("/etc/airtime-saas/rabbitmq-analyzer-" . $devEnv . ".ini", true);
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
        $data['import_directory'] = $importedStorageDirectory;
        $data['original_filename'] = $originalFilename;
        $data['callback_url'] = $callbackUrl;
        $data['api_key'] = $apiKey;
        
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
        $config = parse_ini_file("/etc/airtime-saas/rabbitmq.ini", true);
        $conn = new AMQPConnection($config["rabbitmq"]["host"],
        $config["rabbitmq"]["port"],
        $config["rabbitmq"]["user"],
        $config["rabbitmq"]["password"],
        $config["rabbitmq"]["vhost"]);

        $exchange = $config["rabbitmq"]["queue"];
        $queue = $config["rabbitmq"]["queue"];

        $ch = $conn->channel();


        /*
            name: $queue
            passive: false
            durable: true // the queue will survive server restarts
            exclusive: false // the queue can be accessed in other channels
            auto_delete: false //the queue won't be deleted once the channel is closed.
        */
        $ch->queue_declare($queue, false, true, false, false);

        /*
            name: $exchange
            type: direct
            passive: false
            durable: true // the exchange will survive server restarts
            auto_delete: false //the exchange won't be deleted once the channel is closed.
        */

        $ch->exchange_declare($exchange, 'direct', false, true, false);
        $ch->queue_bind($queue, $exchange);

        $data = json_encode($md).PHP_EOL;
        $msg = new AMQPMessage($data, array('content_type' => 'application/json'));

        $ch->basic_publish($msg, $exchange);
        $ch->close();
        $conn->close();
    }        
}
