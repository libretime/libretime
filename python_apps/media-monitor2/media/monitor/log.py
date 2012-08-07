import logging
import abc
import traceback
from media.monitor.pure import LazyProperty

logfile = '/home/rudi/throwaway/mm2.log'
#logger = None

def setup_logging(log_path):
    #logger = logging.getLogger('mediamonitor2')
    logging.basicConfig(filename=log_path, level=logging.DEBUG)

appname = 'mediamonitor2'

class Loggable(object):
    __metaclass__ = abc.ABCMeta
    @LazyProperty
    def logger(self):
        # TODO : Clean this up
        if not hasattr(self,"_logger"): self._logger = logging.getLogger(appname)
        return self._logger

    def unexpected_exception(self,e):
        self.fatal_exception("'Unexpected' exception has occured:", e)

    def fatal_exception(self, message, e):
        self.logger.error(message)
        self.logger.error( str(e) )
        self.logger.error( traceback.format_exc() )

def get_logger():
    """
    in case we want to use the common logger from a procedural interface
    """
    return logging.getLogger(appname)
