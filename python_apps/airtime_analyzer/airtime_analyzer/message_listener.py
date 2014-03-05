import sys
import pika
import multiprocessing 
from analyzer_pipeline import AnalyzerPipeline

EXCHANGE = "airtime-uploads"
EXCHANGE_TYPE = "topic"
ROUTING_KEY = "" #"airtime.analyzer.tasks"
QUEUE = "airtime-uploads"


''' TODO: Document me
    - round robin messaging
    - acking
    - why we use the multiprocess architecture
'''
class MessageListener:

    def __init__(self, config):

        RMQ_CONFIG_SECTION = "rabbitmq"
        if not config.has_section(RMQ_CONFIG_SECTION):
            print "Error: rabbitmq section not found in config file at " + config_path
            exit(-1)

        self._host = config.get(RMQ_CONFIG_SECTION, 'host')
        self._port = config.getint(RMQ_CONFIG_SECTION, 'port')
        self._username = config.get(RMQ_CONFIG_SECTION, 'user')
        self._password = config.get(RMQ_CONFIG_SECTION, 'password')
        self._vhost = config.get(RMQ_CONFIG_SECTION, 'vhost')

        self._connection = pika.BlockingConnection(pika.ConnectionParameters(host=self._host, 
            port=self._port, virtual_host=self._vhost, 
            credentials=pika.credentials.PlainCredentials(self._username, self._password)))
        self._channel = self._connection.channel()
        self._channel.exchange_declare(exchange=EXCHANGE, type=EXCHANGE_TYPE)
        result = self._channel.queue_declare(queue=QUEUE, durable=True)

        self._channel.queue_bind(exchange=EXCHANGE, queue=QUEUE, routing_key=ROUTING_KEY)
         
        print " Listening for messages..."
        self._channel.basic_consume(MessageListener.msg_received_callback, 
                                    queue=QUEUE, no_ack=False)

        try:
            self._channel.start_consuming()
        except KeyboardInterrupt:
            self._channel.stop_consuming()

        self._connection.close()

    # consume callback function
    @staticmethod
    def msg_received_callback(channel, method_frame, header_frame, body):
        print " - Received '%s' on routing_key '%s'" % (body, method_frame.routing_key)

        # Spin up a worker process. We use the multiprocessing module and multiprocessing.Queue 
        # to pass objects between the processes so that if the analyzer process crashes, it does not
        # take down the rest of the daemon and we NACK that message so that it doesn't get 
        # propagated to other airtime_analyzer daemons (eg. running on other servers). 
        # We avoid cascading failure this way.
        try:
            MessageListener.spawn_analyzer_process(body)
        except Exception:
            #If ANY exception happens while processing a file, we're going to NACK to the 
            #messaging server and tell it to remove the message from the queue. 
            #(NACK is a negative acknowledgement. We could use ACK instead, but this might come
            # in handy in the future.)
            #Exceptions in this context are unexpected, unhandled errors. We try to recover
            #from as many errors as possble in AnalyzerPipeline, but we're safeguarding ourselves
            #here from any catastrophic or genuinely unexpected errors:
            channel.basic_nack(delivery_tag=method_frame.delivery_tag, multiple=False,
                               requeue=False) #Important that it doesn't requeue the message

            #TODO: Report this as a failed upload to the File Upload REST API.
            #
            #

        else:
            # ACK at the very end, after the message has been successfully processed.
            # If we don't ack, then RabbitMQ will redeliver a message in the future.
            channel.basic_ack(delivery_tag=method_frame.delivery_tag)

        # Anything else could happen here:
        # Send an email alert, send an xmnp message, trigger another process, etc
    
    @staticmethod
    def spawn_analyzer_process(json_msg):

        q = multiprocessing.Queue()
        p = multiprocessing.Process(target=AnalyzerPipeline.run_analysis, args=(json_msg, q))
        p.start()
        p.join()
        if p.exitcode == 0:
            results = q.get()
            print "Server received results: "
            print results
        else:
            print "Analyzer process terminated unexpectedly."
            raise AnalyzerException()


