<?php

/**
 * Publish every message the legacy service sends to playout and to the
 * analyzer, so that capture.py can record the reference wire format.
 *
 * The payloads and the argument types are the ones used by the real call
 * sites (PreferenceController, DashboardController, ApiController, ...).
 *
 * Usage: php publish-legacy.php [playout|recorder|analyzer]
 */
$legacyRoot = getenv('LEGACY_ROOT') ?: '/var/www/html';

require_once $legacyRoot . '/application/preload.php';
require_once $legacyRoot . '/application/check.php';

configureDatabase();

/*
 * Application_Model_RabbitMq::SendMessageToPypo() adds event_type to the
 * payload before encoding it, and encodes with JSON_FORCE_OBJECT.
 */
$pypoMessages = [
    // RabbitMqPlugin, PreferenceController, ScheduleController
    'update_schedule' => ['event' => 'update_schedule', 'payload' => []],
    // ApiController::rabbitmqDoPushAction
    'reset_liquidsoap_bootstrap' => [
        'event' => 'reset_liquidsoap_bootstrap',
        'payload' => [],
    ],
    // ApiController::setSourceStatusAction, DashboardController::sourcesAction
    'switch_source_on' => [
        'event' => 'switch_source',
        'payload' => ['sourcename' => 'live_dj', 'status' => 'on'],
    ],
    'switch_source_off' => [
        'event' => 'switch_source',
        'payload' => ['sourcename' => 'master_dj', 'status' => 'off'],
    ],
    // ScheduleController::cancelShowAction, DashboardController::sourcesAction
    'disconnect_source' => ['event' => 'disconnect_source', 'payload' => ['sourcename' => 'live_dj']],
    // Application_Model_Preference::SetHeadTitle, values posted by the forms
    'update_station_name' => [
        'event' => 'update_station_name',
        'payload' => ['station_name' => 'Radio Zürich — "quote" \\ <>& / 😀'],
    ],
    // Application_Model_Preference::SetDefaultTransitionFade
    'update_transition_fade' => ['event' => 'update_transition_fade', 'payload' => ['transition_fade' => '1.500']],
    // Application_Model_Preference::SetStreamLabelFormat
    'update_stream_format' => ['event' => 'update_stream_format', 'payload' => ['stream_format' => '1']],
    // Application_Model_Preference::setOffAirMeta
    'update_message_offline' => [
        'event' => 'update_message_offline',
        'payload' => ['message_offline' => 'LibreTime - offline'],
    ],
];

$only = $argv[1] ?? '';

function publish(string $name, callable $callback): void
{
    try {
        $callback();
        echo "OK   {$name}\n";
    } catch (Throwable $e) {
        echo 'FAIL ' . $name . ' ' . get_class($e) . ': ' . $e->getMessage() . "\n";
    }
}

if ($only === '' || $only === 'playout') {
    foreach ($pypoMessages as $name => $message) {
        publish($name, fn () => Application_Model_RabbitMq::SendMessageToPypo($message['event'], $message['payload']));
    }
}

if ($only === '' || $only === 'recorder') {
    // Application_Model_Scheduler::startNextShow, Application_Model_Show
    foreach (['cancel_recording', 'update_recorder_schedule'] as $eventType) {
        publish($eventType, fn () => Application_Model_RabbitMq::SendMessageToShowRecorder($eventType));
    }
}

if ($only === '' || $only === 'analyzer') {
    /*
     * Application_Service_MediaService::uploadFileToLibrary passes the track
     * type of the file, whose cue point option ends up in the message. The
     * track types are temporary so that the capture runs anywhere.
     */
    foreach ([true, false] as $analyzeCuePoints) {
        $trackType = new CcTracktypes();
        $trackType->setDbCode('rabbitmq-eq');
        $trackType->setDbTypeName('rabbitmq eq');
        $trackType->setDbVisibility(0);
        $trackType->setDbAnalyzeCuePoints($analyzeCuePoints);
        $trackType->save();

        publish('analyze_file_cue_points_' . var_export($analyzeCuePoints, true), function () use ($trackType) {
            Application_Model_RabbitMq::SendMessageToAnalyzer(
                '/tmp/libretime-equivalence.mp3',
                '/path/to/storage/imported/',
                'Some Song.mp3',
                42,
                $trackType->getDbId(),
            );
        });

        $trackType->delete();
    }

    publish('analyze_file_without_track_type', fn () => Application_Model_RabbitMq::SendMessageToAnalyzer(
        '/tmp/libretime-equivalence.mp3',
        '/path/to/storage/imported/',
        'Some Song.mp3',
        42,
        null,
    ));
}
