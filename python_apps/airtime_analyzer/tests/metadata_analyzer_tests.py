# -*- coding: utf-8 -*-
import datetime
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
    assert metadata['bit_rate'] == 64000
    assert metadata['length_seconds'] == 3.90925 
    assert metadata['mime'] == 'audio/mpeg' # Not unicode because MIMEs aren't.
    assert metadata['track_total'] == u'10' # MP3s can have a track_total
    #Mutagen doesn't extract comments from mp3s it seems

def test_mp3_jointstereo():
    metadata = MetadataAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-jointstereo.mp3', dict())
    check_default_metadata(metadata)
    assert metadata['channels'] == 2
    assert metadata['bit_rate'] == 128000
    assert metadata['length_seconds'] == 3.90075 
    assert metadata['mime'] == 'audio/mpeg'
    assert metadata['track_total'] == u'10' # MP3s can have a track_total

def test_mp3_simplestereo():
    metadata = MetadataAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-simplestereo.mp3', dict())
    check_default_metadata(metadata)
    assert metadata['channels'] == 2
    assert metadata['bit_rate'] == 128000
    assert metadata['length_seconds'] == 3.90075 
    assert metadata['mime'] == 'audio/mpeg'
    assert metadata['track_total'] == u'10' # MP3s can have a track_total

def test_mp3_dualmono():
    metadata = MetadataAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-dualmono.mp3', dict())
    check_default_metadata(metadata)
    assert metadata['channels'] == 2
    assert metadata['bit_rate'] == 128000
    assert metadata['length_seconds'] == 3.90075 
    assert metadata['mime'] == 'audio/mpeg'
    assert metadata['track_total'] == u'10' # MP3s can have a track_total


def test_ogg_mono():
    metadata = MetadataAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-mono.ogg', dict())
    check_default_metadata(metadata)
    assert metadata['channels'] == 1
    assert metadata['bit_rate'] == 80000
    assert metadata['length_seconds'] == 3.8394104308390022
    assert metadata['mime'] == 'application/ogg'
    assert metadata['comment'] == u'Test Comment'

def test_ogg_stereo():
    metadata = MetadataAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-stereo.ogg', dict())
    check_default_metadata(metadata)
    assert metadata['channels'] == 2
    assert metadata['bit_rate'] == 112000
    assert metadata['length_seconds'] == 3.8394104308390022
    assert metadata['mime'] == 'application/ogg'
    assert metadata['comment'] == u'Test Comment'

''' faac and avconv can't seem to create a proper mono AAC file... ugh
def test_aac_mono():
    metadata = MetadataAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-mono.m4a')
    print "Mono AAC metadata:"
    print metadata
    check_default_metadata(metadata)
    assert metadata['channels'] == 1
    assert metadata['bit_rate'] == 80000
    assert metadata['length_seconds'] == 3.8394104308390022
    assert metadata['mime'] == 'video/mp4'
    assert metadata['comment'] == u'Test Comment'
'''

def test_aac_stereo():
    metadata = MetadataAnalyzer.analyze(u'tests/test_data/44100Hz-16bit-stereo.m4a', dict())
    check_default_metadata(metadata)
    assert metadata['channels'] == 2
    assert metadata['bit_rate'] == 102619
    assert metadata['length_seconds'] == 3.8626303854875284 
    assert metadata['mime'] == 'video/mp4'
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
    assert metadata['bit_rate'] == 128000
    assert metadata['length_seconds'] == 3.90075 
    assert metadata['mime'] == 'audio/mpeg'
    assert metadata['track_total'] == u'10' # MP3s can have a track_total

