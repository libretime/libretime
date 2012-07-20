import logging
import abc
from media.monitor.pure import LazyProperty

logger = logging.getLogger('mediamonitor2')
logging.basicConfig(filename='/home/rudi/throwaway/mm2.log', level=logging.DEBUG)

class Loggable(object):
    __metaclass__ = abc.ABCMeta
    # TODO : replace this boilerplate with LazyProperty
    @LazyProperty
    def logger(self):
        # TODO : Clean this up
        if not hasattr(self,"_logger"): self._logger = logging.getLogger('mediamonitor2')
        return self._logger

def get_logger():
    return logging.getLogger('mediamonitor2')
