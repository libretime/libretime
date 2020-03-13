'''
Test that we calculate the BPM correctly.
'''

from subprocess import Popen, PIPE
import requests
from libretime_watch import metadata



calib = (
    (120, 'https://upload.wikimedia.org/wikipedia/commons/3/3d/Tiempos_d%C3%A9biles_en_metr%C3%B3nomo_a_120_bpm.ogg'),
    (60, 'https://upload.wikimedia.org/wikipedia/commons/4/46/Tiempos_d%C3%A9biles_en_metr%C3%B3nomo_a_60_bpm.ogg'),
    (100, 'https://upload.wikimedia.org/wikipedia/commons/8/87/Tiempos_d%C3%A9biles_en_metr%C3%B3nomo_a_100_bpm.ogg'),
)


for a in calib:
    audio_uri = a[1]
    filename = 'tmp.ogg'
    p = Popen(['curl', '-o', filename, audio_uri], stdout=PIPE, stderr=PIPE)
    p.communicate()
    bpm = metadata.calculate_bpm(filename)
    print("BPM: {0}".format(bpm))
    assert bpm <= a[0] + 1, "Incorrect calculation of BPM"
    assert bpm >= a[0] - 1, "Incorrect calculation of BPM"
