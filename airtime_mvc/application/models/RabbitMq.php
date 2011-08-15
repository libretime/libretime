<?php
require_once 'php-amqplib/amqp.inc';

class RabbitMq
{
    static public $doPush = FALSE;

    /**
     * Sets a flag to push the schedule at the end of the request.
     */
    public static function PushSchedule() {
        RabbitMq::$doPush = TRUE;
    }

    /**
     * Push the current schedule to RabbitMQ, to be picked up by Pypo.
     * Will push the schedule in the range from 24 hours ago to 24 hours
     * in the future.
     */
/*     public static function PushScheduleFinal()
    {
        global $CC_CONFIG;
        if (RabbitMq::$doPush) {
            $conn = new AMQPConnection($CC_CONFIG["rabbitmq"]["host"],
                                             $CC_CONFIG["rabbitmq"]["port"],
                                             $CC_CONFIG["rabbitmq"]["user"],
                                             $CC_CONFIG["rabbitmq"]["password"]);
            $channel = $conn->channel();
            $channel->access_request($CC_CONFIG["rabbitmq"]["vhost"], false, false, true, true);

            $EXCHANGE = 'airtime-schedule';
            $channel->exchange_declare($EXCHANGE, 'direct', false, true);

            $temp['event_type'] = "update_schedule";
            $temp['schedule'] = Schedule::GetScheduledPlaylists();
            $data = json_encode($temp);
            $msg = new AMQPMessage($data, array('content_type' => 'text/plain'));

            $channel->basic_publish($msg, $EXCHANGE);
            $channel->close();
            $conn->close();
            
            self::SendMessageToShowRecorder("update_schedule");
        }
    } */
    
    public static function SendMessageToPypo($event_type, $md)
    {
        global $CC_CONFIG;

        $md["event_type"] = $event_type;

        $conn = new AMQPConnection($CC_CONFIG["rabbitmq"]["host"],
                                         $CC_CONFIG["rabbitmq"]["port"],
                                         $CC_CONFIG["rabbitmq"]["user"],
                                         $CC_CONFIG["rabbitmq"]["password"]);
        $channel = $conn->channel();
        $channel->access_request($CC_CONFIG["rabbitmq"]["vhost"], false, false, true, true);

        $EXCHANGE = 'airtime-pypo';
        $channel->exchange_declare($EXCHANGE, 'direct', false, true);

        $data = json_encode($md);
        $msg = new AMQPMessage($data, array('content_type' => 'text/plain'));

        $channel->basic_publish($msg, $EXCHANGE);
        $channel->close();
        $conn->close();
        
        self::SendMessageToShowRecorder("update_schedule");
    }

    public static function SendMessageToMediaMonitor($event_type, $md)
    {
        global $CC_CONFIG;

        $md["event_type"] = $event_type;

        $conn = new AMQPConnection($CC_CONFIG["rabbitmq"]["host"],
                                         $CC_CONFIG["rabbitmq"]["port"],
                                         $CC_CONFIG["rabbitmq"]["user"],
                                         $CC_CONFIG["rabbitmq"]["password"]);
        $channel = $conn->channel();
        $channel->access_request($CC_CONFIG["rabbitmq"]["vhost"], false, false, true, true);

        $EXCHANGE = 'airtime-media-monitor';
        $channel->exchange_declare($EXCHANGE, 'direct', false, true);

        $data = json_encode($md);
        $msg = new AMQPMessage($data, array('content_type' => 'text/plain'));

        $channel->basic_publish($msg, $EXCHANGE);
        $channel->close();
        $conn->close();
    }
    
    public static function SendMessageToShowRecorder($event_type)
    {
        global $CC_CONFIG;

        $conn = new AMQPConnection($CC_CONFIG["rabbitmq"]["host"],
                                        $CC_CONFIG["rabbitmq"]["port"],
                                        $CC_CONFIG["rabbitmq"]["user"],
                                        $CC_CONFIG["rabbitmq"]["password"]);
        $channel = $conn->channel();
        $channel->access_request($CC_CONFIG["rabbitmq"]["vhost"], false, false, true, true);
    
        $EXCHANGE = 'airtime-show-recorder';
        $channel->exchange_declare($EXCHANGE, 'direct', false, true);
    
        $today_timestamp = date("Y-m-d H:i:s");
        $now = new DateTime($today_timestamp);
        $end_timestamp = $now->add(new DateInterval("PT2H"));
        $end_timestamp = $end_timestamp->format("Y-m-d H:i:s");
        $temp['event_type'] = $event_type;
        if($event_type = "update_schedule"){
            $temp['shows'] = Show::getShows($today_timestamp, $end_timestamp, $excludeInstance=NULL, $onlyRecord=TRUE);
        }
        $data = json_encode($temp);
        $msg = new AMQPMessage($data, array('content_type' => 'text/plain'));
    
        $channel->basic_publish($msg, $EXCHANGE);
        $channel->close();
        $conn->close();
    }
}

