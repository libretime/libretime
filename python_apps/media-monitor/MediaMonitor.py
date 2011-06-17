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
import math
import socket
import grp
import pwd

from collections import deque

from subprocess import Popen, PIPE, STDOUT

from configobj import ConfigObj

import mutagen
import pyinotify
from pyinotify import WatchManager, Notifier, ProcessEvent

# For RabbitMQ
from kombu.connection import BrokerConnection
from kombu.messaging import Exchange, Queue, Consumer, Producer
from api_clients import api_client

from multiprocessing import Process, Lock

MODE_CREATE = "create"
MODE_MODIFY = "modify"
MODE_MOVED = "moved"
MODE_DELETE = "delete"

global storage_directory
global plupload_directory

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

class MetadataExtractor:

    def __init__(self):

        self.airtime2mutagen = {\
        "MDATA_KEY_TITLE": "title",\
        "MDATA_KEY_CREATOR": "artist",\
        "MDATA_KEY_SOURCE": "album",\
        "MDATA_KEY_GENRE": "genre",\
        "MDATA_KEY_MOOD": "mood",\
        "MDATA_KEY_TRACKNUMBER": "tracknumber",\
        "MDATA_KEY_BPM": "bpm",\
        "MDATA_KEY_LABEL": "organization",\
        "MDATA_KEY_COMPOSER": "composer",\
        "MDATA_KEY_ENCODER": "encodedby",\
        "MDATA_KEY_CONDUCTOR": "conductor",\
        "MDATA_KEY_YEAR": "date",\
        "MDATA_KEY_URL": "website",\
        "MDATA_KEY_ISRC": "isrc",\
        "MDATA_KEY_COPYRIGHT": "copyright",\
        }

        self.mutagen2airtime = {\
        "title": "MDATA_KEY_TITLE",\
        "artist": "MDATA_KEY_CREATOR",\
        "album": "MDATA_KEY_SOURCE",\
        "genre": "MDATA_KEY_GENRE",\
        "mood": "MDATA_KEY_MOOD",\
        "tracknumber": "MDATA_KEY_TRACKNUMBER",\
        "bpm": "MDATA_KEY_BPM",\
        "organization": "MDATA_KEY_LABEL",\
        "composer": "MDATA_KEY_COMPOSER",\
        "encodedby": "MDATA_KEY_ENCODER",\
        "conductor": "MDATA_KEY_CONDUCTOR",\
        "date": "MDATA_KEY_YEAR",\
        "website": "MDATA_KEY_URL",\
        "isrc": "MDATA_KEY_ISRC",\
        "copyright": "MDATA_KEY_COPYRIGHT",\
        }

        self.logger = logging.getLogger('root')

    def get_md5(self, filepath):
        f = open(filepath, 'rb')
        m = hashlib.md5()
        m.update(f.read())
        md5 = m.hexdigest()

        return md5

    ## mutagen_length is in seconds with the format (d+).dd
    ## return format hh:mm:ss.uuu
    def format_length(self, mutagen_length):
        t = float(mutagen_length)
        h = int(math.floor(t/3600))
        t = t % 3600
        m = int(math.floor(t/60))

        s = t % 60
        # will be ss.uuu
        s = str(s)
        s = s[:6]

        length = "%s:%s:%s" % (h, m, s)

        return length

    def save_md_to_file(self, m):
        try:
            airtime_file = mutagen.File(m['MDATA_KEY_FILEPATH'], easy=True)

            for key in m.keys() :
                if key in self.airtime2mutagen:
                    value = m[key]
                    if ((value is not None) and (len(str(value)) > 0)):
                        airtime_file[self.airtime2mutagen[key]] = str(value)
                        #self.logger.info('setting %s = %s ', key, str(value))


            airtime_file.save()
        except Exception, e:
            self.logger.error('Trying to save md')
            self.logger.error('Exception: %s', e)
            self.logger.error('Filepath %s', m['MDATA_KEY_FILEPATH'])

    def get_md_from_file(self, filepath):
        md = {}
        md5 = self.get_md5(filepath)
        md['MDATA_KEY_MD5'] = md5

        file_info = mutagen.File(filepath, easy=True)
        attrs = self.mutagen2airtime
        for key in file_info.keys() :
            if key in attrs :
                md[attrs[key]] = file_info[key][0]

        if 'MDATA_KEY_TITLE' not in md:
            #get rid of file extention from original name, name might have more than 1 '.' in it.
            original_name = os.path.basename(filepath)
            original_name = original_name.split(".")[0:-1]
            original_name = ''.join(original_name)
            md['MDATA_KEY_TITLE'] = original_name

        #incase track number is in format u'4/11'
        if 'MDATA_KEY_TRACKNUMBER' in md:
            if isinstance(md['MDATA_KEY_TRACKNUMBER'], basestring):
                md['MDATA_KEY_TRACKNUMBER'] = md['MDATA_KEY_TRACKNUMBER'].split("/")[0]

        md['MDATA_KEY_BITRATE'] = file_info.info.bitrate
        md['MDATA_KEY_SAMPLERATE'] = file_info.info.sample_rate
        md['MDATA_KEY_DURATION'] = self.format_length(file_info.info.length)
        md['MDATA_KEY_MIME'] = file_info.mime[0]

        if "mp3" in md['MDATA_KEY_MIME']:
            md['MDATA_KEY_FTYPE'] = "audioclip"
        elif "vorbis" in md['MDATA_KEY_MIME']:
            md['MDATA_KEY_FTYPE'] = "audioclip"

        #do this so object can be urlencoded properly.
        for key in md.keys():
            if(isinstance(md[key], basestring)):
                md[key] = md[key].encode('utf-8')

        return md


