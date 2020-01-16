# -*- coding: utf-8 -*-
from __future__ import print_function
import datetime
import mutagen
import mock
from nose.tools import *
from airtime_analyzer.metadata_analyzer import MetadataAnalyzer 

def setup():
    pass

def teardown():
    pass

def check_default_metadata(metadata):
    assert metadata['track_title'] == u'Test Title'
    assert metadata['artist_name'] == u'Test Artist'
    assert metadata['album_title'] == u'Test Album'
    assert metadata['year'] == u'1999'
    assert metadata['genre'] == u'Test Genre'
    assert metadata['track_number'] == u'1'
    assert metadata["length"] == str(datetime.timedelta(seconds=metadata["length_seconds"]))

def test_mp3_mono():
    metadata = MetadataAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-mono.mp3', dict())
    check_default_metadata(metadata)
    assert metadata['channels'] == 1
    assert metadata['bit_rate'] == 63998
    assert abs(metadata['length_seconds'] - 3.9) < 0.1
    assert metadata['mime'] == 'audio/mp3' # Not unicode because MIMEs aren't.
    assert metadata['track_total'] == u'10' # MP3s can have a track_total
    #Mutagen doesn't extract comments from mp3s it seems

def test_mp3_jointstereo():
    metadata = MetadataAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-jointstereo.mp3', dict())
    check_default_metadata(metadata)
    assert metadata['channels'] == 2
    assert metadata['bit_rate'] == 127998
    assert abs(metadata['length_seconds'] - 3.9) < 0.1
    assert metadata['mime'] == 'audio/mp3'
    assert metadata['track_total'] == u'10' # MP3s can have a track_total

def test_mp3_simplestereo():
    metadata = MetadataAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-simplestereo.mp3', dict())
    check_default_metadata(metadata)
    assert metadata['channels'] == 2
    assert metadata['bit_rate'] == 127998
    assert abs(metadata['length_seconds'] - 3.9) < 0.1
    assert metadata['mime'] == 'audio/mp3'
    assert metadata['track_total'] == u'10' # MP3s can have a track_total

def test_mp3_dualmono():
    metadata = MetadataAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-dualmono.mp3', dict())
    check_default_metadata(metadata)
    assert metadata['channels'] == 2
    assert metadata['bit_rate'] == 127998
    assert abs(metadata['length_seconds'] - 3.9) < 0.1
    assert metadata['mime'] == 'audio/mp3'
    assert metadata['track_total'] == u'10' # MP3s can have a track_total


def test_ogg_mono():
    metadata = MetadataAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-mono.ogg', dict())
    check_default_metadata(metadata)
    assert metadata['channels'] == 1
    assert metadata['bit_rate'] == 80000
    assert abs(metadata['length_seconds'] - 3.8) < 0.1
    assert metadata['mime'] == 'audio/vorbis'
    assert metadata['comment'] == u'Test Comment'

def test_ogg_stereo():
    metadata = MetadataAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-stereo.ogg', dict())
    check_default_metadata(metadata)
    assert metadata['channels'] == 2
    assert metadata['bit_rate'] == 112000
    assert abs(metadata['length_seconds'] - 3.8) < 0.1
    assert metadata['mime'] == 'audio/vorbis'
    assert metadata['comment'] == u'Test Comment'

''' faac and avconv can't seem to create a proper mono AAC file... ugh
def test_aac_mono():
    metadata = MetadataAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-mono.m4a')
    print("Mono AAC metadata:")
    print(metadata)
    check_default_metadata(metadata)
    assert metadata['channels'] == 1
    assert metadata['bit_rate'] == 80000
    assert abs(metadata['length_seconds'] - 3.8) < 0.1
    assert metadata['mime'] == 'audio/mp4'
    assert metadata['comment'] == u'Test Comment'
'''

def test_aac_stereo():
    metadata = MetadataAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-stereo.m4a', dict())
    check_default_metadata(metadata)
    assert metadata['channels'] == 2
    assert metadata['bit_rate'] == 102619
    assert abs(metadata['length_seconds'] - 3.8) < 0.1
    assert metadata['mime'] == 'audio/mp4'
    assert metadata['comment'] == u'Test Comment'

def test_mp3_utf8():
    metadata = MetadataAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-stereo-utf8.mp3', dict())
    # Using a bunch of different UTF-8 codepages here. Test data is from:
    #   http://winrus.com/utf8-jap.htm
    assert metadata['track_title'] == u'ｱｲｳｴｵｶｷｸｹｺｻｼｽｾｿﾀﾁﾂﾃ'
    assert metadata['artist_name'] == u'てすと'
    assert metadata['album_title'] == u'Ä ä Ü ü ß'
    assert metadata['year'] == u'1999'
    assert metadata['genre'] == u'Я Б Г Д Ж Й'
    assert metadata['track_number'] == u'1'
    assert metadata['channels'] == 2
    assert metadata['bit_rate'] < 130000
    assert metadata['bit_rate'] > 127000
    assert abs(metadata['length_seconds'] - 3.9) < 0.1
    assert metadata['mime'] == 'audio/mp3'
    assert metadata['track_total'] == u'10' # MP3s can have a track_total

def test_invalid_wma():
    metadata = MetadataAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-stereo-invalid.wma', dict())
    assert metadata['mime'] == 'audio/x-ms-wma'

def test_wav_stereo():
    metadata = MetadataAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-stereo.wav', dict())
    assert metadata['mime'] == 'audio/x-wav'
    assert abs(metadata['length_seconds'] - 3.9) < 0.1
    assert metadata['channels'] == 2
    assert metadata['sample_rate'] == 44100


# Make sure the parameter checking works
@raises(TypeError)
def test_move_wrong_string_param1():
    not_unicode = 'asdfasdf'
    MetadataAnalyzer.analyze(not_unicode, dict())

@raises(TypeError)
def test_move_wrong_metadata_dict():
    not_a_dict = list()
    MetadataAnalyzer.analyze(u'asdfasdf', not_a_dict)

# Test an mp3 file where the number of channels is invalid or missing:
def test_mp3_bad_channels():
    filename = u'tests/test_data/44100Hz-16bit-mono.mp3'
    ''' 
        It'd be a pain in the ass to construct a real MP3 with an invalid number
        of channels by hand because that value is stored in every MP3 frame in the file
    '''
    audio_file = mutagen.File(filename, easy=True)
    audio_file.info.mode = 1777
    with mock.patch('airtime_analyzer.metadata_analyzer.mutagen') as mock_mutagen:
        mock_mutagen.File.return_value = audio_file
        #mock_mutagen.side_effect = lambda *args, **kw: audio_file #File(*args, **kw)

    metadata = MetadataAnalyzer.analyze(filename, dict())
    check_default_metadata(metadata)
    assert metadata['channels'] == 1
    assert metadata['bit_rate'] == 63998
    assert abs(metadata['length_seconds'] - 3.9) < 0.1
    assert metadata['mime'] == 'audio/mp3' # Not unicode because MIMEs aren't.
    assert metadata['track_total'] == u'10' # MP3s can have a track_total
    #Mutagen doesn't extract comments from mp3s it seems

def test_unparsable_file():
    MetadataAnalyzer.analyze(u'README.rst', dict())
