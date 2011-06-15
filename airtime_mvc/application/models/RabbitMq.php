<?php
require_once 'php-amqplib/amqp.inc';

class RabbitMq
{
    static private $doPush = FALSE;

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
    public static function PushScheduleFinal()
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

            $data = json_encode(Schedule::GetScheduledPlaylists());
            $msg = new AMQPMessage($data, array('content_type' => 'text/plain'));

            $channel->basic_publish($msg, $EXCHANGE);
            $channel->close();
            $conn->close();
        }
    }

    public static function SendFileMetaData($md)
    {
        global $CC_CONFIG;

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
}

