from nose.tools import *
import os
import shutil
import multiprocessing
import datetime
from airtime_analyzer.analyzer_pipeline import AnalyzerPipeline

DEFAULT_AUDIO_FILE = u'tests/test_data/44100Hz-16bit-mono.mp3'
DEFAULT_IMPORT_DEST = u'Test Artist/44100Hz-16bit-mono.mp3'

def setup():
    pass

def teardown():
    #Move the file back
    shutil.move(DEFAULT_IMPORT_DEST, DEFAULT_AUDIO_FILE)
    assert os.path.exists(DEFAULT_AUDIO_FILE)

def test_basic():
    filename = os.path.basename(DEFAULT_AUDIO_FILE)
    q = multiprocessing.Queue()
    #This actually imports the file into the "./Test Artist" directory.
    AnalyzerPipeline.run_analysis(q, DEFAULT_AUDIO_FILE, u'.', filename)
    results = q.get()
    assert results['track_title'] == u'Test Title'
    assert results['artist_name'] == u'Test Artist'
    assert results['album_title'] == u'Test Album'
    assert results['year'] == u'1999'
    assert results['genre'] == u'Test Genre'
    assert results['mime'] == 'audio/mpeg' # Not unicode because MIMEs aren't.
    assert results['length_seconds'] == 3.90925
    assert results["length"] == str(datetime.timedelta(seconds=results["length_seconds"]))
    assert os.path.exists(DEFAULT_IMPORT_DEST)

