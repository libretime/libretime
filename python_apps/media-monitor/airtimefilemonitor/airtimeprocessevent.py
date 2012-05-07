import socket
import logging
import time
import os
import shutil
import difflib
import traceback

import pyinotify
from pyinotify import ProcessEvent

from airtimemetadata import AirtimeMetadata
from airtimefilemonitor.mediaconfig import AirtimeMediaConfig

from api_clients import api_client

class AirtimeProcessEvent(ProcessEvent):

    #TODO
    def my_init(self, queue, airtime_config=None, wm=None, mmc=None, api_client=api_client):
        """
        Method automatically called from ProcessEvent.__init__(). Additional
        keyworded arguments passed to ProcessEvent.__init__() are then
        delegated to my_init().
        """
        self.logger = logging.getLogger()
        self.config = airtime_config

        #put the file path into this dict if we want to ignore certain
        #events. For example, when deleting a file from the web ui, we
        #are going to delete it from the db on the server side, so media-monitor
        #doesn't need to contact the server and tell it to delete again.
        self.ignore_event = set()

        self.temp_files = {}
        self.cookies_IN_MOVED_FROM = {}
        self.file_events = []
        self.multi_queue = queue
        self.wm = wm
        self.md_manager = AirtimeMetadata()
        self.mmc = mmc
        self.api_client = api_client
        self.create_dict = {}
        self.mount_file_dir = "/etc";
        self.mount_file = "/etc/mtab";
        self.curr_mtab_file = "/var/tmp/airtime/media-monitor/currMtab"
        self.prev_mtab_file = "/var/tmp/airtime/media-monitor/prevMtab"

    def add_filepath_to_ignore(self, filepath):
        self.ignore_event.add(filepath)

    def process_IN_MOVE_SELF(self, event):
        self.logger.info("event: %s", event)
        path = event.path
        if event.dir:
            if "-unknown-path" in path:
                unknown_path = path
                pos = path.find("-unknown-path")
                path = path[0:pos]+"/"
                
                list = self.api_client.list_all_watched_dirs()
                # case where the dir that is being watched is moved to somewhere 
                if path in list[u'dirs'].values():
                    self.logger.info("Requesting the airtime server to remove '%s'", path)
                    res = self.api_client.remove_watched_dir(path)
                    if(res is None):
                        self.logger.info("Unable to connect to the Airtime server.")
                    # sucess
                    if(res['msg']['code'] == 0):
                        self.logger.info("%s removed from watch folder list successfully.", path)
                    else:
                        self.logger.info("Removing the watch folder failed: %s", res['msg']['error'])
                else:
                    # subdir being moved
                    # in this case, it has to remove watch manualy and also have to manually delete all records
                    # on cc_files table
                    wd = self.wm.get_wd(unknown_path)
                    self.logger.info("Removing watch on: %s wd %s", unknown_path, wd)
                    self.wm.rm_watch(wd, rec=True)
                    self.file_events.append({'mode': self.config.MODE_DELETE_DIR, 'filepath': path})
                
            
    def process_IN_DELETE_SELF(self, event):
        if event.path in self.mount_file_dir:
            return
        self.logger.info("event: %s", event)
        path = event.path + '/'
        if event.dir:
            list = self.api_client.list_all_watched_dirs()
            if path in list[u'dirs'].values():
                self.logger.info("Requesting the airtime server to remove '%s'", path)
                res = self.api_client.remove_watched_dir(path)
                if(res is None):
                    self.logger.info("Unable to connect to the Airtime server.")
                # sucess
                if(res['msg']['code'] == 0):
                    self.logger.info("%s removed from watch folder list successfully.", path)
                else:
                    self.logger.info("Removing the watch folder failed: %s", res['msg']['error'])
    
    def process_IN_CREATE(self, event):
        if event.path in self.mount_file_dir:
            return
        self.logger.info("event: %s", event)
        if not event.dir:
            # record the timestamp of the time on IN_CREATE event
            self.create_dict[event.pathname] = time.time()
        
    #event.dir: True if the event was raised against a directory.
    #event.name: filename
    #event.pathname: pathname (str): Concatenation of 'path' and 'name'.
    # we used to use IN_CREATE event, but the IN_CREATE event gets fired before the
    # copy was done. Hence, IN_CLOSE_WRITE is the correct one to handle.    
    def process_IN_CLOSE_WRITE(self, event):
        if event.path in self.mount_file_dir:
            return
        self.logger.info("event: %s", event)
        self.logger.info("create_dict: %s", self.create_dict)
        
        if event.pathname in self.create_dict:
            # detele corresponding entry from create_dict
            self.create_dict.pop(event.pathname)
            self.handle_created_file(event.dir, event.pathname, event.name)
        
    def handle_created_file(self, dir, pathname, name):
        if not dir:
            self.logger.debug("PROCESS_IN_CLOSE_WRITE: %s, name: %s, pathname: %s ", dir, name, pathname)
            #event is because of a created file

            if self.mmc.is_temp_file(name) :
                #file created is a tmp file which will be modified and then moved back to the original filename.
                #Easy Tag creates this when changing metadata of ogg files.
                self.temp_files[pathname] = None
            #file is being overwritten/replaced in GUI.
            elif "goutputstream" in pathname:
                self.temp_files[pathname] = None
            elif self.mmc.is_audio_file(pathname):
                if self.mmc.is_parent_directory(pathname, self.config.organize_directory):
                        
                    #file was created in /srv/airtime/stor/organize. Need to process and move
                    #to /srv/airtime/stor/imported
                    oldPath = pathname
                    pathname = self.mmc.organize_new_file(pathname)
                    
                    #delete files from organize if they can not be read properly.
                    if pathname is None:
                        try:
                            self.logger.warn("Deleting file because it cannot be read properly: %s", oldPath)
                            os.remove(oldPath)
                        except Exception, e:
                            self.logger.error('Exception: %s', e)
                            self.logger.error("traceback: %s", traceback.format_exc())
                    else:
                        #ensure file is world readable (Liquidsoap and Web UI preview)
                        if self.mmc.make_readable(pathname):
                            is_recorded = self.mmc.is_parent_directory(pathname, self.config.recorded_directory)
                            self.file_events.append({'mode': self.config.MODE_CREATE, 'filepath': pathname, 'is_recorded_show': is_recorded})
                        else:
                            self.logger.warn("Couldn't add %s because failed to change file permissions", pathname)

    def process_IN_MODIFY(self, event):
        # if IN_MODIFY is followed by IN_CREATE, it's not true modify event
        if not event.pathname in self.create_dict:
            self.logger.info("process_IN_MODIFY: %s", event)
            self.handle_modified_file(event.dir, event.pathname, event.name)

    def handle_modified_file(self, dir, pathname, name):
        # if /etc/mtab is modified
        if pathname in self.mount_file:
            self.handle_mount_change()
        # update timestamp on create_dict for the entry with pathname as the key
        if pathname in self.create_dict:
            self.create_dict[pathname] = time.time()
        if not dir and not self.mmc.is_parent_directory(pathname, self.config.organize_directory):
            self.logger.info("Modified: %s", pathname)
            if self.mmc.is_audio_file(name) and self.mmc.make_readable(pathname):
                self.file_events.append({'filepath': pathname, 'mode': self.config.MODE_MODIFY})
    
    # if change is detected on /etc/mtab, we check what mount(file system) was added/removed
    # and act accordingly
    def handle_mount_change(self):
        self.logger.info("Mount change detected, handling changes...");
        # take snapshot of mtab file and update currMtab and prevMtab
        # move currMtab to prevMtab and create new currMtab
        shutil.move(self.curr_mtab_file, self.prev_mtab_file)
        # create the file
        shutil.copy(self.mount_file, self.curr_mtab_file)
        
        d = difflib.Differ()
        curr_fh = open(self.curr_mtab_file, 'r')
        prev_fh = open(self.prev_mtab_file, 'r')
        
        diff = list(d.compare(prev_fh.readlines(), curr_fh.readlines()))
        added_mount_points = []
        removed_mount_points = []
        
        for dir in diff:
            info = dir.split(' ')
            if info[0] == '+':
                added_mount_points.append(info[2])
            elif info[0] == '-':
                removed_mount_points.append(info[2])
        
        self.logger.info("added: %s", added_mount_points)
        self.logger.info("removed: %s", removed_mount_points)
        
        # send current mount information to Airtime
        self.api_client.update_file_system_mount(added_mount_points, removed_mount_points);
    
    def handle_watched_dir_missing(self, dir):
        self.api_client.handle_watched_dir_missing(dir);
        
    #if a file is moved somewhere, this callback is run. With details about
    #where the file is being moved from. The corresponding process_IN_MOVED_TO
    #callback is only called if the destination of the file is also in a watched
    #directory.
    def process_IN_MOVED_FROM(self, event):
        if event.path in self.mount_file:
            return
        self.logger.info("process_IN_MOVED_FROM: %s", event)
        if not event.dir:
            if event.pathname in self.temp_files:
                self.temp_files[event.cookie] = event.pathname
            elif not self.mmc.is_parent_directory(event.pathname, self.config.organize_directory):
                #we don't care about moved_from events from the organize dir.
                if self.mmc.is_audio_file(event.name):
                    self.cookies_IN_MOVED_FROM[event.cookie] = (event, time.time())
        else:
            self.cookies_IN_MOVED_FROM[event.cookie] = (event, time.time())

    def process_IN_MOVED_TO(self, event):
        self.logger.info("process_IN_MOVED_TO: %s", event)
        # if /etc/mtab is modified
        filename = self.mount_file_dir +"/mtab"
        if event.pathname in filename:
            self.handle_mount_change()

        if not event.dir:
            if self.mmc.is_audio_file(event.name):
                if self.mmc.make_readable(event.pathname):
                    if event.cookie in self.temp_files:
                        self.file_events.append({'filepath': event.pathname, 'mode': self.config.MODE_MODIFY})
                        del self.temp_files[event.cookie]
                    elif event.cookie in self.cookies_IN_MOVED_FROM:
                        #files original location was also in a watched directory
                        del self.cookies_IN_MOVED_FROM[event.cookie]
                        if self.mmc.is_parent_directory(event.pathname, self.config.organize_directory):
                            filepath = self.mmc.organize_new_file(event.pathname)
                            
                            #delete files from organize if they can not be read properly.
                            if filepath is None:
                                try:
                                    self.logger.info("Deleting file because it cannot be read properly: %s", event.pathname)
                                    os.remove(event.pathname)
                                    return
                                except Exception, e:
                                    self.logger.error('Exception: %s', e)
                                    self.logger.error("traceback: %s", traceback.format_exc())

                        else:
                            filepath = event.pathname

                        if (filepath is not None):
                            self.file_events.append({'filepath': filepath, 'mode': self.config.MODE_MOVED})
                    else:
                        if self.mmc.is_parent_directory(event.pathname, self.config.organize_directory):
                            filepath = self.mmc.organize_new_file(event.pathname)
                            
                            #delete files from organize if they can not be read properly.
                            if filepath is None:
                                try:
                                    self.logger.info("Deleting file because it cannot be read properly: %s", event.pathname)
                                    os.remove(event.pathname)
                                    return
                                except Exception, e:
                                    self.logger.error('Exception: %s', e)
                                    self.logger.error("traceback: %s", traceback.format_exc())
                        else:
                            #show dragged from unwatched folder into a watched folder. Do not "organize".:q!
                            if self.mmc.is_parent_directory(event.pathname, self.config.recorded_directory):
                                is_recorded = True
                            else:
                                is_recorded = False
                            self.file_events.append({'mode': self.config.MODE_CREATE, 'filepath': event.pathname, 'is_recorded_show': is_recorded})
        else:
            #When we move a directory into a watched_dir, we only get a notification that the dir was created,
            #and no additional information about files that came along with that directory.
            #need to scan the entire directory for files.
                        
            if event.cookie in self.cookies_IN_MOVED_FROM:
                del self.cookies_IN_MOVED_FROM[event.cookie]
                mode = self.config.MODE_MOVED
            else:
                mode = self.config.MODE_CREATE
                        
            files = self.mmc.scan_dir_for_new_files(event.pathname)
            if self.mmc.is_parent_directory(event.pathname, self.config.organize_directory):
                for file in files:
                    filepath = self.mmc.organize_new_file(file)
                    if (filepath is not None):
                        self.file_events.append({'mode': mode, 'filepath': filepath, 'is_recorded_show': False})
            else:
                for file in files:
                    self.file_events.append({'mode': mode, 'filepath': file, 'is_recorded_show': False})


    def process_IN_DELETE(self, event):
        if event.path in self.mount_file_dir:
            return
        self.logger.info("process_IN_DELETE: %s", event)
        self.handle_removed_file(event.dir, event.pathname)

    def handle_removed_file(self, dir, pathname):
        self.logger.info("Deleting %s", pathname)
        if not dir:
            if self.mmc.is_audio_file(pathname):
                if pathname in self.ignore_event:
                    self.logger.info("pathname in ignore event")
                    self.ignore_event.remove(pathname)
                elif not self.mmc.is_parent_directory(pathname, self.config.organize_directory):
                    self.logger.info("deleting a file not in organize")
                    #we don't care if a file was deleted from the organize directory.
                    self.file_events.append({'filepath': pathname, 'mode': self.config.MODE_DELETE})


    def process_default(self, event):
        pass

    def notifier_loop_callback(self, notifier):
        if len(self.file_events) > 0:
            for event in self.file_events:
                self.multi_queue.put(event)
            self.mmc.touch_index_file()
            
            self.file_events = []

        #yield to worker thread
        time.sleep(0)
        
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
        
        # we don't want create_dict grow infinitely
        # this part is like a garbage collector
        for k, t in self.create_dict.items():
            now = time.time()
            if now - t > 5:
                # check if file exist
                # When whole directory is copied to the organized dir,
                # inotify doesn't fire IN_CLOSE_WRITE, hench we need special way of
                # handling those cases. We are manully calling handle_created_file
                # function.
                if os.path.exists(k):
                    # check if file is open                    
                    try:
                        command = "lsof "+k
                        f = os.popen(command)
                    except Exception, e:
                        self.logger.error('Exception: %s', e)
                        self.logger.error("traceback: %s", traceback.format_exc())
                        continue
                    
                    if not f.readlines():
                        self.logger.info("Handling file: %s", k)
                        self.handle_created_file(False, k, os.path.basename(k))
                        del self.create_dict[k]
                else:
                        del self.create_dict[k]

        #check for any events received from Airtime.
        try:
            notifier.connection.drain_events(timeout=0.1)
        #avoid logging a bunch of timeout messages.
        except socket.timeout:
            pass
        except Exception, e:
            self.logger.error('Exception: %s', e)
            self.logger.error("traceback: %s", traceback.format_exc())
            time.sleep(3)

