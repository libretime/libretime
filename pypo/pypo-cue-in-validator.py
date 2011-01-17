import unittest

from util.cue_file import CueFile

from mutagen.mp3 import MP3
from mutagen.oggvorbis import OggVorbis
import random
import string

class test(unittest.TestCase):

    """

    A test class for the cue_in module.

    """

    def setUp(self):
        self.cue_file = CueFile()
        
    def test_cue_mp3(self):
        src = '../audio_samples/OpSound/Peter_Rudenko_-_Opening.mp3'
        dst = '/tmp/' + "".join([random.choice(string.letters) for i in xrange(10)]) + '.mp3'
        self.cue_file.cue(src, dst, 5, 5)
        src_length = MP3(src).info.length
        dst_length = MP3(dst).info.length
        print src + " " + str(src_length)
        print dst + " " + str(dst_length)
        self.assertTrue(dst_length < src_length)

    def test_cue_ogg(self):
        src = '../audio_samples/OpSound/ACDC_-_Back_In_Black-sample.ogg'
        dst = '/tmp/' + "".join([random.choice(string.letters) for i in xrange(10)]) + '.ogg'
        self.cue_file.cue(src, dst, 5, 5)
        src_length = OggVorbis(src).info.length
        dst_length = OggVorbis(dst).info.length
        print src + " " + str(src_length)
        print dst + " " + str(dst_length)
        self.assertTrue(dst_length < src_length)

if __name__ == '__main__':
    unittest.main()
