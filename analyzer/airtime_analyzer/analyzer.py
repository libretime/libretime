# TODO: use an abstract base class (ie. import from abc ...) once we have python >=3.3 that supports @staticmethod with @abstractmethod


class Analyzer:
    """Abstract base class for all "analyzers"."""

    @staticmethod
    def analyze(filename, metadata):
        raise NotImplementedError
