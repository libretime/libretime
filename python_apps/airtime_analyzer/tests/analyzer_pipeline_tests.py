from nose.tools import *
import multiprocessing
from airtime_analyzer.analyzer_pipeline import AnalyzerPipeline

DEFAULT_AUDIO_FILE = u'tests/test_data/44100Hz-16bit-mono.mp3'

def setup():
    pass

def teardown():
    pass

def test_basic():
    q = multiprocessing.Queue()
    AnalyzerPipeline.run_analysis(q, DEFAULT_AUDIO_FILE, u'.')
    results = q.get()
    assert results['track_title'] == u'Test Title'
    assert results['artist_name'] == u'Test Artist'
    assert results['album_title'] == u'Test Album'
    assert results['year'] == u'1999'
    assert results['genre'] == u'Test Genre'
    assert results['mime_type'] == 'audio/mpeg' # Not unicode because MIMEs aren't.
    assert results['length_seconds'] == 3.90925 

