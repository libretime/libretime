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

    private static function sendMessage($exchange, $data)
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

        $channel->exchange_declare($exchange, 'direct', false, true);

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
        self::sendMessage($exchange, $data);
    }

    public static function SendMessageToMediaMonitor($event_type, $md)
    {
        $md["event_type"] = $event_type;

        $exchange = 'airtime-media-monitor';
        $data = json_encode($md);
        self::sendMessage($exchange, $data);
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

        self::sendMessage($exchange, $data);
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
