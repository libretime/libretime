import pytest
from airtime_analyzer.analyzer import Analyzer


def test_analyze():
    with pytest.raises(NotImplementedError):
        abstract_analyzer = Analyzer()
        abstract_analyzer.analyze(u"foo", dict())
