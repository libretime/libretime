# -*- coding: utf-8 -*-

import json
import time
import os
import logging
import traceback

# For RabbitMQ
from kombu.connection import BrokerConnection
from kombu.messaging import Exchange, Queue, Consumer

import pyinotify
from pyinotify import Notifier

from airtimemetadata import AirtimeMetadata

class AirtimeNotifier(Notifier):

    def __init__(self, watch_manager, default_proc_fun=None, read_freq=0, threshold=0, timeout=None, airtime_config=None, api_client=None, bootstrap=None, mmc=None):
        Notifier.__init__(self, watch_manager, default_proc_fun, read_freq, threshold, timeout)

        self.logger = logging.getLogger()
        self.config = airtime_config
        self.api_client = api_client
        self.bootstrap = bootstrap
        self.md_manager = AirtimeMetadata()
        self.import_processes = {}
        self.watched_folders = []
        self.mmc = mmc
        self.wm = watch_manager
        self.mask = pyinotify.ALL_EVENTS

        while not self.init_rabbit_mq():
            self.logger.error("Error connecting to RabbitMQ Server. Trying again in few seconds")
            time.sleep(5)

    def init_rabbit_mq(self):
        """
        This function will attempt to connect to RabbitMQ Server and if successful
        return 'True'. Returns 'False' otherwise.
        """
 
        self.logger.info("Initializing RabbitMQ stuff")
        try:
            schedule_exchange = Exchange("airtime-media-monitor", "direct", durable=True, auto_delete=True)
            schedule_queue = Queue("media-monitor", exchange=schedule_exchange, key="filesystem")
            self.connection = BrokerConnection(self.config.cfg["rabbitmq"]["rabbitmq_host"], self.config.cfg["rabbitmq"]["rabbitmq_user"], self.config.cfg["rabbitmq"]["rabbitmq_password"], self.config.cfg["rabbitmq"]["rabbitmq_vhost"])
            channel = self.connection.channel()
            consumer = Consumer(channel, schedule_queue)
            consumer.register_callback(self.handle_message)
            consumer.consume()
        except Exception, e:
            self.logger.error(e)
            return False

        return True

    def handle_message(self, body, message):
        """
        Messages received from RabbitMQ are handled here. These messages
        instruct media-monitor of events such as a new directory being watched,
        file metadata has been changed, or any other changes to the config of
        media-monitor via the web UI.
        """
        # ACK the message to take it off the queue
        message.ack()

        self.logger.info("Received md from RabbitMQ: " + body)
        m = json.loads(message.body)

        if m['event_type'] == "md_update":
            self.logger.info("AIRTIME NOTIFIER md update event")
            self.md_manager.save_md_to_file(m)

        elif m['event_type'] == "new_watch":
            self.logger.info("AIRTIME NOTIFIER add watched folder event " + m['directory'])
            self.walk_newly_watched_directory(m['directory'])

            self.watch_directory(m['directory'])

        elif m['event_type'] == "remove_watch":
            watched_directory = m['directory']

            mm = self.proc_fun()
            wd = mm.wm.get_wd(watched_directory)
            self.logger.info("Removing watch on: %s wd %s", watched_directory, wd)
            mm.wm.rm_watch(wd, rec=True)

        elif m['event_type'] == "rescan_watch":
            self.bootstrap.sync_database_to_filesystem(str(m['id']), m['directory'])

        elif m['event_type'] == "change_stor":
            storage_directory = self.config.storage_directory
            new_storage_directory = m['directory']
            new_storage_directory_id = str(m['dir_id'])

            mm = self.proc_fun()

            wd = mm.wm.get_wd(storage_directory)
            self.logger.info("Removing watch on: %s wd %s", storage_directory, wd)
            mm.wm.rm_watch(wd, rec=True)

            self.bootstrap.sync_database_to_filesystem(new_storage_directory_id, new_storage_directory)

            self.config.storage_directory = os.path.normpath(new_storage_directory)
            self.config.imported_directory = os.path.normpath(os.path.join(new_storage_directory, '/imported'))
            self.config.organize_directory = os.path.normpath(os.path.join(new_storage_directory, '/organize'))

            for directory in [self.config.storage_directory, self.config.imported_directory, self.config.organize_directory]:
                self.mmc.ensure_is_dir(directory)
                self.mmc.is_readable(directory, True)

            self.watch_directory(new_storage_directory)
        elif m['event_type'] == "file_delete":
            filepath = m['filepath']

            mm = self.proc_fun()
            self.logger.info("Adding file to ignore: %s ", filepath)
            mm.add_filepath_to_ignore(filepath)

            if m['delete']:
                self.logger.info("Deleting file: %s ", filepath)
                try:
                    os.unlink(filepath)
                except Exception, e:
                    self.logger.error('Exception: %s', e)
                    self.logger.error("traceback: %s", traceback.format_exc())


    def update_airtime(self, event):
        """
        Update airtime with information about files discovered in our
        watched directories.
        event: a dict() object with the following attributes:
        -filepath
        -mode
        -data
        -is_recorded_show
        """
        try:
            self.logger.info("updating filepath: %s ", event['filepath'])
            filepath = event['filepath']
            mode = event['mode']

            md = {}
            md['MDATA_KEY_FILEPATH'] = os.path.normpath(filepath)

            if 'data' in event:
                file_md = event['data']
                md.update(file_md)
            else:
                file_md = None

            if (os.path.exists(filepath) and (mode == self.config.MODE_CREATE)):
                if file_md is None:
                    mutagen = self.md_manager.get_md_from_file(filepath)
                    if mutagen is None:
                        return
                    md.update(mutagen)

                if 'is_recorded_show' in event and event['is_recorded_show']:
                    self.api_client.update_media_metadata(md, mode, True)
                else:
                    self.api_client.update_media_metadata(md, mode)

            elif (os.path.exists(filepath) and (mode == self.config.MODE_MODIFY)):
                mutagen = self.md_manager.get_md_from_file(filepath)
                if mutagen is None:
                    return
                md.update(mutagen)
                if 'is_recorded_show' in event and event['is_recorded_show']:
                    self.api_client.update_media_metadata(md, mode, True)
                else:
                    self.api_client.update_media_metadata(md, mode)
            elif (mode == self.config.MODE_MOVED):
                md['MDATA_KEY_MD5'] = self.md_manager.get_md5(filepath)
                if 'is_recorded_show' in event and event['is_recorded_show']:
                    self.api_client.update_media_metadata(md, mode, True)
                else:
                    self.api_client.update_media_metadata(md, mode)
            elif (mode == self.config.MODE_DELETE):
                self.api_client.update_media_metadata(md, mode)

            elif (mode == self.config.MODE_DELETE_DIR):
                self.api_client.update_media_metadata(md, mode)

        except Exception, e:
            self.logger.error("failed updating filepath: %s ", event['filepath'])
            self.logger.error('Exception: %s', e)
            self.logger.error('Traceback: %s', traceback.format_exc())

    #define which directories the pyinotify WatchManager should watch.
    def watch_directory(self, directory):
        return self.wm.add_watch(directory, self.mask, rec=True, auto_add=True)

    def walk_newly_watched_directory(self, directory):

        mm = self.proc_fun()

        self.mmc.is_readable(directory, True)
        for (path, dirs, files) in os.walk(directory):
            for filename in files:
                full_filepath = os.path.join(path, filename)

                if self.mmc.is_audio_file(full_filepath):
                    if self.mmc.is_readable(full_filepath, False):
                        self.logger.info("importing %s", full_filepath)
                        event = {'filepath': full_filepath, 'mode': self.config.MODE_CREATE, 'is_recorded_show': False}
                        mm.multi_queue.put(event)
                    else:
                        self.logger.warn("file '%s' has does not have sufficient read permissions. Ignoring.", full_filepath)

