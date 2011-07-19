import os
import time

from subprocess import Popen, PIPE

class AirtimeMediaMonitorBootstrap():

    """AirtimeMediaMonitorBootstrap constructor

    Keyword Arguments:
    logger      -- reference to the media-monitor logging facility
    pe          -- reference to an instance of ProcessEvent
    api_clients -- reference of api_clients to communicate with airtime-server
    """
    def __init__(self, logger, pe, api_client, mmc):
        self.logger = logger
        self.pe = pe
        self.api_client = api_client
        self.mmc = mmc

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
        new_and_modified_files = set()
        removed_files = set()


        db_known_files_set = set()
        files = self.list_db_files(dir_id)
        for file in files['files']:
            db_known_files_set.add(file)

        new_files = self.mmc.scan_dir_for_new_files(dir)
        all_files_set = set()
        for file_path in new_files:
            if len(file_path.strip(" \n")) > 0:
                all_files_set.add(file_path[len(dir):])

        if os.path.exists(self.mmc.timestamp_file):
            """find files that have been modified since the last time media-monitor process started."""
            time_diff_sec = time.time() - os.path.getmtime(self.mmc.timestamp_file)
            command = "find %s -type f -iname '*.ogg' -o -iname '*.mp3' -readable -mmin -%d" % (dir, time_diff_sec/60+1)
        else:
            command = "find %s -type f -iname '*.ogg' -o -iname '*.mp3' -readable" % dir

        stdout = self.mmc.execCommandAndReturnStdOut(command)
        stdout = unicode(stdout, "utf_8")

        new_files = stdout.splitlines()

        for file_path in new_files:
            if len(file_path.strip(" \n")) > 0:
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

        #NAOMI: Please comment out the "Known files" line, if you find the bug.
        #it is for debugging purposes only (Too much data will be written to log). -mk
        self.logger.info("Known files: \n%s\n\n"%db_known_files_set)
        self.logger.info("Deleted files: \n%s\n\n"%deleted_files_set)
        self.logger.info("New files: \n%s\n\n"%new_files_set)
        self.logger.info("Modified files: \n%s\n\n"%modified_files_set)

        #"touch" file timestamp
        try:
            self.mmc.touch_index_file()
        except Exception, e:
            self.logger.warn(e)

        for file_path in deleted_files_set:
            self.pe.handle_removed_file(False, "%s%s" % (dir, file_path))

        for file_path in new_files_set:
            file_path = "%s%s" % (dir, file_path)
            if os.path.exists(file_path):
                self.pe.handle_created_file(False, os.path.basename(file_path), file_path)

        for file_path in modified_files_set:
            file_path = "%s%s" % (dir, file_path)
            if os.path.exists(file_path):
                self.pe.handle_modified_file(False, os.path.basename(file_path), file_path)
