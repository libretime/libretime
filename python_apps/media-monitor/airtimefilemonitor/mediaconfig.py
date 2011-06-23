import sys

from configobj import ConfigObj

class AirtimeMediaConfig:

    MODE_CREATE = "create"
    MODE_MODIFY = "modify"
    MODE_MOVED = "moved"
    MODE_DELETE = "delete"

    def __init__(self):

        # loading config file
        try:
            config = ConfigObj('/etc/airtime/media-monitor.cfg')
            self.cfg = config
        except Exception, e:
            print 'Error loading config: ', e
            sys.exit()

        self.storage_directory = None


