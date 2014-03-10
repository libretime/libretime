import logging
import multiprocessing 
import shutil
import os
from metadata_analyzer import MetadataAnalyzer

class AnalyzerPipeline:

    # Constructor
    def __init__(self):
        pass

    # Take message dictionary and perform the necessary analysis.
    @staticmethod
    def run_analysis(queue, audio_file_path, final_directory):

        if not isinstance(queue, multiprocessing.queues.Queue):
            raise TypeError("queue must be a multiprocessing.Queue()")
        if not isinstance(audio_file_path, unicode):
            raise TypeError("audio_file_path must be unicode. Was of type " + type(audio_file_path).__name__ + " instead.")
        if not isinstance(final_directory, unicode):
            raise TypeError("final_directory must be unicode. Was of type " + type(final_directory).__name__ + " instead.")


        # Analyze the audio file we were told to analyze:
        # First, we extract the ID3 tags and other metadata:
        queue.put(MetadataAnalyzer.analyze(audio_file_path)) 

        # Note that the queue we're putting the results into is our interprocess communication 
        # back to the main process.

        #print ReplayGainAnalyzer.analyze("foo.mp3")

        final_audio_file_path = final_directory + os.sep + os.path.basename(audio_file_path)
        if os.path.exists(final_audio_file_path) and not os.path.samefile(audio_file_path, final_audio_file_path):
            os.remove(final_audio_file_path)

        shutil.move(audio_file_path, final_audio_file_path)

