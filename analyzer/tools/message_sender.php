<?
require_once('php-amqplib/amqp.inc');

//use PhpAmqpLibConnectionAMQPConnection;
//use PhpAmqpLibMessageAMQPMessage;

define('HOST', '127.0.0.1');
define('PORT', '5672');
define('USER', 'airtime');
define('PASS', 'QEFKX5GMKT4YNMOAL9R8');
define('VHOST', '/airtime');//'/airtime');

$exchange = "airtime-uploads";
$exchangeType = "topic";
$queue = "airtime-uploads";
$routingKey = ""; //"airtime.analyzer.tasks";

if ($argc <= 1)
{
    echo("Usage: " . $argv[0] . " message\n");
    exit();
}

$message = $argv[1];

$connection = new AMQPStreamConnection(HOST, PORT, USER, PASS, VHOST);

if (!isset($connection)) {
    echo "Failed to connect to the RabbitMQ server.";
    return;
}

$channel = $connection->channel();

// declare/create the queue
$channel->queue_declare($queue, false, true, false, false);

// declare/create the exchange as a topic exchange.
$channel->exchange_declare($exchange, $exchangeType, false, true, false);

$msg = new AMQPMessage($message, array("content_type" => "text/plain"));

$channel->basic_publish($msg, $exchange, $routingKey);
print "Sent $message ($routingKey)\n";
$channel->close();
$connection->close();
