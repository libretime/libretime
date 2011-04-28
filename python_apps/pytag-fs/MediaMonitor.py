#!/usr/local/bin/python
import logging
import logging.config
import json
import time
import datetime
import os
import sys
import hashlib

from subprocess import Popen, PIPE, STDOUT

from configobj import ConfigObj

import pyinotify
from pyinotify import WatchManager, Notifier, ProcessEvent

import mutagen

from api_clients import api_client

# configure logging
try:
    logging.config.fileConfig("logging.cfg")
except Exception, e:
    print 'Error configuring logging: ', e
    sys.exit()

# loading config file
try:
    config = ConfigObj('/etc/airtime/MediaMonitor.cfg')
except Exception, e:
    print 'Error loading config file: ', e
    sys.exit()

"""
list of supported easy tags in mutagen version 1.20
['albumartistsort', 'musicbrainz_albumstatus', 'lyricist', 'releasecountry', 'date', 'performer', 'musicbrainz_albumartistid', 'composer', 'encodedby', 'tracknumber', 'musicbrainz_albumid', 'album', 'asin', 'musicbrainz_artistid', 'mood', 'copyright', 'author', 'media', 'length', 'version', 'artistsort', 'titlesort', 'discsubtitle', 'website', 'musicip_fingerprint', 'conductor', 'compilation', 'barcode', 'performer:*', 'composersort', 'musicbrainz_discid', 'musicbrainz_albumtype', 'genre', 'isrc', 'discnumber', 'musicbrainz_trmid', 'replaygain_*_gain', 'musicip_puid', 'artist', 'title', 'bpm', 'musicbrainz_trackid', 'arranger', 'albumsort', 'replaygain_*_peak', 'organization']
"""

class MediaMonitor(ProcessEvent):

    def my_init(self):
        """
        Method automatically called from ProcessEvent.__init__(). Additional
        keyworded arguments passed to ProcessEvent.__init__() are then
        delegated to my_init().
        """
        self.api_client = api_client.api_client_factory(config)

        self.mutagen2airtime = {\
        "title": "track_title",\
        "artist": "artist_name",\
        "album": "album_title",\
        "genre": "genre",\
        "mood": "mood",\
        "tracknumber": "track_number",\
        "bpm": "bpm",\
        "organization": "label",\
        "composer": "composer",\
        "encodedby": "encoded_by",\
        "conductor": "conductor",\
        "date": "year",\
        "website": "info_url",\
        "isrc": "isrc_number",\
        "copyright": "copyright",\
        }

    def process_IN_CREATE(self, event):
        if not event.dir :
            #This is a newly imported file.
            print "%s: %s%s" %  (event.maskname, event.path, event.name)

    #event.path : /srv/airtime/stor/bd2
    #event.name : bd2aa73b58d9c8abcced989621846e99.mp3
    #event.pathname : /srv/airtime/stor/bd2/bd2aa73b58d9c8abcced989621846e99.mp3
    def process_IN_MODIFY(self, event):
        if not event.dir :
            f = file(event.pathname, 'rb')
            m = hashlib.md5()
            m.update(f.read())

            md5 = m.hexdigest()
            gunid = event.name.split('.')[0]

            md = {'gunid':gunid, 'md5':md5}

            file_info = mutagen.File(event.pathname, easy=True)
            attrs = self.mutagen2airtime
            for key in file_info.keys() :
                if key in attrs :
                    md[attrs[key]] = file_info[key][0]

            data = {'md': md}

            response = self.api_client.update_media_metadata(data)

        print "%s: path: %s name: %s" %  (event.maskname, event.path, event.name)

    def process_default(self, event):
        print "%s: %s%s" %  (event.maskname, event.path, event.name)

if __name__ == '__main__':

    print 'Media Monitor'

    try:
        # watched events
        mask = pyinotify.IN_CREATE | pyinotify.IN_MODIFY

        wm = WatchManager()
        wdd = wm.add_watch('/srv/airtime/stor', mask, rec=True, auto_add=True)

        notifier = Notifier(wm, MediaMonitor(), read_freq=10, timeout=1)
        notifier.coalesce_events()
        notifier.loop()
    except KeyboardInterrupt:
        notifier.stop()


