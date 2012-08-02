import logging
import abc
import traceback
from media.monitor.pure import LazyProperty

logger = logging.getLogger('mediamonitor2')
logging.basicConfig(filename='/home/rudi/throwaway/mm2.log', level=logging.DEBUG)

class Loggable(object):
    __metaclass__ = abc.ABCMeta
    @LazyProperty
    def logger(self):
        # TODO : Clean this up
        if not hasattr(self,"_logger"): self._logger = logging.getLogger('mediamonitor2')
        return self._logger
    def unexpected_exception(self,e):
        self.fatal_exception("'Unexpected' exception has occured:", e)

    def fatal_exception(self, message, e):
        self.logger.error(message)
        self.logger.error( str(e) )
        self.logger.error( traceback.format_exc() )


def get_logger():
    return logging.getLogger('mediamonitor2')
