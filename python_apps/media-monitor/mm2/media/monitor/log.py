import logging
import abc
import traceback
from pure import LazyProperty

appname = 'root'

def setup_logging(log_path):
    """ Setup logging by writing log to 'log_path' """
    #logger = logging.getLogger(appname)
    logging.basicConfig(filename=log_path, level=logging.DEBUG)

def get_logger():
    """ in case we want to use the common logger from a procedural
    interface """
    return logging.getLogger()

class Loggable(object):
    """ Any class that wants to log can inherit from this class and
    automatically get a logger attribute that can be used like:
    self.logger.info(...) etc. """
    __metaclass__ = abc.ABCMeta
    @LazyProperty
    def logger(self): return get_logger()

    def unexpected_exception(self,e):
        """ Default message for 'unexpected' exceptions """
        self.fatal_exception("'Unexpected' exception has occured:", e)

    def fatal_exception(self, message, e):
        """ Prints an exception 'e' with 'message'. Also outputs the
        traceback. """
        self.logger.error( message )
        self.logger.error( str(e) )
        self.logger.error( traceback.format_exc() )

