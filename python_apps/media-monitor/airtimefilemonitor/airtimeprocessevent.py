import os
import socket
import grp
import pwd
import logging
import time

from subprocess import Popen, PIPE

import pyinotify
from pyinotify import ProcessEvent

# For RabbitMQ
from kombu.connection import BrokerConnection
from kombu.messaging import Exchange, Queue, Consumer, Producer

from airtimemetadata import AirtimeMetadata
from airtimefilemonitor.mediaconfig import AirtimeMediaConfig

class AirtimeProcessEvent(ProcessEvent):

    def my_init(self, queue, airtime_config=None, wm=None):
        """
        Method automatically called from ProcessEvent.__init__(). Additional
        keyworded arguments passed to ProcessEvent.__init__() are then
        delegated to my_init().
        """

        self.logger = logging.getLogger()
        self.config = airtime_config

        #put the file path into this dict if we want to ignore certain 
        #events. For example, when deleting a file from the web ui, we
        #are going to delete it on the server side, so media-monitor doesn't
        #need to contact the server and tell it to delete again.
        self.ignore_event = set()
        
        self.supported_file_formats = ['mp3', 'ogg']
        self.temp_files = {}
        self.renamed_files = {}
        """
        self.moved_files = {}
        self.gui_replaced = {}
        """
        self.cookies_IN_MOVED_FROM = {}
        self.file_events = []
        self.multi_queue = queue
        self.mask = pyinotify.ALL_EVENTS
        self.wm = wm
        self.md_manager = AirtimeMetadata()
        
    def add_filepath_to_ignore(self, filepath):
        self.ignore_event.add(filepath)

    #define which directories the pyinotify WatchManager should watch.
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
        
    #file needs to be readable by all users, and directories
    #up to this file needs to be readable AND executable by all
    #users.
    def has_correct_permissions(self, filepath):
        st = os.stat(filepath)
        return bool(st.st_mode & stat.S_IROTH)

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

            
    #checks if path is a directory, and if it doesnt exist, then creates it.
    #Otherwise prints error to log file.
    def ensure_is_dir(self, filepath):
        directory = os.path.dirname(filepath)

        try:
            omask = os.umask(0)
            if not os.path.exists(directory):
                os.makedirs(directory, 02777)
                #self.watch_directory(directory)
            elif not os.path.isdir(directory):
                #path exists but it is a file not a directory!
                self.logger.error("path %s exists, but it is not a directory!!!")
        finally:
            os.umask(omask)

    def move_file(self, source, dest):

        try:
            omask = os.umask(0)
            os.rename(source, dest)
        except Exception, e:
            self.logger.error("failed to move file. %s", e)
        finally:
            os.umask(omask)

    #checks if path exists already in stor. If the path exists and the md5s are the 
    #same just overwrite.
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

    #create path in /srv/airtime/stor/imported/[song-metadata]
    def create_file_path(self, original_path, orig_md):

        storage_directory = self.config.storage_directory

        is_recorded_show = False

        try:
            #will be in the format .ext
            file_ext = os.path.splitext(original_path)[1]
            file_ext = file_ext.encode('utf-8')

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

            if 'MDATA_KEY_TRACKNUMBER' in orig_md:
                #make sure all track numbers are at least 2 digits long in the filepath.
                md['MDATA_KEY_TRACKNUMBER'] = "%02d" % (int(md['MDATA_KEY_TRACKNUMBER']))

            #format bitrate as 128kbps
            md['MDATA_KEY_BITRATE'] = str(md['MDATA_KEY_BITRATE']/1000)+"kbps"

            filepath = None
            #file is recorded by Airtime
            #/srv/airtime/stor/recorded/year/month/year-month-day-time-showname-bitrate.ext
            if(md['MDATA_KEY_CREATOR'] == "AIRTIMERECORDERSOURCEFABRIC".encode('utf-8')):
                #yyyy-mm-dd-hh-MM-ss
                y = orig_md['MDATA_KEY_YEAR'].split("-")
                filepath = '%s/%s/%s/%s/%s-%s-%s%s' % (storage_directory, "recorded".encode('utf-8'), y[0], y[1], orig_md['MDATA_KEY_YEAR'], md['MDATA_KEY_TITLE'], md['MDATA_KEY_BITRATE'], file_ext)
            elif(md['MDATA_KEY_TRACKNUMBER'] == u'unknown'.encode('utf-8')):
                filepath = '%s/%s/%s/%s/%s-%s%s' % (storage_directory, "imported".encode('utf-8'), md['MDATA_KEY_CREATOR'], md['MDATA_KEY_SOURCE'], md['MDATA_KEY_TITLE'], md['MDATA_KEY_BITRATE'], file_ext)
            else:
                filepath = '%s/%s/%s/%s/%s-%s-%s%s' % (storage_directory, "imported".encode('utf-8'), md['MDATA_KEY_CREATOR'], md['MDATA_KEY_SOURCE'], md['MDATA_KEY_TRACKNUMBER'], md['MDATA_KEY_TITLE'], md['MDATA_KEY_BITRATE'], file_ext)         

            filepath = self.create_unique_filename(filepath, original_path)
            self.logger.info('Unique filepath: %s', filepath)
            self.ensure_is_dir(filepath)

        except Exception, e:
            self.logger.error('Exception: %s', e)

        return filepath

    #event.dir: True if the event was raised against a directory.
    #event.name: filename
    #event.pathname: pathname (str): Concatenation of 'path' and 'name'.
    def process_IN_CREATE(self, event):
        self.handle_created_file(event.dir, event.name, event.pathname)
                
    def handle_created_file(self, dir, name, pathname):
        if not dir:
            self.logger.debug("PROCESS_IN_CREATE: %s, name: %s, pathname: %s ", dir, name, pathname)
            #event is because of a created file
            if self.is_temp_file(name) :
                #file created is a tmp file which will be modified and then moved back to the original filename.
                self.temp_files[pathname] = None
            elif self.is_audio_file(pathname):
                if self.is_parent_directory(pathname, self.config.organize_directory):
                    #file was created in /srv/airtime/stor/organize. Need to process and move
                    #to /srv/airtime/stor/imported
                    self.organize_new_file(pathname)
                else:
                    self.set_needed_file_permissions(pathname, dir)
                    self.file_events.append({'mode': self.config.MODE_CREATE, 'filepath': pathname, 'is_recorded_show': False})

        else:
            #event is because of a created directory
            if self.is_parent_directory(pathname, self.config.storage_directory):
                self.set_needed_file_permissions(pathname, dir)
                
                
    def organize_new_file(self, pathname):
        self.logger.info("Processing new file: %s", pathname)
        file_md = self.md_manager.get_md_from_file(pathname)

        if file_md is not None:
            #is_recorded_show = 'MDATA_KEY_CREATOR' in file_md and \
            #    file_md['MDATA_KEY_CREATOR'] == "AIRTIMERECORDERSOURCEFABRIC".encode('utf-8')
            filepath = self.create_file_path(pathname, file_md)
            
            self.logger.debug("Moving from %s to %s", pathname, filepath)
            self.move_file(pathname, filepath)
        else:
            self.logger.warn("File %s, has invalid metadata", pathname)           
                
    def process_IN_MODIFY(self, event):
        self.logger.info("process_IN_MODIFY: %s", event)
        self.handle_modified_file(event.dir, event.pathname, event.name)
                
    def handle_modified_file(self, dir, pathname, name):
        if not dir and self.is_parent_directory(pathname, self.config.organize_directory):
            self.logger.info("Modified: %s", pathname)
            if self.is_audio_file(name):
                self.file_events.append({'filepath': pathname, 'mode': self.config.MODE_MODIFY})

    #if a file is moved somewhere, this callback is run. With details about
    #where the file is being moved from. The corresponding process_IN_MOVED_TO
    #callback is only called if the destination of the file is also in a watched
    #directory.
    def process_IN_MOVED_FROM(self, event):
        self.logger.info("process_IN_MOVED_FROM: %s", event)
        if not event.dir:
            if not self.is_parent_directory(event.pathname, self.config.organize_directory):
                #we don't care about moved_from events from the organize dir.
                if self.is_audio_file(event.name):
                    self.cookies_IN_MOVED_FROM[event.cookie] = (event, time.time())

    def process_IN_MOVED_TO(self, event):
        self.logger.info("process_IN_MOVED_TO: %s", event)
        #if stuff dropped in stor via a UI move must change file permissions.
        self.set_needed_file_permissions(event.pathname, event.dir)
        if not event.dir:
            if self.is_audio_file(event.name):
                if event.cookie in self.cookies_IN_MOVED_FROM:
                    #files original location was also in a watched directory
                    del self.cookies_IN_MOVED_FROM[event.cookie]
                    if self.is_parent_directory(event.pathname, self.config.organize_directory):
                        self.organize_new_file(event.pathname)
                    else:
                        self.file_events.append({'filepath': event.pathname, 'mode': self.config.MODE_MOVED})
                else:
                    if self.is_parent_directory(event.pathname, self.config.organize_directory):
                        self.organize_new_file(event.pathname)
                    else:
                        #show dragged from unwatched folder into a watched folder. Do not "organize". 
                        self.file_events.append({'mode': self.config.MODE_CREATE, 'filepath': event.pathname, 'is_recorded_show': False})

    def process_IN_DELETE(self, event):
        self.logger.info("process_IN_DELETE: %s", event)
        self.handle_removed_file(event.dir, event.pathname)
            
    def handle_removed_file(self, dir, pathname):
        self.logger.info("Deleting %s", pathname)
        if not dir:
            if self.is_audio_file(pathname):
                if pathname in self.ignore_event:
                    self.ignore_event.remove(pathname)
                elif not self.is_parent_directory(pathname, self.config.organize_directory):
                    #we don't care if a file was deleted from the organize directory.
                    self.file_events.append({'filepath': pathname, 'mode': self.config.MODE_DELETE})
    

    def process_default(self, event):
        #self.logger.info("PROCESS_DEFAULT: %s", event)
        pass           

    def execCommandAndReturnStdOut(self, command):
        p = Popen(command, shell=True, stdout=PIPE)
        stdout = p.communicate()[0]
        if p.returncode != 0:
            self.logger.warn("command \n%s\n return with a non-zero return value", command)
        return stdout
            
    def write_file(self, file, string):
        f = open(file, 'w')
        f.write(string)
        f.close()

    def notifier_loop_callback(self, notifier):
        if len(self.file_events) > 0:
            for event in self.file_events:
                self.multi_queue.put(event)

            self.file_events = []
            
        #use items() because we are going to be modifying this
        #dictionary while iterating over it.
        for k, pair in self.cookies_IN_MOVED_FROM.items():
            event = pair[0]
            timestamp = pair[1]
            
            timestamp_now = time.time()
            
            if timestamp_now - timestamp > 5:
                #in_moved_from event didn't have a corresponding
                #in_moved_to event in the last 5 seconds. 
                #This means the file was moved to outside of the 
                #watched directories. Let's handle this by deleting 
                #it from the Airtime directory.
                del self.cookies_IN_MOVED_FROM[k]
                self.handle_removed_file(False, event.pathname)
        

        #check for any events recieved from Airtime.
        try:
            notifier.connection.drain_events(timeout=0.1)
        #avoid logging a bunch of timeout messages.
        except socket.timeout:
            pass
        except Exception, e:
            self.logger.info("%s", e)

