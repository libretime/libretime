# -*- coding: utf-8 -*-

import os
import time
import pyinotify
import shutil

from subprocess import Popen, PIPE
from api_clients import api_client

class AirtimeMediaMonitorBootstrap():
    
    """AirtimeMediaMonitorBootstrap constructor

    Keyword Arguments:
    logger      -- reference to the media-monitor logging facility
    pe          -- reference to an instance of ProcessEvent
    api_clients -- reference of api_clients to communicate with airtime-server
    """
    def __init__(self, logger, pe, api_client, mmc, wm, config):
        self.logger = logger
        self.pe = pe
        self.api_client = api_client
        self.mmc = mmc
        self.wm = wm
        self.config = config
        # add /etc on watch list so we can detect mount
        self.mount_file = "/etc"
        self.curr_mtab_file = "/var/tmp/airtime/media-monitor/currMtab"
        self.logger.info("Adding %s on watch list...", self.mount_file)
        self.wm.add_watch(self.mount_file, pyinotify.ALL_EVENTS, rec=False, auto_add=False)
        
        tmp_dir = os.path.dirname(self.curr_mtab_file)
        if not os.path.exists(tmp_dir):
            os.makedirs(tmp_dir)
        
        # create currMtab file if it's the first time
        if not os.path.exists(self.curr_mtab_file):
            shutil.copy('/etc/mtab', self.curr_mtab_file)

    """On bootup we want to scan all directories and look for files that
    weren't there or files that changed before media-monitor process
    went offline.
    """
    def scan(self):
        directories = self.get_list_of_watched_dirs();

        self.logger.info("watched directories found: %s", directories)

        for id, dir in directories.iteritems():
            self.logger.debug("%s, %s", id, dir)
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
        removed_files = set()


        db_known_files_set = set()
        files = self.list_db_files(dir_id)
        for file in files['files']:
            db_known_files_set.add(file)

        all_files = self.mmc.scan_dir_for_new_files(dir)

        all_files_set = set()
        for file_path in all_files:
            file_path = file_path.strip(" \n")
            if len(file_path) > 0 and self.config.problem_directory not in file_path:
                all_files_set.add(file_path[len(dir):])

        # if dir doesn't exists, update db
        if not os.path.exists(dir):
            self.pe.handle_watched_dir_missing(dir)

        if os.path.exists(self.mmc.timestamp_file):
            """find files that have been modified since the last time media-monitor process started."""
            time_diff_sec = time.time() - os.path.getmtime(self.mmc.timestamp_file)
            command = "find %s -iname '*.ogg' -o -iname '*.mp3' -type f -readable -mmin -%d" % (dir, time_diff_sec/60+1)
        else:
            command = "find %s -iname '*.ogg' -o -iname '*.mp3' -type f -readable" % dir

        self.logger.debug(command)
        stdout = self.mmc.exec_command(command)
        
        if stdout is None:
            self.logger.error("Unrecoverable error when syncing db to filesystem.")
            return

        new_files = stdout.splitlines()

        new_and_modified_files = set()
        for file_path in new_files:
            file_path = file_path.strip(" \n")
            if len(file_path) > 0 and self.config.problem_directory not in file_path:
                new_and_modified_files.add(file_path[len(dir):])

        """
        new_and_modified_files gives us a set of files that were either copied or modified
        since the last time media-monitor was running. These files were collected based on
        their modified timestamp. But this is not all that has changed in the directory. Files
        could have been removed, or files could have been moved into this directory (moving does
        not affect last modified timestamp). Lets get a list of files that are on the file-system
        that the db has no record of, and vice-versa.
        """
        deleted_files_set = db_known_files_set - all_files_set
        new_files_set = all_files_set - db_known_files_set
        modified_files_set = new_and_modified_files - new_files_set

        self.logger.info(u"Deleted files: \n%s\n\n", deleted_files_set)
        self.logger.info(u"New files: \n%s\n\n", new_files_set)
        self.logger.info(u"Modified files: \n%s\n\n", modified_files_set)

        #"touch" file timestamp
        try:
            self.mmc.touch_index_file()
        except Exception, e:
            self.logger.warn(e)

        for file_path in deleted_files_set:
            self.logger.debug("deleted file")
            full_file_path = os.path.join(dir, file_path)
            self.logger.debug(full_file_path)
            self.pe.handle_removed_file(False, full_file_path)

        for file_path in new_files_set:
            self.logger.debug("new file")
            full_file_path = os.path.join(dir, file_path)
            self.logger.debug(full_file_path)
            if os.path.exists(full_file_path):
                self.pe.handle_created_file(False, full_file_path, os.path.basename(full_file_path))

        for file_path in modified_files_set:
            self.logger.debug("modified file")
            full_file_path = "%s%s" % (dir, file_path)
            self.logger.debug(full_file_path)
            if os.path.exists(full_file_path):
                self.pe.handle_modified_file(False, full_file_path, os.path.basename(full_file_path))
