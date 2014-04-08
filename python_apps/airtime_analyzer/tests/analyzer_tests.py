from nose.tools import *
from airtime_analyzer.analyzer import Analyzer

def setup():
    pass

def teardown():
    pass

@raises(NotImplementedError)
def test_analyze():
    abstract_analyzer = Analyzer()
    abstract_analyzer.analyze(u'foo', dict())
