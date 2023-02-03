from enum import Enum
from queue import Queue
from typing import Any, Dict, Protocol

from loguru import logger

from .analyze_cuepoint import analyze_cuepoint, analyze_duration
from .analyze_metadata import analyze_metadata
from .analyze_playability import UnplayableFileError, analyze_playability
from .analyze_replaygain import analyze_replaygain
from .organise_file import organise_file


class Step(Protocol):
    @staticmethod
    def __call__(filename: str, metadata: Dict[str, Any]):
        ...


class PipelineStatus(int, Enum):
    SUCCEED = 0
    PENDING = 1
    FAILED = 2


class Pipeline:
    """Analyzes and imports an audio file into the Airtime library.

    This currently performs metadata extraction (eg. gets the ID3 tags from an MP3),
    then moves the file to the Airtime music library (stor/imported), and returns
    the results back to the parent process.
    """

    @staticmethod
    def run_analysis(
        queue,
        audio_file_path,
        import_directory,
        original_filename,
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

            # Analyze the audio file we were told to analyze:
            # First, we extract the ID3 tags and other metadata:
            metadata = {}
            metadata = analyze_metadata(audio_file_path, metadata)
            metadata = analyze_duration(audio_file_path, metadata)
            metadata = analyze_cuepoint(audio_file_path, metadata)
            metadata = analyze_replaygain(audio_file_path, metadata)
            metadata = analyze_playability(audio_file_path, metadata)

            metadata = organise_file(
                audio_file_path,
                import_directory,
                original_filename,
                metadata,
            )

            metadata["import_status"] = PipelineStatus.SUCCEED

            # Pass all the file metadata back to the main analyzer process
            queue.put(metadata)
        except UnplayableFileError as exception:
            logger.exception(exception)
            metadata["import_status"] = PipelineStatus.FAILED
            metadata["reason"] = "The file could not be played."
            raise exception
        except Exception as exception:
            logger.exception(exception)
            raise exception
