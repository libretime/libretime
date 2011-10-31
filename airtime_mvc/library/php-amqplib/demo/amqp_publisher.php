#!/usr/bin/php
<?php
/**
 * Sends a message to a queue
 *
 * @author Sean Murphy<sean@iamseanmurphy.com>
 */
 
require_once('../amqp.inc');

$HOST = 'localhost';
$PORT = 5672;
$USER = 'guest';
$PASS = 'guest';
$VHOST = '/';
$EXCHANGE = 'airtime-schedule';

$QUEUE = 'msgs';

$conn = new AMQPConnection($HOST, $PORT, $USER, $PASS);
$ch = $conn->channel();
$ch->access_request($VHOST, false, false, true, true);
$ch->exchange_declare($EXCHANGE, 'direct', false, true);

$msg_body = json_encode(array("event_type"=>"get_status", "id"=>time()));
//$msg_body = '{"schedule":{"status":{"range":{"start":"2011-09-12 20:45:22","end":"2011-09-13 20:45:22"},"version":"1.1"},"playlists":[],"check":1,"stream_metadata":{"format":"","station_name":""}},"event_type":"update_schedule"}';
$msg = new AMQPMessage($msg_body, array('content_type' => 'text/plain'));

$ch->basic_publish($msg, $EXCHANGE);

echo "Sent message '".$msg_body."'\n";
$ch->close();
$conn->close();

?>
