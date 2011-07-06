import json
import time
import os
import logging

# For RabbitMQ
from kombu.connection import BrokerConnection
from kombu.messaging import Exchange, Queue, Consumer, Producer

import pyinotify
from pyinotify import Notifier

#from api_clients import api_client
from airtimemetadata import AirtimeMetadata

class AirtimeNotifier(Notifier):

    def __init__(self, watch_manager, default_proc_fun=None, read_freq=0, threshold=0, timeout=None, airtime_config=None, api_client=None, bootstrap=None):
        Notifier.__init__(self, watch_manager, default_proc_fun, read_freq, threshold, timeout)

        self.logger = logging.getLogger()
        self.config = airtime_config
        self.api_client = api_client
        self.bootstrap = bootstrap
        self.md_manager = AirtimeMetadata()
        self.import_processes = {}
        self.watched_folders = []
       

        while not self.init_rabbit_mq():
            self.logger.error("Error connecting to RabbitMQ Server. Trying again in few seconds")
            time.sleep(5)

    def init_rabbit_mq(self):
        self.logger.info("Initializing RabbitMQ stuff")
        try:
            schedule_exchange = Exchange("airtime-media-monitor", "direct", durable=True, auto_delete=True)
            schedule_queue = Queue("media-monitor", exchange=schedule_exchange, key="filesystem")
            self.connection = BrokerConnection(self.config.cfg["rabbitmq_host"], self.config.cfg["rabbitmq_user"], self.config.cfg["rabbitmq_password"], "/")
            channel = self.connection.channel()
            consumer = Consumer(channel, schedule_queue)
            consumer.register_callback(self.handle_message)
            consumer.consume()
        except Exception, e:
            self.logger.error(e)
            return False

        return True

    """
    Messages received from RabbitMQ are handled here. These messages
    instruct media-monitor of events such as a new directory being watched,
    file metadata has been changed, or any other changes to the config of
    media-monitor via the web UI.
    """
    def handle_message(self, body, message):
        # ACK the message to take it off the queue
        message.ack()

        self.logger.info("Received md from RabbitMQ: " + body)
        m =  json.loads(message.body)

        if m['event_type'] == "md_update":
            self.logger.info("AIRTIME NOTIFIER md update event")
            self.md_manager.save_md_to_file(m)

        elif m['event_type'] == "new_watch":
            mm = self.proc_fun()
            if mm.has_correct_permissions(m['directory']):
                self.logger.info("AIRTIME NOTIFIER add watched folder event " + m['directory'])
                self.walk_newly_watched_directory(m['directory'])

                mm.watch_directory(m['directory'])
            else:
                self.logger.warn("filepath '%s' has does not have sufficient read permissions. Ignoring.", full_filepath)

        elif m['event_type'] == "remove_watch":
            watched_directory = m['directory'].encode('utf-8')

            mm = self.proc_fun()
            wd = mm.wm.get_wd(watched_directory)
            self.logger.info("Removing watch on: %s wd %s", watched_directory, wd)
            mm.wm.rm_watch(wd, rec=True)

        elif m['event_type'] == "change_stor":
            storage_directory = self.config.storage_directory
            new_storage_directory = m['directory'].encode('utf-8')
            new_storage_directory_id = str(m['dir_id']).encode('utf-8')

            mm = self.proc_fun()

            wd = mm.wm.get_wd(storage_directory)
            self.logger.info("Removing watch on: %s wd %s", storage_directory, wd)
            mm.wm.rm_watch(wd, rec=True)

            mm.set_needed_file_permissions(new_storage_directory, True)

            self.bootstrap.check_for_diff(new_storage_directory_id, new_storage_directory)
            
            self.config.storage_directory = new_storage_directory
            self.config.imported_directory = new_storage_directory + '/imported'
            self.config.organize_directory = new_storage_directory + '/organize'
            
            mm.ensure_is_dir(self.config.storage_directory)
            mm.ensure_is_dir(self.config.imported_directory)
            mm.ensure_is_dir(self.config.organize_directory)

            mm.watch_directory(new_storage_directory)
            
            """
            old_storage_contents = os.listdir(storage_directory)
            for item in old_storage_contents:
                fp = "%s/%s" % (storage_directory, item)
                nfp = "%s/%s" % (new_storage_directory, item)
                self.logger.info("Moving %s to %s", fp, nfp)
                mm.move_file(fp, nfp)
            
            """
            
            
            
        elif m['event_type'] == "file_delete":
            self.logger.info("Deleting file: %s ", m['filepath'])
            mm = self.proc_fun()
            mm.add_filepath_to_ignore(m['filepath'])
            os.unlink(m['filepath'])


    #update airtime with information about files discovered in our
    #watched directories. Pass in a dict() object with the following 
    #attributes:
    # -filepath
    # -mode
    # -data
    # -is_recorded_show
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
                if mutagen is None:
                    return
                md.update(mutagen)

            if d['is_recorded_show']:
                self.api_client.update_media_metadata(md, mode, True)
            else:
                self.api_client.update_media_metadata(md, mode)

        elif (os.path.exists(filepath) and (mode == self.config.MODE_MODIFY)):
            mutagen = self.md_manager.get_md_from_file(filepath)
            if mutagen is None:
                return
            md.update(mutagen)
            self.api_client.update_media_metadata(md, mode)

        elif (mode == self.config.MODE_MOVED):
            md['MDATA_KEY_MD5'] = self.md_manager.get_md5(filepath)
            self.api_client.update_media_metadata(md, mode)

        elif (mode == self.config.MODE_DELETE):
            self.api_client.update_media_metadata(md, mode)


    def walk_newly_watched_directory(self, directory):

        mm = self.proc_fun()

        for (path, dirs, files) in os.walk(directory):
            for filename in files:
                full_filepath = path+"/"+filename

                if mm.is_audio_file(full_filepath):
                    if mm.has_correct_permissions(full_filepath):
                        self.logger.info("importing %s", full_filepath)
                        event = {'filepath': full_filepath, 'mode': self.config.MODE_CREATE, 'is_recorded_show': False}
                        mm.multi_queue.put(event)
                    else:
                        self.logger.warn("file '%s' has does not have sufficient read permissions. Ignoring.", full_filepath)

