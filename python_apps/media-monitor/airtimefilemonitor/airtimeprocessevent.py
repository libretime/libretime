import socket
import logging
import time

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

    def add_filepath_to_ignore(self, filepath):
        self.ignore_event.add(filepath)

    def process_IN_MOVE_SELF(self, event):
        self.logger.info("event: %s", event)
        if event.dir:
            path = event.path
            wd = self.wm.get_wd(path)
            self.logger.info("Removing watch on: %s wd %s", path, wd)
            self.wm.rm_watch(wd, rec=True)
            if "-unknown-path" in path:
                pos = path.find("-unknown-path")
                path = path[0:pos]
                
            list = self.api_client.list_all_watched_dirs()
            # case where the dir that is being watched is moved to somewhere 
            if path in list:
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
                self.file_events.append({'mode': self.config.MODE_DELETE_DIR, 'filepath': path})
                
            
    def process_IN_DELETE_SELF(self, event):
        self.logger.info("event: %s", event)
        if event.dir:
            path = event.path
            wd = self.wm.get_wd(path)
            self.logger.info("Removing watch on: %s wd %s", path, wd)
            self.wm.rm_watch(wd, rec=True)
            if "-unknown-path" in path:
                pos = path.find("-unknown-path")
                path = path[0:pos]
                
            list = self.api_client.list_all_watched_dirs()
            if path in list:
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
                self.file_events.append({'mode': self.config.MODE_DELETE_DIR, 'filepath': path})
                    
    #event.dir: True if the event was raised against a directory.
    #event.name: filename
    #event.pathname: pathname (str): Concatenation of 'path' and 'name'.
    def process_IN_CREATE(self, event):
        self.handle_created_file(event.dir, event.pathname, event.name)

    def handle_created_file(self, dir, pathname, name):
        if not dir:
            self.logger.debug("PROCESS_IN_CREATE: %s, name: %s, pathname: %s ", dir, name, pathname)
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
                    new_filepath = self.mmc.organize_new_file(pathname)
                    return new_filepath
                else:
                    self.mmc.set_needed_file_permissions(pathname, dir)
                    if self.mmc.is_parent_directory(pathname, self.config.recorded_directory):
                        is_recorded = True
                    else :
                        is_recorded = False
                    self.file_events.append({'mode': self.config.MODE_CREATE, 'filepath': pathname, 'is_recorded_show': is_recorded})

        else:
            #event is because of a created directory
            if self.mmc.is_parent_directory(pathname, self.config.storage_directory):
                self.mmc.set_needed_file_permissions(pathname, dir)

    def process_IN_MODIFY(self, event):
        self.logger.info("process_IN_MODIFY: %s", event)
        self.handle_modified_file(event.dir, event.pathname, event.name)

    def handle_modified_file(self, dir, pathname, name):
        if not dir and not self.mmc.is_parent_directory(pathname, self.config.organize_directory):
            self.logger.info("Modified: %s", pathname)
            if self.mmc.is_audio_file(name):
                self.file_events.append({'filepath': pathname, 'mode': self.config.MODE_MODIFY})

    #if a file is moved somewhere, this callback is run. With details about
    #where the file is being moved from. The corresponding process_IN_MOVED_TO
    #callback is only called if the destination of the file is also in a watched
    #directory.
    def process_IN_MOVED_FROM(self, event):
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


    #Some weird thing to note about this event: it seems that if a file is moved to a newly
    #created directory, then the IN_MOVED_FROM event will be called, but instead of a corresponding
    #IN_MOVED_TO event, a IN_CREATED event will happen instead. However if the directory existed before
    #then the IN_MOVED_TO event will be called.
    def process_IN_MOVED_TO(self, event):
        self.logger.info("process_IN_MOVED_TO: %s", event)
        #if stuff dropped in stor via a UI move must change file permissions.
        self.mmc.set_needed_file_permissions(event.pathname, event.dir)
        if not event.dir:
            if self.mmc.is_audio_file(event.name):
                if event.cookie in self.temp_files:
                    self.file_events.append({'filepath': event.pathname, 'mode': self.config.MODE_MODIFY})
                    del self.temp_files[event.cookie]
                elif event.cookie in self.cookies_IN_MOVED_FROM:
                    #files original location was also in a watched directory
                    del self.cookies_IN_MOVED_FROM[event.cookie]
                    if self.mmc.is_parent_directory(event.pathname, self.config.organize_directory):
                        filepath = self.mmc.organize_new_file(event.pathname)
                    else:
                        filepath = event.pathname

                    if (filepath is not None):
                        self.file_events.append({'filepath': filepath, 'mode': self.config.MODE_MOVED})
                else:
                    if self.mmc.is_parent_directory(event.pathname, self.config.organize_directory):
                        self.mmc.organize_new_file(event.pathname)
                    else:
                        #show dragged from unwatched folder into a watched folder. Do not "organize".:q!
                        if self.mmc.is_parent_directory(event.pathname, self.config.recorded_directory):
                            is_recorded = True
                        else :
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
        self.logger.info("process_IN_DELETE: %s", event)
        self.handle_removed_file(event.dir, event.pathname)

    def handle_removed_file(self, dir, pathname):
        self.logger.info("Deleting %s", pathname)
        if not dir:
            if self.mmc.is_audio_file(pathname):
                if pathname in self.ignore_event:
                    self.ignore_event.remove(pathname)
                elif not self.mmc.is_parent_directory(pathname, self.config.organize_directory):
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


        #check for any events received from Airtime.
        try:
            notifier.connection.drain_events(timeout=0.1)
        #avoid logging a bunch of timeout messages.
        except socket.timeout:
            pass
        except Exception, e:
            self.logger.info("%s", e)
            time.sleep(3)

