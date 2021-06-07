from airtime_analyzer.analyzer import Analyzer
from nose.tools import *


def setup():
    pass


def teardown():
    pass


@raises(NotImplementedError)
def test_analyze():
    abstract_analyzer = Analyzer()
    abstract_analyzer.analyze(u"foo", dict())
