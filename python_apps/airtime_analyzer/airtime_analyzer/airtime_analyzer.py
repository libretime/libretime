"""Contains the main application class for airtime_analyzer.
"""
import logging
import logging.handlers
import sys
import signal
import traceback
from . import config_file
from functools import partial
from .metadata_analyzer import MetadataAnalyzer
from .replaygain_analyzer import ReplayGainAnalyzer
from .status_reporter import StatusReporter 
from .message_listener import MessageListener


class AirtimeAnalyzerServer:
    """A server for importing uploads to Airtime as background jobs.
    """

    # Constants 
    _LOG_PATH = "/var/log/airtime/airtime_analyzer.log"
   
    # Variables
    _log_level = logging.INFO

    def __init__(self, rmq_config_path, http_retry_queue_path, debug=False):

        # Dump a stacktrace with 'kill -SIGUSR2 <PID>'
        signal.signal(signal.SIGUSR2, lambda sig, frame: AirtimeAnalyzerServer.dump_stacktrace())

        # Configure logging
        self.setup_logging(debug)

        # Read our rmq config file
        rmq_config = config_file.read_config_file(rmq_config_path)

        # Start up the StatusReporter process
        StatusReporter.start_thread(http_retry_queue_path)

        # Start listening for RabbitMQ messages telling us about newly
        # uploaded files. This blocks until we recieve a shutdown signal.
        self._msg_listener = MessageListener(rmq_config)

        StatusReporter.stop_thread()
    

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
   
    @classmethod
    def dump_stacktrace(stack):
        ''' Dump a stacktrace for all threads '''
        code = []
        for threadId, stack in list(sys._current_frames().items()):
            code.append("\n# ThreadID: %s" % threadId)
            for filename, lineno, name, line in traceback.extract_stack(stack):
                code.append('File: "%s", line %d, in %s' % (filename, lineno, name))
                if line:
                    code.append("  %s" % (line.strip()))
        logging.info('\n'.join(code))
