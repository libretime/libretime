import sys
import pika
import json
import time
import select
import signal
import logging 
import multiprocessing 
import queue
from .analyzer_pipeline import AnalyzerPipeline
from .status_reporter import StatusReporter

EXCHANGE = "airtime-uploads"
EXCHANGE_TYPE = "topic"
ROUTING_KEY = ""
QUEUE = "airtime-uploads"


""" A message listener class that waits for messages from Airtime through RabbitMQ
    notifying us about new uploads.
    
    This is probably the most important class in this application. It connects
    to RabbitMQ (or an AMQP server) and listens for messages that notify us
    when a user uploads a new file to Airtime, either through the web interface
    or via FTP (on Airtime Pro). When we get a notification, we spawn a child
    process that extracts the uploaded audio file's metadata and moves it into
    Airtime's music library directory. Lastly, the extracted metadata is 
    reported back to the Airtime web application.
    
    There's a couple of Very Important technical details and constraints that you
    need to know if you're going to work on this code:
    
    1) airtime_analyzer is designed so it doesn't have to run on the same 
       computer as the web server. It just needs access to your Airtime library
       folder (stor). 
    2) airtime_analyzer is multi-tenant - One process can be used for many
       Airtime instances. It's designed NOT to know about whether it's running
       in a single tenant or multi-tenant environment. All the information it 
       needs to import a file into an Airtime instance is passed in via those
       RabbitMQ messages.
    3) We're using a "topic exchange" for the new upload notification RabbitMQ
       messages. This means if we run several airtime_analyzer processes on 
       different computers, RabbitMQ will do round-robin dispatching of the
       file notification. This is cheap, easy load balancing and
       redundancy for us. You can even run multiple airtime_analyzer processes
       on one machine if you want.
    4) We run the actual work (metadata analysis and file moving) in a separate
       child process so that if it crashes, we can stop RabbitMQ from resending
       the file notification message to another airtime_analyzer process (NACK), 
       which would otherwise cause cascading failure. We also do this so that we 
       can report the problem file to the Airtime web interface ("import failed").

    So that is a quick overview of the design constraints for this application, and
    why airtime_analyzer is written this way.
"""
class MessageListener:

    def __init__(self, rmq_config):
        ''' Start listening for file upload notification messages
            from RabbitMQ
            
            Keyword arguments:
                rmq_config: A ConfigParser object containing the [rabbitmq] configuration.
        '''
    
        self._shutdown = False

        # Read the RabbitMQ connection settings from the rmq_config file
        # The exceptions throw here by default give good error messages. 
        RMQ_CONFIG_SECTION = "rabbitmq"
        self._host = rmq_config.get(RMQ_CONFIG_SECTION, 'host')
        self._port = rmq_config.getint(RMQ_CONFIG_SECTION, 'port')
        self._username = rmq_config.get(RMQ_CONFIG_SECTION, 'user')
        self._password = rmq_config.get(RMQ_CONFIG_SECTION, 'password')
        self._vhost = rmq_config.get(RMQ_CONFIG_SECTION, 'vhost')

        # Set up a signal handler so we can shutdown gracefully
        # For some reason, this signal handler must be set up here. I'd rather 
        # put it in AirtimeAnalyzerServer, but it doesn't work there (something to do
        # with pika's SIGTERM handler interfering with it, I think...)
        signal.signal(signal.SIGTERM, self.graceful_shutdown)

        while not self._shutdown:
            try:
                self.connect_to_messaging_server()
                self.wait_for_messages()
            except (KeyboardInterrupt, SystemExit):
                break # Break out of the while loop and exit the application
            except select.error:
                pass 
            except pika.exceptions.AMQPError as e:
                if self._shutdown:
                    break
                logging.error("Connection to message queue failed. ")
                logging.error(e)
                logging.info("Retrying in 5 seconds...")
                time.sleep(5)

        self.disconnect_from_messaging_server()
        logging.info("Exiting cleanly.")


    def connect_to_messaging_server(self):
        '''Connect to the RabbitMQ server and start listening for messages.'''
        self._connection = pika.BlockingConnection(pika.ConnectionParameters(host=self._host, 
            port=self._port, virtual_host=self._vhost, 
            credentials=pika.credentials.PlainCredentials(self._username, self._password)))
        self._channel = self._connection.channel()
        self._channel.exchange_declare(exchange=EXCHANGE, exchange_type=EXCHANGE_TYPE, durable=True)
        result = self._channel.queue_declare(queue=QUEUE, durable=True)

        self._channel.queue_bind(exchange=EXCHANGE, queue=QUEUE, routing_key=ROUTING_KEY)
         
        logging.info(" Listening for messages...")
        self._channel.basic_consume(self.msg_received_callback,
                                    queue=QUEUE, no_ack=False)

    def wait_for_messages(self):
        '''Wait until we've received a RabbitMQ message.'''
        self._channel.start_consuming()

    def disconnect_from_messaging_server(self):
        '''Stop consuming RabbitMQ messages and disconnect'''
        # If you try to close a connection that's already closed, you're going to have a bad time.
        # We're breaking EAFP because this can be called multiple times depending on exception
        # handling flow here.
        if not self._channel.is_closed and not self._channel.is_closing:
            self._channel.stop_consuming()
        if not self._connection.is_closed and not self._connection.is_closing:
            self._connection.close()
   
    def graceful_shutdown(self, signum, frame):
        '''Disconnect and break out of the message listening loop'''
        self._shutdown = True
        self.disconnect_from_messaging_server()

    def msg_received_callback(self, channel, method_frame, header_frame, body):
        ''' A callback method that runs when a RabbitMQ message is received. 
        
            Here we parse the message, spin up an analyzer process, and report the 
            metadata back to the Airtime web application (or report an error).
        ''' 
        logging.info(" - Received '%s' on routing_key '%s'" % (body, method_frame.routing_key))
        
        #Declare all variables here so they exist in the exception handlers below, no matter what.
        audio_file_path = ""
        #final_file_path = ""
        import_directory = ""
        original_filename = ""
        callback_url    = ""
        api_key         = ""
        file_prefix = ""

        ''' Spin up a worker process. We use the multiprocessing module and multiprocessing.Queue 
            to pass objects between the processes so that if the analyzer process crashes, it does not
            take down the rest of the daemon and we NACK that message so that it doesn't get 
            propagated to other airtime_analyzer daemons (eg. running on other servers). 
            We avoid cascading failure this way.
        '''
        try:
            msg_dict = json.loads(body)
            api_key         = msg_dict["api_key"]
            callback_url    = msg_dict["callback_url"]

            audio_file_path = msg_dict["tmp_file_path"]
            import_directory = msg_dict["import_directory"]
            original_filename = msg_dict["original_filename"]
            file_prefix = msg_dict["file_prefix"]
            storage_backend = msg_dict["storage_backend"]

            audio_metadata = MessageListener.spawn_analyzer_process(audio_file_path, import_directory, original_filename, storage_backend, file_prefix)
            StatusReporter.report_success_to_callback_url(callback_url, api_key, audio_metadata)

        except KeyError as e:
            # A field in msg_dict that we needed was missing (eg. audio_file_path)
            logging.exception("A mandatory airtime_analyzer message field was missing from the message.")
            # See the huge comment about NACK below.
            channel.basic_nack(delivery_tag=method_frame.delivery_tag, multiple=False,
                               requeue=False) #Important that it doesn't requeue the message
        
        except Exception as e:
            logging.exception(e)
            ''' If ANY exception happens while processing a file, we're going to NACK to the 
                messaging server and tell it to remove the message from the queue. 
                (NACK is a negative acknowledgement. We could use ACK instead, but this might come
                 in handy in the future.)
                Exceptions in this context are unexpected, unhandled errors. We try to recover
                from as many errors as possble in AnalyzerPipeline, but we're safeguarding ourselves
                here from any catastrophic or genuinely unexpected errors:
            '''
            channel.basic_nack(delivery_tag=method_frame.delivery_tag, multiple=False,
                               requeue=False) #Important that it doesn't requeue the message

            #
            # TODO: If the JSON was invalid or the web server is down, 
            #       then don't report that failure to the REST API
            #TODO: Catch exceptions from this HTTP request too:
            if callback_url: # If we got an invalid message, there might be no callback_url in the JSON
                # Report this as a failed upload to the File Upload REST API.
                StatusReporter.report_failure_to_callback_url(callback_url, api_key, import_status=2,
                                                              reason='An error occurred while importing this file')
            

        else:
            # ACK at the very end, after the message has been successfully processed.
            # If we don't ack, then RabbitMQ will redeliver the message in the future.
            channel.basic_ack(delivery_tag=method_frame.delivery_tag)
    
    @staticmethod
    def spawn_analyzer_process(audio_file_path, import_directory, original_filename, storage_backend, file_prefix):
        ''' Spawn a child process to analyze and import a new audio file. '''
        '''
        q = multiprocessing.Queue()
        p = multiprocessing.Process(target=AnalyzerPipeline.run_analysis,
                        args=(q, audio_file_path, import_directory, original_filename, storage_backend, file_prefix))
        p.start()
        p.join()
        if p.exitcode == 0:
            results = q.get()
            logging.info("Main process received results from child: ")
            logging.info(results)
        else:
            raise Exception("Analyzer process terminated unexpectedly.")
        '''
        metadata = {}

        q = queue.Queue()
        try:
            AnalyzerPipeline.run_analysis(q, audio_file_path, import_directory, original_filename, storage_backend, file_prefix)
            metadata = q.get()
        except Exception as e:
            logging.error("Analyzer pipeline exception: %s" % str(e))
            metadata["import_status"] = AnalyzerPipeline.IMPORT_STATUS_FAILED

        # Ensure our queue doesn't fill up and block due to unexpected behaviour. Defensive code.
        while not q.empty():
            q.get()

        return metadata