class AirtimeNotifier(Notifier):

    def __init__(self, watch_manager, default_proc_fun=None, read_freq=0, threshold=0, timeout=None):
        Notifier.__init__(self, watch_manager, default_proc_fun, read_freq, threshold, timeout)

        schedule_exchange = Exchange("airtime-media-monitor", "direct", durable=True, auto_delete=True)
        schedule_queue = Queue("media-monitor", exchange=schedule_exchange, key="filesystem")
        self.connection = BrokerConnection(config["rabbitmq_host"], config["rabbitmq_user"], config["rabbitmq_password"], "/")
        channel = self.connection.channel()
        consumer = Consumer(channel, schedule_queue)
        consumer.register_callback(self.handle_message)
        consumer.consume()

        self.logger = logging.getLogger('root')
        self.api_client = api_client.api_client_factory(config)
        self.md_manager = MetadataExtractor()
        self.import_processes = {}
        self.watched_folders = []

    def handle_message(self, body, message):
        # ACK the message to take it off the queue
        message.ack()

        self.logger.info("Received md from RabbitMQ: " + body)
        m =  json.loads(message.body)

        if m['event_type'] == "md_update":
            self.logger.info("AIRTIME NOTIFIER md update event")
            self.md_manager.save_md_to_file(m)
        elif m['event_type'] == "new_watch":
            self.logger.info("AIRTIME NOTIFIER add watched folder event " + m['directory'])
            #start a new process to walk through this folder and add the files to Airtime.
            p = Process(target=self.walk_newly_watched_directory, args=(m['directory'],))
            p.start()
            self.import_processes[m['directory']] = p
            #add this new folder to our list of watched folders
            self.watched_folders.append(m['directory'])

    def update_airtime(self, d):

        filepath = d['filepath']
        mode = d['mode']

        data = None
        md = {}
        md['MDATA_KEY_FILEPATH'] = filepath

        if (os.path.exists(filepath) and (mode == MODE_CREATE)):
            mutagen = self.md_manager.get_md_from_file(filepath)
            md.update(mutagen)
            data = md
        elif (os.path.exists(filepath) and (mode == MODE_MODIFY)):
            mutagen = self.md_manager.get_md_from_file(filepath)
            md.update(mutagen)
            data = md
        elif (mode == MODE_MOVED):
            mutagen = self.md_manager.get_md_from_file(filepath)
            md.update(mutagen)
            data = md
        elif (mode == MODE_DELETE):
            data = md

        if data is not None:
            self.logger.info("Updating Change to Airtime " + filepath)
            response = None
            while response is None:
                response = self.api_client.update_media_metadata(data, mode)
                time.sleep(5)

    def walk_newly_watched_directory(self, directory):

        for (path, dirs, files) in os.walk(directory):
            for filename in files:
                full_filepath = path+"/"+filename
                self.update_airtime({'filepath': full_filepath, 'mode': MODE_CREATE})


