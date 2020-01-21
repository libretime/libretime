from __future__ import print_function
from nose.tools import *
from airtime_analyzer.replaygain_analyzer import ReplayGainAnalyzer


def check_default_metadata(metadata):
    ''' Check that the values extract by Silan/CuePointAnalyzer on our test audio files match what we expect.
    :param metadata: a metadata dictionary
    :return: Nothing
    '''
    '''
    # We give python-rgain some leeway here by specifying a tolerance. It's not perfectly consistent across codecs...
    assert abs(metadata['cuein']) < tolerance_seconds
    assert abs(metadata['cueout'] - length_seconds) < tolerance_seconds
    '''
    tolerance = 0.30
    expected_replaygain = 5.0
    print(metadata['replay_gain'])
    assert abs(metadata['replay_gain'] - expected_replaygain) < tolerance

def test_missing_replaygain():
    old_rg = ReplayGainAnalyzer.REPLAYGAIN_EXECUTABLE
    ReplayGainAnalyzer.REPLAYGAIN_EXECUTABLE = 'foosdaf'
    metadata = ReplayGainAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-stereo-utf8.mp3', dict())
    ReplayGainAnalyzer.REPLAYGAIN_EXECUTABLE = old_rg # Need to put this back

def test_invalid_filepath():
    metadata = ReplayGainAnalyzer.analyze(u'non-existent-file', dict())


def test_mp3_utf8():
    metadata = ReplayGainAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-stereo-utf8.mp3', dict())
    check_default_metadata(metadata)
test_mp3_utf8.rgain = True

def test_mp3_dualmono():
    metadata = ReplayGainAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-dualmono.mp3', dict())
    check_default_metadata(metadata)
test_mp3_dualmono.rgain = True

def test_mp3_jointstereo():
    metadata = ReplayGainAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-jointstereo.mp3', dict())
    check_default_metadata(metadata)
test_mp3_jointstereo.rgain = True

def test_mp3_simplestereo():
    metadata = ReplayGainAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-simplestereo.mp3', dict())
    check_default_metadata(metadata)
test_mp3_simplestereo.rgain = True

def test_mp3_stereo():
    metadata = ReplayGainAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-stereo.mp3', dict())
    check_default_metadata(metadata)
test_mp3_stereo.rgain = True

def test_mp3_mono():
    metadata = ReplayGainAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-mono.mp3', dict())
    check_default_metadata(metadata)
test_mp3_mono.rgain = True

def test_ogg_stereo():
    metadata = ReplayGainAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-stereo.ogg', dict())
    check_default_metadata(metadata)
test_ogg_stereo = True

def test_invalid_wma():
    metadata = ReplayGainAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-stereo-invalid.wma', dict())
test_invalid_wma.rgain = True

def test_mp3_missing_id3_header():
    metadata = ReplayGainAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-mp3-missingid3header.mp3', dict())
test_mp3_missing_id3_header.rgain = True

def test_m4a_stereo():
    metadata = ReplayGainAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-stereo.m4a', dict())
    check_default_metadata(metadata)
test_m4a_stereo.rgain = True

''' WAVE is not supported by python-rgain yet
def test_wav_stereo():
    metadata = ReplayGainAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-stereo.wav', dict())
    check_default_metadata(metadata)
test_wav_stereo.rgain = True
'''
