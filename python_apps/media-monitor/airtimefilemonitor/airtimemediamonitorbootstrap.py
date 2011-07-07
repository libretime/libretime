import os
import time

from subprocess import Popen, PIPE

class AirtimeMediaMonitorBootstrap():

    def __init__(self, logger, multi_queue, pe, api_client):
        self.logger = logger
        self.multi_queue = multi_queue
        self.pe = pe
        self.airtime_tmp = '/var/tmp/airtime'
        self.api_client = api_client
        
    """
    on bootup we want to scan all directories and look for files that
    weren't there or files that changed before media-monitor process
    went offline.
    """
    def scan(self):
        directories = self.get_list_of_watched_dirs();
        
        self.logger.info("watched directories found: %s", directories)
        
        for id, dir in directories.iteritems():
            self.logger.debug("%s, %s", id, dir)
            self.check_for_diff(id, dir)
            
    def list_db_files(self, dir_id):
        return self.api_client.list_all_db_files(dir_id)
        
    def get_list_of_watched_dirs(self):
        json = self.api_client.list_all_watched_dirs()
        return json["dirs"]
            
    def check_for_diff(self, dir_id, dir):        
        #set to hold new and/or modified files. We use a set to make it ok if files are added
        #twice. This is because some of the tests for new files return result sets that are not
        #mutually exclusive from each other.
        new_and_modified_files = set()
        removed_files = set()
        
        
        db_known_files_set = set()
        files = self.list_db_files(dir_id)
        for file in files['files']:
            db_known_files_set.add(file)
            
            
        command = "find %s -type f -iname '*.ogg' -o -iname '*.mp3' -readable" % dir
        stdout = self.execCommandAndReturnStdOut(command)
        stdout = unicode(stdout, "utf_8")

        new_files = stdout.splitlines()
        all_files_set = set()
        for file_path in new_files:
            if len(file_path.strip(" \n")) > 0:
                all_files_set.add(file_path[len(dir)+1:])           
        
        
        if os.path.exists("/var/tmp/airtime/.media_monitor_boot"):
            #find files that have been modified since the last time
            #media-monitor process started.
            time_diff_sec = time.time() - os.path.getmtime("/var/tmp/airtime/.media_monitor_boot")
            command = "find %s -type f -iname '*.ogg' -o -iname '*.mp3' -readable -mmin -%d" % (dir, time_diff_sec/60+1)
        else:
            command = "find %s -type f -iname '*.ogg' -o -iname '*.mp3' -readable" % dir
            
        stdout = self.execCommandAndReturnStdOut(command)
        stdout = unicode(stdout, "utf_8")

        new_files = stdout.splitlines()
                
        for file_path in new_files:
            if len(file_path.strip(" \n")) > 0:
                new_and_modified_files.add(file_path[len(dir)+1:])
            
        #new_and_modified_files gives us a set of files that were either copied or modified
        #since the last time media-monitor was running. These files were collected based on
        #their modified timestamp. But this is not all that has changed in the directory. Files
        #could have been removed, or files could have been moved into this directory (moving does
        #not affect last modified timestamp). Lets get a list of files that are on the file-system
        #that the db has no record of, and vice-versa.
        
        deleted_files_set = db_known_files_set - all_files_set
        new_files_set = all_files_set - db_known_files_set
        modified_files_set = new_and_modified_files - new_files_set
        
        self.logger.info("Deleted files: \n%s\n\n"%deleted_files_set)
        self.logger.info("New files: \n%s\n\n"%new_files_set)
        self.logger.info("Modified files: \n%s\n\n"%modified_files_set)   
                
        #"touch" file timestamp
        open("/var/tmp/airtime/.media_monitor_boot","w")       
                
        for file_path in deleted_files_set:
            self.pe.handle_removed_file(False, "%s/%s" % (dir, file_path))
                
        for file_path in new_files_set:
            if os.path.exists(file_path):
                file_path = "%s/%s" % (dir, file_path)
                self.pe.handle_created_file(False, os.path.basename(file_path), file_path)
                
        for file_path in modified_files_set:
            if os.path.exists(file_path):
                file_path = "%s/%s" % (dir, file_path)
                self.pe.handle_modified_file(False, os.path.basename(file_path), file_path)
                            
    def execCommand(self, command):
        p = Popen(command, shell=True)
        sts = os.waitpid(p.pid, 0)[1]
        if sts != 0:
            self.logger.warn("command \n%s\n return with a non-zero return value", command)        
                
    def execCommandAndReturnStdOut(self, command):
        p = Popen(command, shell=True, stdout=PIPE)
        stdout = p.communicate()[0]
        if p.returncode != 0:
            self.logger.warn("command \n%s\n return with a non-zero return value", command)
        return stdout
                       