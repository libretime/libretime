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
import shutil

from subprocess import Popen, PIPE, STDOUT

from configobj import ConfigObj

import mutagen
import pyinotify
from pyinotify import WatchManager, Notifier, ProcessEvent

# For RabbitMQ
from kombu.connection import BrokerConnection
from kombu.messaging import Exchange, Queue, Consumer, Producer
from api_clients import api_client

global storage_directory
storage_directory = "/srv/airtime/stor"

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

        self.supported_file_formats = ['mp3', 'ogg']
        self.logger = logging.getLogger('root')
        self.temp_files = {}
        self.imported_renamed_files = {}

    def get_md5(self, filepath):
        f = open(filepath, 'rb')
        m = hashlib.md5()
        m.update(f.read())
        md5 = m.hexdigest()

        return md5

    def ensure_dir(self, filepath):

        directory = os.path.dirname(filepath)

        if ((not os.path.exists(directory)) or ((os.path.exists(directory) and not os.path.isdir(directory)))):
            os.makedirs(directory, 02775)

    def create_unique_filename(self, filepath):

        file_dir = os.path.dirname(filepath)
        filename = os.path.basename(filepath).split(".")[0]
        file_ext = os.path.splitext(filepath)[1]

        if(os.path.exists(filepath)):
            i = 1;
            while(True):
                new_filepath = "%s/%s(%s).%s" % (file_dir, filename, i, file_ext)

                if(os.path.exists(new_filepath)):
                    i = i+1;
                else:
                    filepath = new_filepath

        self.imported_renamed_files[filepath] = 0

        return filepath

    def create_file_path(self, imported_filepath):

        global storage_directory

        original_name = os.path.basename(imported_filepath)
        file_ext = os.path.splitext(imported_filepath)[1]
        file_info = mutagen.File(imported_filepath, easy=True)

        metadata = {'artist':None,
                    'album':None,
                    'title':None,
                    'tracknumber':None}

        for key in metadata.keys():
            if key in file_info:
                metadata[key] = file_info[key][0]

        if metadata['artist'] is not None:
            base = "%s/%s" % (storage_directory, metadata['artist'])
            if metadata['album'] is not None:
                base = "%s/%s" % (base, metadata['album'])
            if metadata['title'] is not None:
                if metadata['tracknumber'] is not None:
                    metadata['tracknumber'] = "%02d" % (int(metadata['tracknumber']))
                    base = "%s/%s - %s" % (base, metadata['tracknumber'], metadata['title'])
                else:
                    base = "%s/%s" % (base, metadata['title'])
            else:
                base = "%s/%s" % (base, original_name)
        else:
            base = "%s/%s" % (storage_directory, original_name)

        base = "%s%s" % (base, file_ext)

        filepath = self.create_unique_filename(base)
        self.ensure_dir(filepath)
        shutil.move(imported_filepath, filepath)

    def ensure_dir(self, filepath):

        directory = os.path.dirname(filepath)

        if ((not os.path.exists(directory)) or ((os.path.exists(directory) and not os.path.isdir(directory)))):
            os.makedirs(directory, 02775)

    def create_unique_filename(self, filepath):

        file_dir = os.path.dirname(filepath)
        print file_dir
        filename = os.path.basename(filepath).split(".")[0]
        print filename
        file_ext = os.path.splitext(filepath)[1]
        print file_ext

        if(os.path.exists(filepath)):
            i = 1;
            while(True):
                new_filepath = "%s/%s(%s).%s" % (file_dir, filename, i, file_ext)

                if(os.path.exists(new_filepath)):
                    i = i+1;
                else:
                    return new_filepath

        return filepath

    def create_file_path(self, imported_filepath):

        global storage_directory

        original_name = os.path.basename(imported_filepath)
        file_ext = os.path.splitext(imported_filepath)[1]
        file_info = mutagen.File(imported_filepath, easy=True)

        metadata = {'artist':None,
                    'album':None,
                    'title':None,
                    'tracknumber':None}

        for key in metadata.keys():
            if key in file_info:
                metadata[key] = file_info[key][0]

        if metadata['artist'] is not None:
            base = "%s/%s" % (storage_directory, metadata['artist'])
            if metadata['album'] is not None:
                base = "%s/%s" % (base, metadata['album'])
            if metadata['title'] is not None:
                if metadata['tracknumber'] is not None:
                    metadata['tracknumber'] = "%02d" % (int(metadata['tracknumber']))
                    base = "%s/%s - %s" % (base, metadata['tracknumber'], metadata['title'])
                else:
                    base = "%s/%s" % (base, metadata['title'])
            else:
                base = "%s/%s" % (base, original_name)
        else:
            base = "%s/%s" % (storage_directory, original_name)

        base = "%s%s" % (base, file_ext)

        filepath = self.create_unique_filename(base)
        self.ensure_dir(filepath)
        shutil.move(imported_filepath, filepath)

    def update_airtime(self, event):
        self.logger.info("Updating Change to Airtime")
        try:
            md5 = self.get_md5(event.pathname)
            md = {'filepath':event.pathname, 'md5':md5}

            file_info = mutagen.File(event.pathname, easy=True)
            attrs = self.mutagen2airtime
            for key in file_info.keys() :
                if key in attrs :
                    md[attrs[key]] = file_info[key][0]

            data = {'md': md}
            response = self.api_client.update_media_metadata(data)

        except Exception, e:
            self.logger.info("%s", e)

    def is_renamed_file(self, filename):
        if filename in self.imported_renamed_files:
            del self.imported_renamed_files[filename]
            return True

        return False

    def is_temp_file(self, filename):
        info = filename.split(".")

        if(info[-2] in self.supported_file_formats):
            return True
        else :
            return False

    def is_audio_file(self, filename):
        info = filename.split(".")

        if(info[-1] in self.supported_file_formats):
            return True
        else :
            return False


    def process_IN_CREATE(self, event):
        if not event.dir:
            #file created is a tmp file which will be modified and then moved back to the original filename.
            if self.is_temp_file(event.name) :
                self.temp_files[event.pathname] = None
            #This is a newly imported file.
            else :
                if not is_renamed_file(event.pathname):
                    self.create_file_path(event.pathname)

            self.logger.info("%s: %s", event.maskname, event.pathname)

    def process_IN_MODIFY(self, event):
        if not event.dir :

            if self.is_audio_file(event.name) :
                self.update_airtime(event)

            self.logger.info("%s: %s", event.maskname, event.pathname)

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
        wdd = wm.add_watch(storage_directory, mask, rec=True, auto_add=True)

        logger = logging.getLogger('root')
        logger.info("Added watch to %s", storage_directory)

        notifier = AirtimeNotifier(wm, MediaMonitor(), read_freq=int(config["check_filesystem_events"]), timeout=1)
        notifier.coalesce_events()
        notifier.loop(callback=checkRabbitMQ)
    except KeyboardInterrupt:
        notifier.stop()
