import sys
import json
import time
import select
import signal
import os
import logging 
import pyinotify
import urllib3

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
        default_import = {'import_dir' : '/srv/airtime/stor/uploads'}
        # TODO import_dir is added via the BASH install script but not via setup.py so putting in a default value as a temporary fix
        self._import_dir = lt_config.get(LT_CONFIG_SECTION, 'import_dir')

        # Set up a signal handler so we can shutdown gracefully - perhaps this is not needed
        # For some reason, this signal handler must be set up here. I'd rather 
        # put it in LibretimeImportServer, but it doesn't work there (something to do
        # with pika's SIGTERM handler interfering with it, I think...)
        # TODO - create a scan_folder function that will find all files existing in the directory and upload them
        self.watch_folder()
    """
    upload_file will take a file and send it to the local REST api and the remove it from the filesystem
    need to learn more python to figure out how to properly call it from inside of the EventHandler function
    """
    def upload_and_remove_file(self, url, filename):
        files = {'file': open(filename, 'rb')}
        r = requests.post(url, auth=HTTPBasicAuth(str(api_key), ''), files=files)
        print(r.text)
        # TODO we might want to parse r.text to determine if the upload status = 1 and was successful then delete
        os.remove(event.pathname)

    def watch_folder(self):
        import_dir = self._import_dir
        logging.info(" Watching %s folder for new files...", import_dir)
        # pyinotify uses a mask file - if a new file is written into the upload folder it should create a IN_CLOSE_WRITE
        # event when the file is finished being written, if a FTP upload disconnects then this could trigger incomplete
        # files being uploaded
        mask = pyinotify.IN_CLOSE_WRITE
        # lets send a /media/rest POST account via requests to the server
        url = 'http://' + str(self._host) + ':' + str(self._port) + str(self._basedir) + 'rest/media'
        print(url)
        api_key = self._api_key
        # pulled this solution from stackoverflow https://stackoverflow.com/questions/18305026/python-pyinotify-to-monitor-the-specified-suffix-files-in-a-dir
        # this will match the files processed to ensure that it only sends files with known audio types to the server
        SUFFIXES = {".mp3","MP3",".m4a","M4A",".flac","FLAC",".wav","WAV",".ogg","OGG"}
        def suffix_filter(event):
            # return True to stop processing of event (to "stop chaining")
            return os.path.splitext(event.pathname)[1] not in SUFFIXES
        wm = pyinotify.WatchManager()  # Watch Manager
        #processevent = pyinotify.ProcessEvent(pevent=suffix_filter)
        http = urllib3.PoolManager()
        class EventHandler(pyinotify.ProcessEvent):
            def __call__(self, event):
                if not suffix_filter(event):
                    super(EventHandler, self).__call__(event)
            def process_IN_CLOSE_WRITE(self, event):
                logging.info("This file was written to the import directory and will be uploaded: %s", event.pathname)
                # TODO - figure out how to properly call above function upload_file(FolderWatcher, event.pathname,url)
                #FolderWatcher().upload_and_remove_file(self, event.pathname, url)
	            #Need to use pathname2url to pass utf8 filenames otherwise it breaks, probably related to PHP noncompliance with RFC-2231 and requests using that
                # the solution is to encode it with 3986 https://stackoverflow.com/questions/20801929/urlencode-for-rfc-3986
                with open (event.pathname, "rb") as audiofile:
                    audio_data = audiofile.read()
                path, filename = os.path.split(event.pathname)
                authstring = str(api_key) + ':'
                print(filename)
                headers = urllib3.util.make_headers(basic_auth=authstring)
                fields = {'file': (filename, audio_data)}
                r = http.request('POST', url, headers=headers, fields=fields)
                print(r.status)
                #TODO we might want to parse r.text to determine if the upload status = 1 and was successful then delete
                os.remove(event.pathname)
                #TODO we should check if the file is in a subdirectory and if that subdirectory is empty
                # and then delete the subdirectory
                #logging.info("checking to see if %s is empty", os.path.dirname(event.pathname))
                if ((not (os.listdir(os.path.dirname(event.pathname)))) and (os.path.dirname(event.pathname) != import_dir)):
                    os.rmdir(os.path.dirname(event.pathname))
                    #logging.info("deleted this folder %s", os.path.dirname(event.pathname))


            def default(self,event):
                print(event.maskname, event.pathname)
        handler = EventHandler()
        notifier = pyinotify.ThreadedNotifier(wm,handler)
        notifier.start()
        # TODO check and see if self._import_dir exists and have exception if it does not
        # TODO - might also want to check and ensure that the import directory is not set to the current organized dir
        # to avoid an endless file importation loop
        #
        # rec=True and auto_add = True allow the watch to import any files that are inside of a folder that is copied
        # into the uploads directory - the files will be removed by the folder will remain
        # TODO possible housekeeping and deletion of directories after all files have been removed from them
        wdd = wm.add_watch(import_dir, mask, rec=True, auto_add=True)
