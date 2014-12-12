from nose.tools import *
from airtime_analyzer.playability_analyzer import *

def check_default_metadata(metadata):
    ''' Stub function for now in case we need it later.'''
    pass

def test_missing_liquidsoap():
    old_ls = PlayabilityAnalyzer.LIQUIDSOAP_EXECUTABLE
    PlayabilityAnalyzer.LIQUIDSOAP_EXECUTABLE = 'foosdaf'
    metadata = PlayabilityAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-stereo-utf8.mp3', dict())
    PlayabilityAnalyzer.LIQUIDSOAP_EXECUTABLE = old_ls # Need to put this back

@raises(UnplayableFileError)
def test_invalid_filepath():
    metadata = PlayabilityAnalyzer.analyze(u'non-existent-file', dict())

def test_mp3_utf8():
    metadata = PlayabilityAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-stereo-utf8.mp3', dict())
    check_default_metadata(metadata)

def test_mp3_dualmono():
    metadata = PlayabilityAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-dualmono.mp3', dict())
    check_default_metadata(metadata)

def test_mp3_jointstereo():
    metadata = PlayabilityAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-jointstereo.mp3', dict())
    check_default_metadata(metadata)

def test_mp3_simplestereo():
    metadata = PlayabilityAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-simplestereo.mp3', dict())
    check_default_metadata(metadata)

def test_mp3_stereo():
    metadata = PlayabilityAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-stereo.mp3', dict())
    check_default_metadata(metadata)

def test_mp3_mono():
    metadata = PlayabilityAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-mono.mp3', dict())
    check_default_metadata(metadata)

def test_ogg_stereo():
    metadata = PlayabilityAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-stereo.ogg', dict())
    check_default_metadata(metadata)

@raises(UnplayableFileError)
def test_invalid_wma():
    metadata = PlayabilityAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-stereo-invalid.wma', dict())

def test_m4a_stereo():
    metadata = PlayabilityAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-stereo.m4a', dict())
    check_default_metadata(metadata)

def test_wav_stereo():
    metadata = PlayabilityAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-stereo.wav', dict())
    check_default_metadata(metadata)

@raises(UnplayableFileError)
def test_unknown():
    metadata = PlayabilityAnalyzer.analyze(u'http://www.google.com', dict())
    check_default_metadata(metadata)