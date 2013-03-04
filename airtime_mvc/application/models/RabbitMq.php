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
}
