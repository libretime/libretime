
class Analyzer:
    """ Abstract base class fpr all "analyzers".
    """
    @staticmethod
    def analyze(filename, metadata):
        raise NotImplementedError

'''
class AnalyzerError(Error):
    def __init__(self):
        super.__init__(self)
'''
