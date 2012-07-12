import logging
import abc

logger = logging.getLogger('mediamonitor2')
logging.basicConfig(filename='/home/rudi/throwaway/mm2.log', level=logging.DEBUG)

class Loggable(object):
    __metaclass__ = abc.ABCMeta
    @property
    def logger(self):
        if not hasattr(self,"_logger"): self._logger = logging.getLogger('mediamonitor2')
        return self._logger
