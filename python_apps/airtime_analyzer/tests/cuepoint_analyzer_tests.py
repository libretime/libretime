from nose.tools import *
from airtime_analyzer.cuepoint_analyzer import CuePointAnalyzer

def check_default_metadata(metadata):
    ''' Check that the values extract by Silan/CuePointAnalyzer on our test audio files match what we expect.
    :param metadata: a metadata dictionary
    :return: Nothing
    '''
    # We give silan some leeway here by specifying a tolerance
    tolerance_seconds = 0.1
    length_seconds = 3.9
    assert abs(metadata['length_seconds'] - length_seconds) < tolerance_seconds
    assert abs(float(metadata['cuein'])) < tolerance_seconds
    assert abs(float(metadata['cueout']) - length_seconds) < tolerance_seconds

def test_missing_silan():
    old_silan = CuePointAnalyzer.SILAN_EXECUTABLE
    CuePointAnalyzer.SILAN_EXECUTABLE = 'foosdaf'
    metadata = CuePointAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-stereo-utf8.mp3', dict())
    CuePointAnalyzer.SILAN_EXECUTABLE = old_silan # Need to put this back

def test_invalid_filepath():
    metadata = CuePointAnalyzer.analyze(u'non-existent-file', dict())


def test_mp3_utf8():
    metadata = CuePointAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-stereo-utf8.mp3', dict())
    check_default_metadata(metadata)

def test_mp3_dualmono():
    metadata = CuePointAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-dualmono.mp3', dict())
    check_default_metadata(metadata)

def test_mp3_jointstereo():
    metadata = CuePointAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-jointstereo.mp3', dict())
    check_default_metadata(metadata)

def test_mp3_simplestereo():
    metadata = CuePointAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-simplestereo.mp3', dict())
    check_default_metadata(metadata)

def test_mp3_stereo():
    metadata = CuePointAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-stereo.mp3', dict())
    check_default_metadata(metadata)

def test_mp3_mono():
    metadata = CuePointAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-mono.mp3', dict())
    check_default_metadata(metadata)

def test_ogg_stereo():
    metadata = CuePointAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-stereo.ogg', dict())
    check_default_metadata(metadata)

def test_invalid_wma():
    metadata = CuePointAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-stereo-invalid.wma', dict())

def test_m4a_stereo():
    metadata = CuePointAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-stereo.m4a', dict())
    check_default_metadata(metadata)

def test_wav_stereo():
    metadata = CuePointAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-stereo.wav', dict())
    check_default_metadata(metadata)
