from nose.tools import *
import os
import shutil
import multiprocessing
import time
import mock
from pprint import pprint
from airtime_analyzer.filemover_analyzer import FileMoverAnalyzer

DEFAULT_AUDIO_FILE = u'tests/test_data/44100Hz-16bit-mono.mp3'
DEFAULT_IMPORT_DEST = u'Test Artist/Test Album/44100Hz-16bit-mono.mp3'

def setup():
    pass

def teardown():
    pass

@raises(Exception)
def test_dont_use_analyze():
    FileMoverAnalyzer.analyze(u'foo', dict())

@raises(TypeError)
def test_move_wrong_string_param1():
    FileMoverAnalyzer.move(42, '', '', dict())

@raises(TypeError)
def test_move_wrong_string_param2():
    FileMoverAnalyzer.move(u'', 23, u'', dict())

@raises(TypeError)
def test_move_wrong_string_param3():
    FileMoverAnalyzer.move('', '', 5, dict())

@raises(TypeError)
def test_move_wrong_dict_param():
    FileMoverAnalyzer.move('', '', '', 12345)

@raises(FileNotFoundError)
def test_move_wrong_string_param3():
    FileMoverAnalyzer.move('', '', '', dict())

def test_basic():
    filename = os.path.basename(DEFAULT_AUDIO_FILE)
    FileMoverAnalyzer.move(DEFAULT_AUDIO_FILE, u'.', filename, dict())
    #Move the file back
    shutil.move("./" + filename, DEFAULT_AUDIO_FILE)
    assert os.path.exists(DEFAULT_AUDIO_FILE)

def test_basic_samefile():
    filename = os.path.basename(DEFAULT_AUDIO_FILE)
    FileMoverAnalyzer.move(DEFAULT_AUDIO_FILE, u'tests/test_data', filename, dict())
    assert os.path.exists(DEFAULT_AUDIO_FILE)

def test_duplicate_file():
    filename = os.path.basename(DEFAULT_AUDIO_FILE)
    #Import the file once
    FileMoverAnalyzer.move(DEFAULT_AUDIO_FILE, u'.', filename, dict())
    #Copy it back to the original location
    shutil.copy("./" + filename, DEFAULT_AUDIO_FILE)
    #Import it again. It shouldn't overwrite the old file and instead create a new
    metadata = dict()
    metadata = FileMoverAnalyzer.move(DEFAULT_AUDIO_FILE, u'.', filename, metadata)
    #Cleanup: move the file (eg. 44100Hz-16bit-mono.mp3) back
    shutil.move("./" + filename, DEFAULT_AUDIO_FILE)
    #Remove the renamed duplicate, eg. 44100Hz-16bit-mono_03-26-2014-11-58.mp3
    os.remove(metadata["full_path"])
    assert os.path.exists(DEFAULT_AUDIO_FILE)

''' If you import three copies of the same file, the behaviour is:
    - The filename is of the first file preserved.
    - The filename of the second file has the timestamp attached to it.
    - The filename of the third file has a UUID placed after the timestamp, but ONLY IF
      it's imported within 1 second of the second file (ie. if the timestamp is the same).
'''
def test_double_duplicate_files():
    # Here we use mock to patch out the time.localtime() function so that it
    # always returns the same value. This allows us to consistently simulate this test cases
    # where the last two of the three files are imported at the same time as the timestamp.
    with mock.patch('airtime_analyzer.filemover_analyzer.time') as mock_time:
        mock_time.localtime.return_value = time.localtime()#date(2010, 10, 8)
        mock_time.side_effect = lambda *args, **kw: time(*args, **kw)

    filename = os.path.basename(DEFAULT_AUDIO_FILE)
    #Import the file once
    FileMoverAnalyzer.move(DEFAULT_AUDIO_FILE, u'.', filename, dict())
    #Copy it back to the original location
    shutil.copy("./" + filename, DEFAULT_AUDIO_FILE)
    #Import it again. It shouldn't overwrite the old file and instead create a new
    first_dup_metadata = dict()
    first_dup_metadata = FileMoverAnalyzer.move(DEFAULT_AUDIO_FILE, u'.', filename,
                                                first_dup_metadata)
    #Copy it back again!
    shutil.copy("./" + filename, DEFAULT_AUDIO_FILE)
    #Reimport for the third time, which should have the same timestamp as the second one
    #thanks to us mocking out time.localtime()
    second_dup_metadata = dict()
    second_dup_metadata = FileMoverAnalyzer.move(DEFAULT_AUDIO_FILE, u'.', filename,
                                                 second_dup_metadata)
    #Cleanup: move the file (eg. 44100Hz-16bit-mono.mp3) back
    shutil.move("./" + filename, DEFAULT_AUDIO_FILE)
    #Remove the renamed duplicate, eg. 44100Hz-16bit-mono_03-26-2014-11-58.mp3
    os.remove(first_dup_metadata["full_path"])
    os.remove(second_dup_metadata["full_path"])
    assert os.path.exists(DEFAULT_AUDIO_FILE)

@raises(OSError)
def test_bad_permissions_destination_dir():
    filename = os.path.basename(DEFAULT_AUDIO_FILE)
    dest_dir = u'/sys/foobar' # /sys is using sysfs on Linux, which is unwritable
    FileMoverAnalyzer.move(DEFAULT_AUDIO_FILE, dest_dir, filename, dict())
    #Move the file back
    shutil.move(os.path.join(dest_dir, filename), DEFAULT_AUDIO_FILE)
    assert os.path.exists(DEFAULT_AUDIO_FILE)

