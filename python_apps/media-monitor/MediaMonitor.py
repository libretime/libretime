#!/usr/local/bin/python
import logging
import logging.config
import json
import time
import datetime
import os
import sys
import hashlib
import json

from subprocess import Popen, PIPE, STDOUT

from configobj import ConfigObj

import mutagen
import pyinotify
from pyinotify import WatchManager, Notifier, ProcessEvent

# For RabbitMQ
from kombu.connection import BrokerConnection
from kombu.messaging import Exchange, Queue, Consumer, Producer
from api_clients import api_client

# configure logging
try:
    logging.config.fileConfig("logging.cfg")
except Exception, e:
    print 'Error configuring logging: ', e
    sys.exit()

# loading config file
try:
    config = ConfigObj('/etc/airtime/media-monitor.cfg')
except Exception, e:
    logger = logging.getLogger();
    logger.error('Error loading config file: %s', e)
    sys.exit()

"""
list of supported easy tags in mutagen version 1.20
['albumartistsort', 'musicbrainz_albumstatus', 'lyricist', 'releasecountry', 'date', 'performer', 'musicbrainz_albumartistid', 'composer', 'encodedby', 'tracknumber', 'musicbrainz_albumid', 'album', 'asin', 'musicbrainz_artistid', 'mood', 'copyright', 'author', 'media', 'length', 'version', 'artistsort', 'titlesort', 'discsubtitle', 'website', 'musicip_fingerprint', 'conductor', 'compilation', 'barcode', 'performer:*', 'composersort', 'musicbrainz_discid', 'musicbrainz_albumtype', 'genre', 'isrc', 'discnumber', 'musicbrainz_trmid', 'replaygain_*_gain', 'musicip_puid', 'artist', 'title', 'bpm', 'musicbrainz_trackid', 'arranger', 'albumsort', 'replaygain_*_peak', 'organization']
"""

def checkRabbitMQ(notifier):
    try:
        notifier.connection.drain_events(timeout=int(config["check_airtime_events"]))
    except Exception, e:
            logger = logging.getLogger('root')
            logger.info("%s", e)

class AirtimeNotifier(Notifier):

    def __init__(self, watch_manager, default_proc_fun=None, read_freq=0, threshold=0, timeout=None):
        Notifier.__init__(self, watch_manager, default_proc_fun, read_freq, threshold, timeout)

        self.airtime2mutagen = {\
        "track_title": "title",\
        "artist_name": "artist",\
        "album_title": "album",\
        "genre": "genre",\
        "mood": "mood",\
        "track_number": "tracknumber",\
        "bpm": "bpm",\
        "label": "organization",\
        "composer": "composer",\
        "encoded_by": "encodedby",\
        "conductor": "conductor",\
        "year": "date",\
        "info_url": "website",\
        "isrc_number": "isrc",\
        "copyright": "copyright",\
        }
        
        schedule_exchange = Exchange("airtime-media-monitor", "direct", durable=True, auto_delete=True)
        schedule_queue = Queue("media-monitor", exchange=schedule_exchange, key="filesystem")
        self.connection = BrokerConnection(config["rabbitmq_host"], config["rabbitmq_user"], config["rabbitmq_password"], "/")
        channel = self.connection.channel()
        consumer = Consumer(channel, schedule_queue)
        consumer.register_callback(self.handle_message)
        consumer.consume()

    def handle_message(self, body, message):
        # ACK the message to take it off the queue
        message.ack()

        logger = logging.getLogger('root')
        logger.info("Received md from RabbitMQ: " + body)

        m =  json.loads(message.body) 
        airtime_file = mutagen.File(m['filepath'], easy=True)
        del m['filepath']
        for key in m.keys() :
            if m[key] != "" :
                airtime_file[self.airtime2mutagen[key]] = m[key]

        airtime_file.save()

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

        self.logger = logging.getLogger('root')

        self.temp_files = {}

    def update_airtime(self, event):
        self.logger.info("Updating Change to Airtime")
        try: 
            f = open(event.pathname, 'rb')
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

        except Exception, e:
            self.logger.info("%s", e)

    def process_IN_CREATE(self, event):
        if not event.dir :
            filename_info = event.name.split(".")

            #file created is a tmp file which will be modified and then moved back to the original filename.
            if len(filename_info) > 2 :
                self.temp_files[event.pathname] = None
            #This is a newly imported file.
            else :
                pass

            self.logger.info("%s: %s", event.maskname, event.pathname)

    #event.path : /srv/airtime/stor/bd2
    #event.name : bd2aa73b58d9c8abcced989621846e99.mp3
    #event.pathname : /srv/airtime/stor/bd2/bd2aa73b58d9c8abcced989621846e99.mp3
    def process_IN_MODIFY(self, event):
        if not event.dir :
            filename_info = event.name.split(".")

            #file modified is not a tmp file.
            if len(filename_info) == 2 :
                self.update_airtime(event) 

        self.logger.info("%s: path: %s name: %s", event.maskname, event.path, event.name)

    def process_IN_MOVED_FROM(self, event):
        if event.pathname in self.temp_files :
            del self.temp_files[event.pathname]
            self.temp_files[event.cookie] = event.pathname

        self.logger.info("%s: %s", event.maskname, event.pathname)

    def process_IN_MOVED_TO(self, event):
        if event.cookie in self.temp_files :
            del self.temp_files[event.cookie]
            self.update_airtime(event)
       
        self.logger.info("%s: %s", event.maskname, event.pathname)

    def process_default(self, event):
        self.logger.info("%s: %s", event.maskname, event.pathname)

if __name__ == '__main__':

    try:
        # watched events
        mask = pyinotify.IN_CREATE | pyinotify.IN_MODIFY | pyinotify.IN_MOVED_FROM | pyinotify.IN_MOVED_TO
        #mask = pyinotify.ALL_EVENTS

        wm = WatchManager()
        wdd = wm.add_watch('/srv/airtime/stor', mask, rec=True, auto_add=True)

        notifier = AirtimeNotifier(wm, MediaMonitor(), read_freq=int(config["check_filesystem_events"]), timeout=1)
        notifier.coalesce_events()
        notifier.loop(callback=checkRabbitMQ)
    except KeyboardInterrupt:
        notifier.stop()


