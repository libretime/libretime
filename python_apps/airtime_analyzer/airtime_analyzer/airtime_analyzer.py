import ConfigParser
import logging
import logging.handlers
import sys
from metadata_analyzer import MetadataAnalyzer
from replaygain_analyzer import ReplayGainAnalyzer
from message_listener import MessageListener


class AirtimeAnalyzerServer:

    # Constants 
    _CONFIG_PATH = '/etc/airtime/airtime.conf'
    _LOG_PATH = "/var/log/airtime/airtime_analyzer.log"
   
    # Variables
    _log_level = logging.INFO

    def __init__(self, debug=False):

        # Configure logging
        self.setup_logging(debug)

        # Read our config file
        rabbitmq_config = self.read_config_file()

        # Start listening for RabbitMQ messages telling us about newly
        # uploaded files.
        self._msg_listener = MessageListener(rabbitmq_config)
    

    def setup_logging(self, debug):
   
        if debug:
            self._log_level = logging.DEBUG
        else:
            #Disable most pika/rabbitmq logging:
            pika_logger = logging.getLogger('pika')
            pika_logger.setLevel(logging.CRITICAL)
        
        #self.log = logging.getLogger(__name__)

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


    def read_config_file(self):
        config = ConfigParser.SafeConfigParser()
        config_path = AirtimeAnalyzerServer._CONFIG_PATH 
        try:
            config.readfp(open(config_path))
        except IOError as e:
            print "Failed to open config file at " + config_path + ": " + e.strerror 
            exit(-1)
        except Exception:
            print e.strerror 
            exit(-1)

        return config
        

''' When being run from the command line, analyze a file passed
    as an argument. '''
if __name__ == "__main__":
    import sys
    analyzers = AnalyzerPipeline()


