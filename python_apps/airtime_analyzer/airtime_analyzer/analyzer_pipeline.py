import logging
import multiprocessing 
import shutil
import os, errno
import time
import uuid 
from metadata_analyzer import MetadataAnalyzer

class AnalyzerPipeline:

    # Constructor
    def __init__(self):
        pass

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
        results = MetadataAnalyzer.analyze(audio_file_path)

        # Note that the queue we're putting the results into is our interprocess communication 
        # back to the main process.

        #Import the file over to it's final location.
        #TODO: Move all this file moving stuff to its own Analyzer class.
        #      Also, handle the case where the move fails and write some code
        #      to possibly move the file to problem_files.
       
        final_file_path = import_directory
        if results.has_key("artist_name"):
            final_file_path += "/" + results["artist_name"]
        if results.has_key("album"):
            final_file_path += "/" + results["album"]
        final_file_path += "/" + original_filename

        #Ensure any redundant slashes are stripped
        final_file_path = os.path.normpath(final_file_path)

        #If a file with the same name already exists in the "import" directory, then
        #we add a unique string to the end of this one. We never overwrite a file on import
        #because if we did that, it would mean Airtime's database would have 
        #the wrong information for the file we just overwrote (eg. the song length would be wrong!)
        if os.path.exists(final_file_path) and not os.path.samefile(audio_file_path, final_file_path):
            #If the final file path is the same as the file we've been told to import (which
            #you often do when you're debugging), then don't move the file at all.

            base_file_path, file_extension = os.path.splitext(final_file_path)
            final_file_path = "%s_%s%s" % (base_file_path, time.strftime("%m-%d-%Y-%H-%M-%S", time.localtime()), file_extension)

            #If THAT path exists, append a UUID instead:
            while os.path.exists(final_file_path):
                base_file_path, file_extension = os.path.splitext(final_file_path)
                final_file_path = "%s_%s%s" % (base_file_path, str(uuid.uuid4()), file_extension)

            #Ensure the full path to the file exists
            mkdir_p(os.path.dirname(final_file_path))
            
            #Move the file into its final destination directory 
            shutil.move(audio_file_path, final_file_path)


        #Pass the full path back to Airtime
        results["full_path"] = final_file_path
        queue.put(results)


def mkdir_p(path):
    try:
        os.makedirs(path)
    except OSError as exc: # Python >2.5
        if exc.errno == errno.EEXIST and os.path.isdir(path):
            pass
        else: raise

