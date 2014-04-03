import sys
import pika
import json
import time
import logging 
import multiprocessing 
from analyzer_pipeline import AnalyzerPipeline
from status_reporter import StatusReporter

EXCHANGE = "airtime-uploads"
EXCHANGE_TYPE = "topic"
ROUTING_KEY = "" #"airtime.analyzer.tasks"
QUEUE = "airtime-uploads"


''' TODO: Document me
    - round robin messaging
    - acking
    - why we use the multiprocess architecture
    - in general, how it works and why it works this way
'''
class MessageListener:

    def __init__(self, config):

        # Read the RabbitMQ connection settings from the config file
        # The exceptions throw here by default give good error messages. 
        RMQ_CONFIG_SECTION = "rabbitmq"
        self._host = config.get(RMQ_CONFIG_SECTION, 'host')
        self._port = config.getint(RMQ_CONFIG_SECTION, 'port')
        self._username = config.get(RMQ_CONFIG_SECTION, 'user')
        self._password = config.get(RMQ_CONFIG_SECTION, 'password')
        self._vhost = config.get(RMQ_CONFIG_SECTION, 'vhost')

        while True:
            try:
                self.connect_to_messaging_server()
                self.wait_for_messages()
            except KeyboardInterrupt:
                self.disconnect_from_messaging_server()
                break
            except pika.exceptions.AMQPError as e:
                logging.error("Connection to message queue failed. ")
                logging.error(e)
                logging.info("Retrying in 5 seconds...")
                time.sleep(5)

        self._connection.close()


    def connect_to_messaging_server(self):

        self._connection = pika.BlockingConnection(pika.ConnectionParameters(host=self._host, 
            port=self._port, virtual_host=self._vhost, 
            credentials=pika.credentials.PlainCredentials(self._username, self._password)))
        self._channel = self._connection.channel()
        self._channel.exchange_declare(exchange=EXCHANGE, type=EXCHANGE_TYPE, durable=True)
        result = self._channel.queue_declare(queue=QUEUE, durable=True)

        self._channel.queue_bind(exchange=EXCHANGE, queue=QUEUE, routing_key=ROUTING_KEY)
         
        logging.info(" Listening for messages...")
        self._channel.basic_consume(MessageListener.msg_received_callback, 
                                    queue=QUEUE, no_ack=False)

    def wait_for_messages(self):
        self._channel.start_consuming()

    def disconnect_from_messaging_server(self):
        self._channel.stop_consuming()


    # consume callback function
    @staticmethod
    def msg_received_callback(channel, method_frame, header_frame, body):
        logging.info(" - Received '%s' on routing_key '%s'" % (body, method_frame.routing_key))
        
        #Declare all variables here so they exist in the exception handlers below, no matter what.
        audio_file_path = ""
        #final_file_path = ""
        import_directory = ""
        original_filename = ""
        callback_url    = ""
        api_key         = ""

        # Spin up a worker process. We use the multiprocessing module and multiprocessing.Queue 
        # to pass objects between the processes so that if the analyzer process crashes, it does not
        # take down the rest of the daemon and we NACK that message so that it doesn't get 
        # propagated to other airtime_analyzer daemons (eg. running on other servers). 
        # We avoid cascading failure this way.
        try:
            msg_dict = json.loads(body)
            audio_file_path = msg_dict["tmp_file_path"]
            #final_file_path = msg_dict["final_file_path"]
            import_directory = msg_dict["import_directory"]
            original_filename = msg_dict["original_filename"]
            callback_url    = msg_dict["callback_url"]
            api_key         = msg_dict["api_key"]
            
            audio_metadata = MessageListener.spawn_analyzer_process(audio_file_path, import_directory, original_filename)
            StatusReporter.report_success_to_callback_url(callback_url, api_key, audio_metadata)

        except KeyError as e:
            # A field in msg_dict that we needed was missing (eg. audio_file_path)
            logging.exception("A mandatory airtime_analyzer message field was missing from the message.")
            # See the huge comment about NACK below.
            channel.basic_nack(delivery_tag=method_frame.delivery_tag, multiple=False,
                               requeue=False) #Important that it doesn't requeue the message
        
        except Exception as e:
            logging.exception(e)
            #If ANY exception happens while processing a file, we're going to NACK to the 
            #messaging server and tell it to remove the message from the queue. 
            #(NACK is a negative acknowledgement. We could use ACK instead, but this might come
            # in handy in the future.)
            #Exceptions in this context are unexpected, unhandled errors. We try to recover
            #from as many errors as possble in AnalyzerPipeline, but we're safeguarding ourselves
            #here from any catastrophic or genuinely unexpected errors:
            channel.basic_nack(delivery_tag=method_frame.delivery_tag, multiple=False,
                               requeue=False) #Important that it doesn't requeue the message

            # TODO: Report this as a failed upload to the File Upload REST API.
            #
            # TODO: If the JSON was invalid or the web server is down, 
            #       then don't report that failure to the REST API
            #TODO: Catch exceptions from this HTTP request too:
            if callback_url: # If we got an invalid message, there might be no callback_url in the JSON
                StatusReporter.report_failure_to_callback_url(callback_url, api_key, import_status=2,
                                                              reason=u'An error occurred while importing this file')
            

        else:
            # ACK at the very end, after the message has been successfully processed.
            # If we don't ack, then RabbitMQ will redeliver the message in the future.
            channel.basic_ack(delivery_tag=method_frame.delivery_tag)
    
    @staticmethod
    def spawn_analyzer_process(audio_file_path, import_directory, original_filename):

        q = multiprocessing.Queue()
        p = multiprocessing.Process(target=AnalyzerPipeline.run_analysis, 
                        args=(q, audio_file_path, import_directory, original_filename))
        p.start()
        p.join()
        if p.exitcode == 0:
            results = q.get()
            logging.info("Main process received results from child: ")
            logging.info(results)
        else:
            raise Exception("Analyzer process terminated unexpectedly.")

        return results

