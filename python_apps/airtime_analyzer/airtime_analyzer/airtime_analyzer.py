import ConfigParser
from metadata_analyzer import MetadataAnalyzer
from replaygain_analyzer import ReplayGainAnalyzer
from message_listener import MessageListener


class AirtimeAnalyzerServer:

    _CONFIG_PATH = '/etc/airtime/airtime.conf'

    def __init__(self):
        
        # Read our config file
        rabbitmq_config = self.read_config_file()

        # Start listening for RabbitMQ messages telling us about newly
        # uploaded files.
        self._msg_listener = MessageListener(rabbitmq_config)

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