class MediaMonitor(ProcessEvent):

    def my_init(self):
        """
        Method automatically called from ProcessEvent.__init__(). Additional
        keyworded arguments passed to ProcessEvent.__init__() are then
        delegated to my_init().
        """
        self.api_client = api_client.api_client_factory(config)
        self.supported_file_formats = ['mp3', 'ogg']
        self.logger = logging.getLogger('root')
        self.temp_files = {}
        self.moved_files = {}
        self.file_events = deque()
        self.mask = pyinotify.ALL_EVENTS
        self.wm = WatchManager()
        self.md_manager = MetadataExtractor()

        schedule_exchange = Exchange("airtime-media-monitor", "direct", durable=True, auto_delete=True)
        schedule_queue = Queue("media-monitor", exchange=schedule_exchange, key="filesystem")
        connection = BrokerConnection(config["rabbitmq_host"], config["rabbitmq_user"], config["rabbitmq_password"], "/")
        channel = connection.channel()

    def watch_directory(self, directory):
        return self.wm.add_watch(directory, self.mask, rec=True, auto_add=True)

    def is_parent_directory(self, filepath, directory):
        return (directory == filepath[0:len(directory)])

    def set_needed_file_permissions(self, item, is_dir):

        try:
            omask = os.umask(0)

            uid = pwd.getpwnam('pypo')[2]
            gid = grp.getgrnam('www-data')[2]

            os.chown(item, uid, gid)

            if is_dir is True:
                os.chmod(item, 02777)
            else:
                os.chmod(item, 0666)

        except Exception, e:
            self.logger.error("Failed to change file's owner/group/permissions.")
            self.logger.error(item)
        finally:
            os.umask(omask)

    def ensure_dir(self, filepath):
        directory = os.path.dirname(filepath)

        try:
            omask = os.umask(0)
            if ((not os.path.exists(directory)) or ((os.path.exists(directory) and not os.path.isdir(directory)))):
                os.makedirs(directory, 02777)
                self.watch_directory(directory)
        finally:
            os.umask(omask)

    def move_file(self, source, dest):

        try:
            omask = os.umask(0)
            os.rename(source, dest)
        except Exception, e:
            self.logger.error("failed to move file.")
        finally:
            os.umask(omask)

    def create_unique_filename(self, filepath):

        try:
            if(os.path.exists(filepath)):
                self.logger.info("Path %s exists", filepath)
                file_dir = os.path.dirname(filepath)
                filename = os.path.basename(filepath).split(".")[0]
                #will be in the format .ext
                file_ext = os.path.splitext(filepath)[1]
                i = 1;
                while(True):
                    new_filepath = '%s/%s(%s)%s' % (file_dir, filename, i, file_ext)
                    self.logger.error("Trying %s", new_filepath)

                    if(os.path.exists(new_filepath)):
                        i = i+1;
                    else:
                        filepath = new_filepath
                        break

        except Exception, e:
             self.logger.error("Exception %s", e)

        return filepath

    def create_file_path(self, imported_filepath):

        global storage_directory

        try:
            #get rid of file extention from original name, name might have more than 1 '.' in it.
            original_name = os.path.basename(imported_filepath)
            original_name = original_name.split(".")[0:-1]
            original_name = ''.join(original_name)

            #will be in the format .ext
            file_ext = os.path.splitext(imported_filepath)[1]
            file_ext = file_ext.encode('utf-8')
            md = self.md_manager.get_md_from_file(imported_filepath)

            path_md = ['MDATA_KEY_TITLE', 'MDATA_KEY_CREATOR', 'MDATA_KEY_SOURCE', 'MDATA_KEY_TRACKNUMBER', 'MDATA_KEY_BITRATE']

            self.logger.info('Getting md')

            for m in path_md:
                if m not in md:
                    md[m] = u'unknown'.encode('utf-8')
                else:
                    #get rid of any "/" which will interfere with the filepath.
                    if isinstance(md[m], basestring):
                        md[m] = md[m].replace("/", "-")

            self.logger.info(md)

            self.logger.info('Starting filepath creation')

            filepath = None
            if (md['MDATA_KEY_TITLE'] == u'unknown'.encode('utf-8')):
                self.logger.info('unknown title')
                filepath = '%s/%s/%s/%s-%s%s' % (storage_directory.encode('utf-8'), md['MDATA_KEY_CREATOR'], md['MDATA_KEY_SOURCE'], original_name, md['MDATA_KEY_BITRATE'], file_ext)
            elif(md['MDATA_KEY_TRACKNUMBER'] == u'unknown'.encode('utf-8')):
                self.logger.info('unknown track number')
                filepath = '%s/%s/%s/%s-%s%s' % (storage_directory.encode('utf-8'), md['MDATA_KEY_CREATOR'], md['MDATA_KEY_SOURCE'], md['MDATA_KEY_TITLE'], md['MDATA_KEY_BITRATE'], file_ext)
            else:
                self.logger.info('full metadata')
                filepath = '%s/%s/%s/%s-%s-%s%s' % (storage_directory.encode('utf-8'), md['MDATA_KEY_CREATOR'], md['MDATA_KEY_SOURCE'], md['MDATA_KEY_TRACKNUMBER'], md['MDATA_KEY_TITLE'], md['MDATA_KEY_BITRATE'], file_ext)

            self.logger.info(u'Created filepath: %s', filepath)
            filepath = self.create_unique_filename(filepath)
            self.logger.info(u'Unique filepath: %s', filepath)
            self.ensure_dir(filepath)

        except Exception, e:
            self.logger.error('Exception: %s', e)

        return filepath

    def is_temp_file(self, filename):
        info = filename.split(".")

        if(info[-2] in self.supported_file_formats):
            return True
        else:
            return False

    def is_audio_file(self, filename):
        info = filename.split(".")

        if(info[-1] in self.supported_file_formats):
            return True
        else:
            return False

    def process_IN_CREATE(self, event):
        self.logger.info("%s: %s", event.maskname, event.pathname)
        if not event.dir:
            #file created is a tmp file which will be modified and then moved back to the original filename.
            if self.is_temp_file(event.name) :
                self.temp_files[event.pathname] = None
            #This is a newly imported file.
            else :
                global plupload_directory
                #files that have been added through plupload have a placeholder already put in Airtime's database.
                if not self.is_parent_directory(event.pathname, plupload_directory):
                    if self.is_audio_file(event.pathname):
                        self.set_needed_file_permissions(event.pathname, event.dir)
                        md5 = self.md_manager.get_md5(event.pathname)
                        response = self.api_client.check_media_status(md5)

                        #this file is new, md5 does not exist in Airtime.
                        if(response['airtime_status'] == 0):
                            filepath = self.create_file_path(event.pathname)
                            self.move_file(event.pathname, filepath)
                            self.file_events.append({'mode': MODE_CREATE, 'filepath': filepath})

        else:
            self.set_needed_file_permissions(event.pathname, event.dir)


    def process_IN_MODIFY(self, event):
        if not event.dir:
            self.logger.info("%s: %s", event.maskname, event.pathname)
            global plupload_directory
            #files that have been added through plupload have a placeholder already put in Airtime's database.
            if not self.is_parent_directory(event.pathname, plupload_directory):
                if self.is_audio_file(event.name) :
                    self.file_events.append({'filepath': event.pathname, 'mode': MODE_MODIFY})

    def process_IN_MOVED_FROM(self, event):
        self.logger.info("%s: %s", event.maskname, event.pathname)
        if not event.dir:
            if event.pathname in self.temp_files:
                del self.temp_files[event.pathname]
                self.temp_files[event.cookie] = event.pathname
            else:
                self.moved_files[event.cookie] = event.pathname

    def process_IN_MOVED_TO(self, event):
        self.logger.info("%s: %s", event.maskname, event.pathname)
        #if stuff dropped in stor via a UI move must change file permissions.
        self.set_needed_file_permissions(event.pathname, event.dir)
        if not event.dir:
            if event.cookie in self.temp_files:
                del self.temp_files[event.cookie]
                self.file_events.append({'filepath': event.pathname, 'mode': MODE_MODIFY})
            elif event.cookie in self.moved_files:
                old_filepath = self.moved_files[event.cookie]
                del self.moved_files[event.cookie]

                global plupload_directory
                if self.is_parent_directory(old_filepath, plupload_directory):
                    #file renamed from /tmp/plupload does not have a path in our naming scheme yet.
                    md_filepath = self.create_file_path(event.pathname)
                    #move the file a second time to its correct Airtime naming schema.
                    self.move_file(event.pathname, md_filepath)
                    self.file_events.append({'filepath': md_filepath, 'mode': MODE_MOVED})
                else:
                    self.file_events.append({'filepath': event.pathname, 'mode': MODE_MOVED})

            else:
                #TODO need to pass in if md5 exists to this file creation function, identical files will just replace current files not have a (1) etc.
                #file has been most likely dropped into stor folder from an unwatched location. (from gui, mv command not cp)
                md_filepath = self.create_file_path(event.pathname)
                self.move_file(event.pathname, md_filepath)
                self.file_events.append({'mode': MODE_CREATE, 'filepath': md_filepath})

    def process_IN_DELETE(self, event):
        if not event.dir:
            self.logger.info("%s: %s", event.maskname, event.pathname)
            self.file_events.append({'filepath': event.pathname, 'mode': MODE_DELETE})

    def process_default(self, event):
        self.logger.info("%s: %s", event.maskname, event.pathname)

    def notifier_loop_callback(self, notifier):

        for watched_directory in notifier.import_processes.keys():
            process = notifier.import_processes[watched_directory]
            if not process.is_alive():
                self.watch_directory(watched_directory)
                del notifier.import_processes[watched_directory]

        while len(self.file_events) > 0:
            self.logger.info("Processing a file event update to Airtime.")
            file_info = self.file_events.popleft()
            notifier.update_airtime(file_info)

        try:
            notifier.connection.drain_events(timeout=1)
        #avoid logging a bunch of timeout messages.
        except socket.timeout:
            pass
        except Exception, e:
            self.logger.info("%s", e)

if __name__ == '__main__':

    try:
        logger = logging.getLogger('root')
        mm = MediaMonitor()

        response = None
        while response is None:
            response = mm.api_client.setup_media_monitor()
            time.sleep(5)

        storage_directory = response["stor"]
        plupload_directory = response["plupload"]

        wdd = mm.watch_directory(storage_directory)
        logger.info("Added watch to %s", storage_directory)
        logger.info("wdd result %s", wdd[storage_directory])
        wdd = mm.watch_directory(plupload_directory)
        logger.info("Added watch to %s", plupload_directory)
        logger.info("wdd result %s", wdd[plupload_directory])

        notifier = AirtimeNotifier(mm.wm, mm, read_freq=int(config["check_filesystem_events"]), timeout=1)
        notifier.coalesce_events()

        #notifier.loop(callback=mm.notifier_loop_callback)

        while True:
            if(notifier.check_events(1)):
                notifier.read_events()
                notifier.process_events()
            mm.notifier_loop_callback(notifier)

    except KeyboardInterrupt:
        notifier.stop()
    except Exception, e:
        logger.error('Exception: %s', e)
