import logging
import multiprocessing 
from metadata_analyzer import MetadataAnalyzer
from filemover_analyzer import FileMoverAnalyzer

class AnalyzerPipeline:

    # Take message dictionary and perform the necessary analysis.
    @staticmethod
    def run_analysis(queue, audio_file_path, import_directory, original_filename):

        if not isinstance(queue, multiprocessing.queues.Queue):
            raise TypeError("queue must be a multiprocessing.Queue()")
        if not isinstance(audio_file_path, unicode):
            raise TypeError("audio_file_path must be unicode. Was of type " + type(audio_file_path).__name__ + " instead.")
        if not isinstance(import_directory, unicode):
            raise TypeError("import_directory must be unicode. Was of type " + type(import_directory).__name__ + " instead.")
        if not isinstance(original_filename, unicode):
            raise TypeError("original_filename must be unicode. Was of type " + type(original_filename).__name__ + " instead.")

        #print ReplayGainAnalyzer.analyze("foo.mp3")

        # Analyze the audio file we were told to analyze:
        # First, we extract the ID3 tags and other metadata:
        metadata = dict()
        metadata = MetadataAnalyzer.analyze(audio_file_path, metadata)
        metadata = FileMoverAnalyzer.move(audio_file_path, import_directory, original_filename, metadata)
        metadata["import_status"] = 0 # imported

        # Note that the queue we're putting the results into is our interprocess communication 
        # back to the main process.

        #Pass all the file metadata back to the main analyzer process, which then passes
        #it back to the Airtime web application.
        queue.put(metadata)


