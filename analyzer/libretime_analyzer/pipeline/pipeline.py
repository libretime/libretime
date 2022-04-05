""" Analyzes and imports an audio file into the Airtime library.
"""
from enum import Enum
from queue import Queue
from typing import Any, Dict

from loguru import logger
from typing_extensions import Protocol

from .analyze_cuepoint import analyze_cuepoint
from .analyze_metadata import analyze_metadata
from .analyze_playability import UnplayableFileError, analyze_playability
from .analyze_replaygain import analyze_replaygain
from .organise_file import organise_file


class Step(Protocol):
    @staticmethod
    def __call__(filename: str, metadata: Dict[str, Any]):
        ...


class PipelineStatus(int, Enum):
    succeed = 0
    pending = 1
    failed = 2


class Pipeline:
    """Analyzes and imports an audio file into the Airtime library.

    This currently performs metadata extraction (eg. gets the ID3 tags from an MP3),
    then moves the file to the Airtime music library (stor/imported), and returns
    the results back to the parent process. This class is used in an isolated process
    so that if it crashes, it does not kill the entire airtime_analyzer daemon and
    the failure to import can be reported back to the web application.
    """

    @staticmethod
    def run_analysis(
        queue,
        audio_file_path,
        import_directory,
        original_filename,
        storage_backend,
        file_prefix,
    ):
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
        try:
            if not isinstance(queue, Queue):
                raise TypeError("queue must be a Queue.Queue()")
            if not isinstance(audio_file_path, str):
                raise TypeError(
                    "audio_file_path must be unicode. Was of type "
                    + type(audio_file_path).__name__
                    + " instead."
                )
            if not isinstance(import_directory, str):
                raise TypeError(
                    "import_directory must be unicode. Was of type "
                    + type(import_directory).__name__
                    + " instead."
                )
            if not isinstance(original_filename, str):
                raise TypeError(
                    "original_filename must be unicode. Was of type "
                    + type(original_filename).__name__
                    + " instead."
                )
            if not isinstance(file_prefix, str):
                raise TypeError(
                    "file_prefix must be unicode. Was of type "
                    + type(file_prefix).__name__
                    + " instead."
                )

            # Analyze the audio file we were told to analyze:
            # First, we extract the ID3 tags and other metadata:
            metadata = dict()
            metadata["file_prefix"] = file_prefix

            metadata = analyze_metadata(audio_file_path, metadata)
            metadata = analyze_cuepoint(audio_file_path, metadata)
            metadata = analyze_replaygain(audio_file_path, metadata)
            metadata = analyze_playability(audio_file_path, metadata)

            metadata = organise_file(
                audio_file_path, import_directory, original_filename, metadata
            )

            metadata["import_status"] = 0  # Successfully imported

            # Note that the queue we're putting the results into is our interprocess communication
            # back to the main process.

            # Pass all the file metadata back to the main analyzer process, which then passes
            # it back to the Airtime web application.
            queue.put(metadata)
        except UnplayableFileError as e:
            logger.exception(e)
            metadata["import_status"] = PipelineStatus.failed
            metadata["reason"] = "The file could not be played."
            raise e
        except Exception as e:
            # Ensures the traceback for this child process gets written to our log files:
            logger.exception(e)
            raise e
