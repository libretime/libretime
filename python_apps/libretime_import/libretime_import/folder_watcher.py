import sys
import json
import time
import select
import signal
import os
import logging 
import pyinotify
import requests
from requests.auth import HTTPBasicAuth

""" This class will watch a folder and proceed to import any files written to it via a post request to the LibreTime 
server that is configured via the libretime config folder. Any files that exist in the folder when the service starts 
will be uploaded and deleted first and then as long as the libretime_import service is running any files should be 
uploaded as soon as they are copied or otherwise written into the watched folder. 
"""
class FolderWatcher:

    def __init__(self, lt_config):
        ''' Start watching the folder for file uploads
            
            Keyword arguments:
                lt_config: A ConfigParser object containing the [general] configuration.
        '''
    
        self._shutdown = False

        # Read the LibreTime web server settings from the lt_config file
        # The exceptions throw here by default give good error messages. 
        LT_CONFIG_SECTION = "general"
        self._host = lt_config.get(LT_CONFIG_SECTION, 'base_url')
        self._port = lt_config.getint(LT_CONFIG_SECTION, 'base_port')
        self._basedir = lt_config.get(LT_CONFIG_SECTION, 'base_dir')
        self._api_key = lt_config.get(LT_CONFIG_SECTION, 'api_key')
        # TODO - make import_dir configurable via the command line or database add it to the airtime.conf config w/ installer
        self._import_dir = lt_config.get(LT_CONFIG_SECTION, 'import_dir')

        # Set up a signal handler so we can shutdown gracefully - perhaps this is not needed
        # For some reason, this signal handler must be set up here. I'd rather 
        # put it in LibretimeImportServer, but it doesn't work there (something to do
        # with pika's SIGTERM handler interfering with it, I think...)
        # signal.signal(signal.SIGTERM, self.graceful_shutdown)

        self.watch_folder()



    def watch_folder(self):
        import_dir = self._import_dir
        logging.info(" Watching %s folder for new files...", import_dir)
        # pyinotify uses a mask file - if a new file is written into the upload folder it should create a IN_CLOSE_WRITE
        # event when the file is finished being written, if a FTP upload disconnects then this could trigger incomplete
        # files being uploaded
        mask = pyinotify.IN_CLOSE_WRITE
        # lets send a /media/rest POST account via requests to the server
        url = 'http://' + str(self._host) + ':' + str(self._port) + str(self._basedir) + 'rest/media'
        print url
        api_key = self._api_key
        # pulled this solution from stackoverflow https://stackoverflow.com/questions/18305026/python-pyinotify-to-monitor-the-specified-suffix-files-in-a-dir
        # this will match the files processed to ensure that it only sends files with known audio types to the server
        SUFFIXES = {".mp3","MP3",".m4a","M4A",".flac","FLAC",".wav","WAV",".ogg","OGG"}
        def suffix_filter(event):
            # return True to stop processing of event (to "stop chaining")
            return os.path.splitext(event.pathname)[1] not in SUFFIXES
        wm = pyinotify.WatchManager()  # Watch Manager
        #processevent = pyinotify.ProcessEvent(pevent=suffix_filter)
        class EventHandler(pyinotify.ProcessEvent):
            def __call__(self, event):
                if not suffix_filter(event):
                    super(EventHandler, self).__call__(event)
            def process_IN_CLOSE_WRITE(self, event):
                logging.info("This file was written to the directory and will be uploaded: %s", event.pathname)
                files = {'file': open(event.pathname, 'rb')}
                r = requests.post(url, auth=HTTPBasicAuth(str(api_key), ''), files=files)
                print r.text
                #TODO we might want to parse r.text to determine if the upload status = 1 and was successful then delete
                os.remove(event.pathname)
            def default(self,event):
                print event.maskname, event.pathname
        handler = EventHandler()
        notifier = pyinotify.ThreadedNotifier(wm,handler)
        notifier.start()
        # TODO check and see if self._import_dir exists and have exception if it does not
        # rec=True and auto_add = True allow the watch to import any files that are inside of a folder that is copied
        # into the uploads directory - the files will be removed by the folder will remain
        # TODO possible housekeeping and deletion of directories after all files have been removed from them
        wdd = wm.add_watch(import_dir, mask, rec=True, auto_add=True)
