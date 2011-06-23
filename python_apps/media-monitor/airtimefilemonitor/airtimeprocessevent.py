import os
import socket
import grp
import pwd
import logging

from multiprocessing import Process, Lock, Queue as mpQueue

import pyinotify
from pyinotify import WatchManager, Notifier, ProcessEvent

# For RabbitMQ
from kombu.connection import BrokerConnection
from kombu.messaging import Exchange, Queue, Consumer, Producer

from airtimemetadata import AirtimeMetadata
from airtimefilemonitor.mediaconfig import AirtimeMediaConfig

class AirtimeProcessEvent(ProcessEvent):

    def my_init(self, airtime_config=None):
        """
        Method automatically called from ProcessEvent.__init__(). Additional
        keyworded arguments passed to ProcessEvent.__init__() are then
        delegated to my_init().
        """

        self.logger = logging.getLogger()
        self.config = airtime_config

        self.supported_file_formats = ['mp3', 'ogg']
        self.temp_files = {}
        self.moved_files = {}
        self.file_events = mpQueue()
        self.mask = pyinotify.ALL_EVENTS
        self.wm = WatchManager()
        self.md_manager = AirtimeMetadata()

        schedule_exchange = Exchange("airtime-media-monitor", "direct", durable=True, auto_delete=True)
        schedule_queue = Queue("media-monitor", exchange=schedule_exchange, key="filesystem")
        connection = BrokerConnection(self.config.cfg["rabbitmq_host"], self.config.cfg["rabbitmq_user"], self.config.cfg["rabbitmq_password"], "/")
        channel = connection.channel()

    def watch_directory(self, directory):
        return self.wm.add_watch(directory, self.mask, rec=True, auto_add=True)

    def is_parent_directory(self, filepath, directory):
        return (directory == filepath[0:len(directory)])

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

    #checks if path exists already in stor. If the path exists and the md5s are the same just moves file to same path anyway to avoid duplicates in the system.
    def create_unique_filename(self, filepath, old_filepath):

        try:
            if(os.path.exists(filepath)):
                self.logger.info("Path %s exists", filepath)

                self.logger.info("Checking if md5s are the same.")
                md5_fp = self.md_manager.get_md5(filepath)
                md5_ofp = self.md_manager.get_md5(old_filepath)

                if(md5_fp == md5_ofp):
                    self.logger.info("Md5s are the same, moving to same filepath.")
                    return filepath

                self.logger.info("Md5s aren't the same, appending to filepath.")
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

        storage_directory = self.config.storage_directory

        is_recorded_show = False

        try:
            #will be in the format .ext
            file_ext = os.path.splitext(imported_filepath)[1]
            file_ext = file_ext.encode('utf-8')

            orig_md = self.md_manager.get_md_from_file(imported_filepath)
            path_md = ['MDATA_KEY_TITLE', 'MDATA_KEY_CREATOR', 'MDATA_KEY_SOURCE', 'MDATA_KEY_TRACKNUMBER', 'MDATA_KEY_BITRATE']

            md = {}
            for m in path_md:
                if m not in orig_md:
                    md[m] = u'unknown'.encode('utf-8')
                else:
                    #get rid of any "/" which will interfere with the filepath.
                    if isinstance(orig_md[m], basestring):
                        md[m] = orig_md[m].replace("/", "-")
                    else:
                        md[m] = orig_md[m]

            filepath = None
            #file is recorded by Airtime
            #/srv/airtime/stor/recorded/year/month/year-month-day-time-showname-bitrate.ext
            if(md['MDATA_KEY_CREATOR'] == "AIRTIMERECORDERSOURCEFABRIC".encode('utf-8')):
                #yyyy-mm-dd-hh-MM-ss
                y = orig_md['MDATA_KEY_YEAR'].split("-")
                filepath = '%s/%s/%s/%s/%s-%s-%s%s' % (storage_directory, "recorded".encode('utf-8'), y[0], y[1], orig_md['MDATA_KEY_YEAR'], md['MDATA_KEY_TITLE'], md['MDATA_KEY_BITRATE'], file_ext)
                is_recorded_show = True
            elif(md['MDATA_KEY_TRACKNUMBER'] == u'unknown'.encode('utf-8')):
                filepath = '%s/%s/%s/%s/%s-%s%s' % (storage_directory, "imported".encode('utf-8'), md['MDATA_KEY_CREATOR'], md['MDATA_KEY_SOURCE'], md['MDATA_KEY_TITLE'], md['MDATA_KEY_BITRATE'], file_ext)
            else:
                filepath = '%s/%s/%s/%s/%s-%s-%s%s' % (storage_directory, "imported".encode('utf-8'), md['MDATA_KEY_CREATOR'], md['MDATA_KEY_SOURCE'], md['MDATA_KEY_TRACKNUMBER'], md['MDATA_KEY_TITLE'], md['MDATA_KEY_BITRATE'], file_ext)

            self.logger.info('Created filepath: %s', filepath)
            filepath = self.create_unique_filename(filepath, imported_filepath)
            self.logger.info('Unique filepath: %s', filepath)
            self.ensure_dir(filepath)

        except Exception, e:
            self.logger.error('Exception: %s', e)

        return filepath, orig_md, is_recorded_show

    def process_IN_CREATE(self, event):

        self.logger.info("%s: %s", event.maskname, event.pathname)
        storage_directory = self.config.storage_directory

        if not event.dir:
            #file created is a tmp file which will be modified and then moved back to the original filename.
            if self.is_temp_file(event.name) :
                self.temp_files[event.pathname] = None
            #This is a newly imported file.
            else :
                if self.is_audio_file(event.pathname):
                    if self.is_parent_directory(event.pathname, storage_directory):
                        self.set_needed_file_permissions(event.pathname, event.dir)
                        filepath, file_md, is_recorded_show = self.create_file_path(event.pathname)
                        self.move_file(event.pathname, filepath)
                        self.file_events.put({'mode': self.config.MODE_CREATE, 'filepath': filepath, 'data': file_md, 'is_recorded_show': is_recorded_show})
                    else:
                        self.file_events.put({'mode': self.config.MODE_CREATE, 'filepath': event.pathname, 'is_recorded_show': False})

        else:
            if self.is_parent_directory(event.pathname, storage_directory):
                self.set_needed_file_permissions(event.pathname, event.dir)


    def process_IN_MODIFY(self, event):
        if not event.dir:
            self.logger.info("%s: %s", event.maskname, event.pathname)
            if self.is_audio_file(event.name) :
                self.file_events.put({'filepath': event.pathname, 'mode': self.config.MODE_MODIFY})

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
                self.file_events.put({'filepath': event.pathname, 'mode': self.config.MODE_MODIFY})
            elif event.cookie in self.moved_files:
                old_filepath = self.moved_files[event.cookie]
                del self.moved_files[event.cookie]
                self.file_events.put({'filepath': event.pathname, 'mode': self.config.MODE_MOVED})
            else:
                # show dragged from unwatched folder into a watched folder.
                storage_directory = self.config.storage_directory
                if self.is_parent_directory(event.pathname, storage_directory):
                    filepath, file_md, is_recorded_show = self.create_file_path(event.pathname)
                    self.move_file(event.pathname, filepath)
                    self.file_events.put({'mode': self.config.MODE_CREATE, 'filepath': filepath, 'data': file_md, 'is_recorded_show': False})
                else:
                    self.file_events.put({'mode': self.config.MODE_CREATE, 'filepath': event.pathname, 'is_recorded_show': False})

    def process_IN_DELETE(self, event):
        self.logger.info("%s: %s", event.maskname, event.pathname)
        if not event.dir:
            self.file_events.put({'filepath': event.pathname, 'mode': self.config.MODE_DELETE})

    def process_default(self, event):
        #self.logger.info("%s: %s", event.maskname, event.pathname)
        pass

    def notifier_loop_callback(self, notifier):

        #put a watch on any fully imported watched directories.
        for watched_directory in notifier.import_processes.keys():
            process = notifier.import_processes[watched_directory]
            if not process.is_alive():
                self.watch_directory(watched_directory)
                del notifier.import_processes[watched_directory]

        #check for any events recieved from Airtime.
        try:
            notifier.connection.drain_events(timeout=0.1)
        #avoid logging a bunch of timeout messages.
        except socket.timeout:
            pass
        except Exception, e:
            self.logger.info("%s", e)

