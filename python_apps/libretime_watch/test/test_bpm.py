'''
Test that we calculate the BPM correctly.
'''

from subprocess import Popen, PIPE
import requests
from libretime_watch import metadata

audio_uri = 'https://upload.wikimedia.org/wikipedia/commons/3/3f/Aam_Jhora-Audio.ogg'
filename = 'tmp.ogg'
p = Popen(['curl', '-o', filename, audio_uri], stdout=PIPE, stderr=PIPE)
p.communicate()
bpm = metadata.calculate_bpm(filename)
print("BPM: {0}".format(bpm))
assert bpm == 84, "Incorrect calculation of BPM"