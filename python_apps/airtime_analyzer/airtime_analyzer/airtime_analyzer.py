"""Contains the main application class for airtime_analyzer.
"""
import ConfigParser
import logging
import logging.handlers
import sys
from functools import partial
from metadata_analyzer import MetadataAnalyzer
from replaygain_analyzer import ReplayGainAnalyzer
from status_reporter import StatusReporter 
from message_listener import MessageListener


class AirtimeAnalyzerServer:
    """A server for importing uploads to Airtime as background jobs.
    """

    # Constants 
    _LOG_PATH = "/var/log/airtime/airtime_analyzer.log"
   
    # Variables
    _log_level = logging.INFO

    def __init__(self, rmq_config_path, http_retry_queue_path, debug=False):

        # Configure logging
        self.setup_logging(debug)

        # Read our config file
        rabbitmq_config = self.read_config_file(rmq_config_path)
       
        # Start up the StatusReporter process
        StatusReporter.start_child_process(http_retry_queue_path)

        # Start listening for RabbitMQ messages telling us about newly
        # uploaded files.
        self._msg_listener = MessageListener(rabbitmq_config)

        StatusReporter.stop_child_process()
    

    def setup_logging(self, debug):
        """Set up nicely formatted logging and log rotation.
        
        Keyword arguments:
        debug -- a boolean indicating whether to enable super verbose logging
                 to the screen and disk.
        """
        if debug:
            self._log_level = logging.DEBUG
        else:
            #Disable most pika/rabbitmq logging:
            pika_logger = logging.getLogger('pika')
            pika_logger.setLevel(logging.CRITICAL)
        
        # Set up logging
        logFormatter = logging.Formatter("%(asctime)s [%(module)s] [%(levelname)-5.5s]  %(message)s")
        rootLogger = logging.getLogger()
        rootLogger.setLevel(self._log_level)

        fileHandler = logging.handlers.RotatingFileHandler(filename=self._LOG_PATH, maxBytes=1024*1024*30,
                                                  backupCount=8)
        fileHandler.setFormatter(logFormatter)
        rootLogger.addHandler(fileHandler)

        consoleHandler = logging.StreamHandler()
        consoleHandler.setFormatter(logFormatter)
        rootLogger.addHandler(consoleHandler)


    def read_config_file(self, config_path):
        """Parse the application's config file located at config_path."""
        config = ConfigParser.SafeConfigParser()
        try:
            config.readfp(open(config_path))
        except IOError as e:
            print "Failed to open config file at " + config_path + ": " + e.strerror 
            exit(-1)
        except Exception:
            print e.strerror 
            exit(-1)

        return config
    
