import logging
import abc
import traceback
from media.monitor.pure import LazyProperty

def setup_logging(log_path):
    #logger = logging.getLogger('mediamonitor2')
    logging.basicConfig(filename=log_path, level=logging.DEBUG)

appname = 'mediamonitor2'

class Loggable(object):
    __metaclass__ = abc.ABCMeta
    @LazyProperty
    def logger(self): return logging.getLogger(appname)

    def unexpected_exception(self,e):
        """
        Default message for 'unexpected' exceptions
        """
        self.fatal_exception("'Unexpected' exception has occured:", e)

    def fatal_exception(self, message, e):
        """
        Prints an exception 'e' with 'message'. Also outputs the traceback.
        """
        self.logger.error( message )
        self.logger.error( str(e) )
        self.logger.error( traceback.format_exc() )

def get_logger():
    """
    in case we want to use the common logger from a procedural interface
    """
    return logging.getLogger(appname)
