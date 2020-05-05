from nose.tools import *
import os
import shutil
import multiprocessing
from queue import Queue
import datetime
from airtime_analyzer.analyzer_pipeline import AnalyzerPipeline
from airtime_analyzer import config_file

DEFAULT_AUDIO_FILE = u'tests/test_data/44100Hz-16bit-mono.mp3'
DEFAULT_IMPORT_DEST = u'Test Artist/Test Album/44100Hz-16bit-mono.mp3'

def setup():
    pass

def teardown():
    #Move the file back
    shutil.move(DEFAULT_IMPORT_DEST, DEFAULT_AUDIO_FILE)
    assert os.path.exists(DEFAULT_AUDIO_FILE)

def test_basic():
    filename = os.path.basename(DEFAULT_AUDIO_FILE)
    q = Queue()
    file_prefix = u''
    storage_backend = "file"
    #This actually imports the file into the "./Test Artist" directory.
    AnalyzerPipeline.run_analysis(q, DEFAULT_AUDIO_FILE, u'.', filename, storage_backend, file_prefix)
    metadata = q.get()
    assert metadata['track_title'] == u'Test Title'
    assert metadata['artist_name'] == u'Test Artist'
    assert metadata['album_title'] == u'Test Album'
    assert metadata['year'] == u'1999'
    assert metadata['genre'] == u'Test Genre'
    assert metadata['mime'] == 'audio/mp3' # Not unicode because MIMEs aren't.
    assert abs(metadata['length_seconds'] - 3.9) < 0.1
    assert metadata["length"] == str(datetime.timedelta(seconds=metadata["length_seconds"]))
    assert os.path.exists(DEFAULT_IMPORT_DEST)

@raises(TypeError)
def test_wrong_type_queue_param():
    AnalyzerPipeline.run_analysis(Queue(), u'', u'', u'')

@raises(TypeError)
def test_wrong_type_string_param2():
    AnalyzerPipeline.run_analysis(Queue(), '', u'', u'')

@raises(TypeError)
def test_wrong_type_string_param3():
    AnalyzerPipeline.run_analysis(Queue(), u'', '', u'')

@raises(TypeError)
def test_wrong_type_string_param4():
    AnalyzerPipeline.run_analysis(Queue(), u'', u'', '')

