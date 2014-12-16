import os
import time
import shutil
import sys
import logging

from configobj import ConfigObj
from subprocess import Popen, PIPE
from api_clients import api_client as apc

"""
The purpose of this script is that you can run it, and it will compare what the database has to what your filesystem
has. It will then report if there are any differences. It will *NOT* make any changes, unlike media-monitor which uses
similar code when it starts up (but then makes changes if something is different)
"""


class AirtimeMediaMonitorBootstrap():
    
    """AirtimeMediaMonitorBootstrap constructor

    Keyword Arguments:
    logger      -- reference to the media-monitor logging facility
    pe          -- reference to an instance of ProcessEvent
    api_clients -- reference of api_clients to communicate with airtime-server
    """
    def __init__(self):
        config = ConfigObj('/etc/airtime/airtime.conf')
        self.api_client = apc.api_client_factory(config)

        """        
        try:
            logging.config.fileConfig("logging.cfg")
        except Exception, e:
            print 'Error configuring logging: ', e
            sys.exit(1)
        """
        
        self.logger = logging.getLogger()
        self.logger.info("Adding %s on watch list...", "xxx")
        
        self.scan()
        
    """On bootup we want to scan all directories and look for files that
    weren't there or files that changed before media-monitor process
    went offline.
    """
    def scan(self):
        directories = self.get_list_of_watched_dirs();

        self.logger.info("watched directories found: %s", directories)

        for id, dir in directories.iteritems():
            self.logger.debug("%s, %s", id, dir)
            #CHANGED!!!
            #self.sync_database_to_filesystem(id, api_client.encode_to(dir, "utf-8"))
            self.sync_database_to_filesystem(id, dir)

    """Gets a list of files that the Airtime database knows for a specific directory.
    You need to provide the directory's row ID, which is obtained when calling
    get_list_of_watched_dirs function.
    dir_id -- row id of the directory in the cc_watched_dirs database table
    """
    def list_db_files(self, dir_id):
        return self.api_client.list_all_db_files(dir_id)

    """
    returns the path and the database row id for this path for all watched directories. Also
    returns the Stor directory, which can be identified by its row id (always has value of "1")
    """
    def get_list_of_watched_dirs(self):
        json = self.api_client.list_all_watched_dirs()
        return json["dirs"]
        
    def scan_dir_for_existing_files(self, dir):
        command = 'find "%s" -type f -iname "*.ogg" -o -iname "*.mp3" -readable' % dir.replace('"', '\\"')
        self.logger.debug(command)
        #CHANGED!!
        stdout = self.exec_command(command).decode("UTF-8")
        
        return stdout.splitlines()
        
    def exec_command(self, command):
        p = Popen(command, shell=True, stdout=PIPE, stderr=PIPE)
        stdout, stderr = p.communicate()
        if p.returncode != 0:
            self.logger.warn("command \n%s\n return with a non-zero return value", command)
            self.logger.error(stderr)
        return stdout

    """
    This function takes in a path name provided by the database (and its corresponding row id)
    and reads the list of files in the local file system. Its purpose is to discover which files
    exist on the file system but not in the database and vice versa, as well as which files have
    been modified since the database was last updated. In each case, this method will call an
    appropiate method to ensure that the database actually represents the filesystem.
    dir_id -- row id of the directory in the cc_watched_dirs database table
    dir    -- pathname of the directory
    """
    def sync_database_to_filesystem(self, dir_id, dir):
        """
        set to hold new and/or modified files. We use a set to make it ok if files are added
        twice. This is because some of the tests for new files return result sets that are not
        mutually exclusive from each other.
        """
        db_known_files_set = set()

        files = self.list_db_files(dir_id)
        for file in files['files']:
            db_known_files_set.add(file)

        existing_files = self.scan_dir_for_existing_files(dir)

        existing_files_set = set()
        for file_path in existing_files:
            if len(file_path.strip(" \n")) > 0:
                existing_files_set.add(file_path[len(dir):])

            
        deleted_files_set = db_known_files_set - existing_files_set
        new_files_set = existing_files_set - db_known_files_set


        print ("DB Known files: \n%s\n\n"%len(db_known_files_set))
        print ("FS Known files: \n%s\n\n"%len(existing_files_set))
        
        print ("Deleted files: \n%s\n\n"%deleted_files_set)
        print ("New files: \n%s\n\n"%new_files_set)
        
if __name__ == "__main__":
    AirtimeMediaMonitorBootstrap()
