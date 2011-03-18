<?php

  //AMQP PHP library test

require_once('amqp.inc');

$EXCHANGE = 'test';
$BROKER_HOST   = 'localhost';
$BROKER_PORT   = 5672;
$QUEUE    = 'myqueue';
$USER     ='guest';
$PASSWORD ='guest';

$msg_body = NULL;

$myCallback = function($msg) {
var_dump($msg);
};

try
{
    echo "Creating connection\n";
    $conn = new AMQPConnection($BROKER_HOST, $BROKER_PORT,
                               $USER,
                               $PASSWORD);
    
    echo "Getting channel\n";
    $ch = $conn->channel();
    echo "Requesting access\n";
    $ch->access_request('/data', false, false, true, true);
    
    echo "Declaring exchange\n";
    $ch->exchange_declare($EXCHANGE, 'direct', false, false, false);

    echo "Declaring queue\n";
    $ch->queue_declare($QUEUE, false, true, false, false);

    echo "Binding queue to exchange\n";
    $ch->queue_bind($QUEUE, $EXCHANGE);

    echo "Receiving message\n";
    $ch->basic_consume($QUEUE, $consumer_tag, false, false, false, false, $myCallback);
    //$ch->basic_consume($EXCHANGE, "tag", false, false, false, false, 'myCallback');

    echo "Waiting\n";

    while (count($ch->callbacks)) {
      $ch->wait();
    }

    echo "Closing channel\n";
    $ch->close();
    echo "Closing connection\n";
    $conn->close();
    echo "Done.\n";
} catch (Exception $e) {
    echo 'Caught exception: ',  $e->getMessage();
    echo "\nTrace:\n" . $e->getTraceAsString();
}
?>
