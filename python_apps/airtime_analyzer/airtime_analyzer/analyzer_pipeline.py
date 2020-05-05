""" Analyzes and imports an audio file into the Airtime library.
"""
import logging
import threading
import multiprocessing
from queue import Queue
import configparser
from .metadata_analyzer import MetadataAnalyzer
from .filemover_analyzer import FileMoverAnalyzer
from .cuepoint_analyzer import CuePointAnalyzer
from .replaygain_analyzer import ReplayGainAnalyzer
from .playability_analyzer import *

class AnalyzerPipeline:
    """ Analyzes and imports an audio file into the Airtime library.

        This currently performs metadata extraction (eg. gets the ID3 tags from an MP3),
        then moves the file to the Airtime music library (stor/imported), and returns
        the results back to the parent process. This class is used in an isolated process
        so that if it crashes, it does not kill the entire airtime_analyzer daemon and
        the failure to import can be reported back to the web application.
    """

    IMPORT_STATUS_FAILED = 2

    @staticmethod
    def run_analysis(queue, audio_file_path, import_directory, original_filename, storage_backend, file_prefix):
        """Analyze and import an audio file, and put all extracted metadata into queue.

        Keyword arguments:
            queue: A multiprocessing.queues.Queue which will be used to pass the
                   extracted metadata back to the parent process.
            audio_file_path: Path on disk to the audio file to analyze.
            import_directory: Path to the final Airtime "import" directory where
                              we will move the file.
            original_filename: The original filename of the file, which we'll try to
                               preserve. The file at audio_file_path typically has a
                               temporary randomly generated name, which is why we want
                               to know what the original name was.
            storage_backend: String indicating the storage backend (amazon_s3 or file)
            file_prefix:
        """
        # It is super critical to initialize a separate log file here so that we
        # don't inherit logging/locks from the parent process. Supposedly
        # this can lead to Bad Things (deadlocks): http://bugs.python.org/issue6721
        AnalyzerPipeline.python_logger_deadlock_workaround()

        try:
            if not isinstance(queue, Queue):
                raise TypeError("queue must be a Queue.Queue()")
            if not isinstance(audio_file_path, str):
                raise TypeError("audio_file_path must be unicode. Was of type " + type(audio_file_path).__name__ + " instead.")
            if not isinstance(import_directory, str):
                raise TypeError("import_directory must be unicode. Was of type " + type(import_directory).__name__ + " instead.")
            if not isinstance(original_filename, str):
                raise TypeError("original_filename must be unicode. Was of type " + type(original_filename).__name__ + " instead.")
            if not isinstance(file_prefix, str):
                raise TypeError("file_prefix must be unicode. Was of type " + type(file_prefix).__name__ + " instead.")


            # Analyze the audio file we were told to analyze:
            # First, we extract the ID3 tags and other metadata:
            metadata = dict()
            metadata["file_prefix"] = file_prefix

            metadata = MetadataAnalyzer.analyze(audio_file_path, metadata)
            metadata = CuePointAnalyzer.analyze(audio_file_path, metadata)
            metadata = ReplayGainAnalyzer.analyze(audio_file_path, metadata)
            metadata = PlayabilityAnalyzer.analyze(audio_file_path, metadata)

            metadata = FileMoverAnalyzer.move(audio_file_path, import_directory, original_filename, metadata)

            metadata["import_status"] = 0 # Successfully imported

            # Note that the queue we're putting the results into is our interprocess communication
            # back to the main process.

            # Pass all the file metadata back to the main analyzer process, which then passes
            # it back to the Airtime web application.
            queue.put(metadata)
        except UnplayableFileError as e:
            logging.exception(e)
            metadata["import_status"] = AnalyzerPipeline.IMPORT_STATUS_FAILED
            metadata["reason"] = "The file could not be played."
            raise e
        except Exception as e:
            # Ensures the traceback for this child process gets written to our log files:
            logging.exception(e)
            raise e

    @staticmethod
    def python_logger_deadlock_workaround():
        # Workaround for: http://bugs.python.org/issue6721#msg140215
        logger_names = list(logging.Logger.manager.loggerDict.keys())
        logger_names.append(None) # Root logger
        for name in logger_names:
            for handler in logging.getLogger(name).handlers:
                handler.createLock()
        logging._lock = threading.RLock()

