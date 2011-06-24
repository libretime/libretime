import json
import time
import os
import logging

from multiprocessing import Process, Lock, Queue as mpQueue

# For RabbitMQ
from kombu.connection import BrokerConnection
from kombu.messaging import Exchange, Queue, Consumer, Producer

import pyinotify
from pyinotify import WatchManager, Notifier, ProcessEvent

from api_clients import api_client
from airtimemetadata import AirtimeMetadata

class AirtimeNotifier(Notifier):

    def __init__(self, watch_manager, default_proc_fun=None, read_freq=0, threshold=0, timeout=None, airtime_config=None):
        Notifier.__init__(self, watch_manager, default_proc_fun, read_freq, threshold, timeout)

        self.logger = logging.getLogger()
        self.config = airtime_config
        self.api_client = api_client.api_client_factory(self.config.cfg)
        self.md_manager = AirtimeMetadata()
        self.import_processes = {}
        self.watched_folders = []

        schedule_exchange = Exchange("airtime-media-monitor", "direct", durable=True, auto_delete=True)
        schedule_queue = Queue("media-monitor", exchange=schedule_exchange, key="filesystem")
        self.connection = BrokerConnection(self.config.cfg["rabbitmq_host"], self.config.cfg["rabbitmq_user"], self.config.cfg["rabbitmq_password"], "/")
        channel = self.connection.channel()
        consumer = Consumer(channel, schedule_queue)
        consumer.register_callback(self.handle_message)
        consumer.consume()

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

        elif m['event_type'] == "remove_watch":
            watched_directory = m['directory'].encode('utf-8')

            mm = self.proc_fun()
            wd = mm.wm.get_wd(watched_directory)
            self.logger.info("Removing watch on: %s wd %s", watched_directory, wd)
            mm.wm.rm_watch(wd, rec=True)

        elif m['event_type'] == "change_stor":
            storage_directory = self.config.storage_directory
            new_storage_directory = m['directory'].encode('utf-8')

            mm = self.proc_fun()

            wd = mm.wm.get_wd(storage_directory)
            self.logger.info("Removing watch on: %s wd %s", storage_directory, wd)
            mm.wm.rm_watch(wd, rec=True)

            mm.set_needed_file_permissions(new_storage_directory, True)
            mm.move_file(storage_directory, new_storage_directory)
            self.config.storage_directory = new_storage_directory

            mm.watch_directory(new_storage_directory)


    def update_airtime(self, d):

        filepath = d['filepath']
        mode = d['mode']

        md = {}
        md['MDATA_KEY_FILEPATH'] = filepath

        if 'data' in d:
            file_md = d['data']
            md.update(file_md)
        else:
            file_md = None
            data = None


        if (os.path.exists(filepath) and (mode == self.config.MODE_CREATE)):
            if file_md is None:
                mutagen = self.md_manager.get_md_from_file(filepath)
                md.update(mutagen)

            if d['is_recorded_show']:
                self.api_client.update_media_metadata(md, mode, True)
            else:
                self.api_client.update_media_metadata(md, mode)

        elif (os.path.exists(filepath) and (mode == self.config.MODE_MODIFY)):
            mutagen = self.md_manager.get_md_from_file(filepath)
            md.update(mutagen)
            self.api_client.update_media_metadata(md, mode)

        elif (mode == self.config.MODE_MOVED):
            md['MDATA_KEY_MD5'] = self.md_manager.get_md5(filepath)
            self.api_client.update_media_metadata(md, mode)

        elif (mode == self.config.MODE_DELETE):
            self.api_client.update_media_metadata(md, mode)


    def process_file_events(self, queue):

        while True:
            event = queue.get()
            self.logger.info("received event %s", event);
            self.update_airtime(event)

    def walk_newly_watched_directory(self, directory):

        mm = self.proc_fun()

        for (path, dirs, files) in os.walk(directory):
            for filename in files:
                full_filepath = path+"/"+filename

                if mm.is_audio_file(full_filepath):
                    self.update_airtime({'filepath': full_filepath, 'mode': self.config.MODE_CREATE, 'is_recorded_show': False})

