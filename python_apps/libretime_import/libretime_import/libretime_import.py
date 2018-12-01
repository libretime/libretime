"""Contains the main application class for airtime_analyzer.
"""
import logging
import logging.handlers
import sys
import signal
import traceback
from . import config_file
from functools import partial
from .folder_watcher import FolderWatcher

class LibretimeImportServer:
    """A server for importing uploads to Airtime as background jobs.
    """

    # Constants 
    _LOG_PATH = "/var/log/airtime/libretime_import.log"
   
    # Variables
    _log_level = logging.INFO

    def __init__(self, lt_config_path, http_retry_queue_path, debug=False):

        # Dump a stacktrace with 'kill -SIGUSR2 <PID>'
        signal.signal(signal.SIGUSR2, lambda sig, frame: LibretimeImportServer.dump_stacktrace())

        # Configure logging
        self.setup_logging(debug)

        # Read our libretimesu config file
        lt_config = config_file.read_config_file(lt_config_path)

        # Start watching the uploads folder for new files
        # uploaded files. This blocks until we recieve a shutdown signal.
        FolderWatcher(lt_config)

    def setup_logging(self, debug):
        """Set up nicely formatted logging and log rotation.
        
        Keyword arguments:
        debug -- a boolean indicating whether to enable super verbose logging
                 to the screen and disk.
        """
        if debug:
            self._log_level = logging.DEBUG

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


