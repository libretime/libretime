<?php

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

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

    private static function sendMessage($exchange, $exchangeType, $autoDeleteExchange, $data, $queue = '')
    {
        $CC_CONFIG = Config::getConfig();

        $conn = new AMQPStreamConnection(
            $CC_CONFIG['rabbitmq']['host'],
            $CC_CONFIG['rabbitmq']['port'],
            $CC_CONFIG['rabbitmq']['user'],
            $CC_CONFIG['rabbitmq']['password'],
            $CC_CONFIG['rabbitmq']['vhost']
        );

        if (!isset($conn)) {
            throw new Exception('Cannot connect to RabbitMQ server');
        }

        $channel = $conn->channel();
        $channel->access_request(
            $CC_CONFIG['rabbitmq']['vhost'],
            false,
            false,
            true,
            true
        );

        // I'm pretty sure we DON'T want to autodelete ANY exchanges but I'm keeping the code
        // the way it is just so I don't accidentally break anything when I add the Analyzer code in. -- Albert, March 13, 2014
        $channel->exchange_declare($exchange, $exchangeType, false, true, $autoDeleteExchange);

        $msg = new AMQPMessage($data, ['content_type' => 'text/plain']);

        $channel->basic_publish($msg, $exchange);
        $channel->close();
        $conn->close();
    }

    public static function SendMessageToPypo($event_type, $md)
    {
        $md['event_type'] = $event_type;

        $exchange = 'airtime-pypo';
        $data = json_encode($md, JSON_FORCE_OBJECT);
        self::sendMessage($exchange, 'direct', true, $data);
    }

    public static function SendMessageToMediaMonitor($event_type, $md)
    {
        $md['event_type'] = $event_type;

        $exchange = 'airtime-analyzer';
        $data = json_encode($md);
        self::sendMessage($exchange, 'direct', true, $data);
    }

    public static function SendMessageToShowRecorder($event_type)
    {
        $exchange = 'airtime-pypo';

        $now = new DateTime('@' . time()); // in UTC timezone
        $end_timestamp = new DateTime('@' . (time() + 3600 * 2)); // in UTC timezone

        $temp = [];
        $temp['event_type'] = $event_type;
        $temp['server_timezone'] = Application_Model_Preference::GetTimezone();
        if ($event_type == 'update_recorder_schedule') {
            $temp['shows'] = Application_Model_Show::getShows(
                $now,
                $end_timestamp,
                $onlyRecord = true
            );
        }
        $data = json_encode($temp);

        self::sendMessage($exchange, 'direct', true, $data);
    }

    public static function SendMessageToAnalyzer(
        $tmpFilePath,
        $importedStorageDirectory,
        $originalFilename,
        $fileId,
        $fileTrackTypeId
    ) {
        $config = Config::getConfig();

        $conn = new AMQPStreamConnection(
            $config['rabbitmq']['host'],
            $config['rabbitmq']['port'],
            $config['rabbitmq']['user'],
            $config['rabbitmq']['password'],
            $config['rabbitmq']['vhost']
        );

        $exchange = 'airtime-uploads';
        $exchangeType = 'topic';
        $queue = 'airtime-uploads';
        $autoDeleteExchange = false;

        $data['file_id'] = $fileId;
        $data['tmp_file_path'] = $tmpFilePath;
        $data['import_directory'] = $importedStorageDirectory;
        $data['original_filename'] = $originalFilename;

        $options = new stdClass();

        if ($fileTrackTypeId) {
            $fileTrackType = new Application_Model_Tracktype($fileTrackTypeId);
            $options->analyze_cue_points = $fileTrackType->getAnalyzeCuePoints();
        }

        $data['options'] = $options;

        $jsonData = json_encode($data);
        // self::sendMessage($exchange, 'topic', false, $jsonData, 'airtime-uploads');

        if (!isset($conn)) {
            throw new Exception('Cannot connect to RabbitMQ server');
        }

        $channel = $conn->channel();
        $channel->access_request(
            $config['rabbitmq']['vhost'],
            false,
            false,
            true,
            true
        );

        // I'm pretty sure we DON'T want to autodelete ANY exchanges but I'm keeping the code
        // the way it is just so I don't accidentally break anything when I add the Analyzer code in. -- Albert, March 13, 2014
        $channel->exchange_declare($exchange, $exchangeType, false, true, $autoDeleteExchange);

        $msg = new AMQPMessage($jsonData, ['content_type' => 'text/plain']);

        $channel->basic_publish($msg, $exchange);
        $channel->close();
        $conn->close();
    }
}
