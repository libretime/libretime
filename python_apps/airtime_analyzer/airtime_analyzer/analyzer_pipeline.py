from metadata_analyzer import MetadataAnalyzer

class AnalyzerPipeline:

    def __init__(self):
        pass

    #TODO: Take a JSON message and perform the necessary analysis.
    #TODO: Comment the shit out of this
    @staticmethod
    def run_analysis(json_msg, queue):
        # TODO: Pass the JSON along to each analyzer??
        #print MetadataAnalyzer.analyze("foo.mp3")
        #print ReplayGainAnalyzer.analyze("foo.mp3")
        #raise Exception("Test Crash")
        queue.put(MetadataAnalyzer.analyze("foo.mp3"))

