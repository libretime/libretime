
class Analyzer:

    @staticmethod
    def analyze(filename):
        raise NotImplementedError

class AnalyzerError(Exception):
    def __init__(self):
        super.__init__(self)
