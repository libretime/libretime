import os
import time
import media.monitor.pure as mmp
import media.monitor.log
from subprocess import Popen, PIPE
import api_clients.api_client as ac
from media.monitor.syncdb import SyncDB

logger = media.monitor.log.get_logger()

def find_command(directory, extra_arguments=""):
    """ Builds a find command that respects supported_file_formats list
    Note: Use single quotes to quote arguments """
    ext_globs = [ "-iname '*.%s'" % ext for ext in mmp.supported_extensions ]
    find_glob = ' -o '.join(ext_globs)
    return "find '%s' %s %s" % (directory, find_glob, extra_arguments)

def exec_command(command):
    p = Popen(command, shell=True, stdout=PIPE, stderr=PIPE)
    stdout, stderr = p.communicate()
    if p.returncode != 0:
        logger.warn("command \n%s\n return with a non-zero return value", command)
        logger.error(stderr)
    try:
        #File name charset encoding is UTF-8.
        stdout = stdout.decode("UTF-8")
    except Exception:
        stdout = None
        logger.error("Could not decode %s using UTF-8" % stdout)
    return stdout

def scan_dir_for_new_files(dir):
    command = find_command(directory=dir, extra_arguments="-type f -readable")
    logger.debug(command)
    stdout = exec_command(command)
    if stdout is None: return []
    else: return stdout.splitlines()

def clean_dirty_file_paths(dirty_files):
    """ clean dirty file paths by removing blanks and removing trailing/leading whitespace"""
    return filter(lambda e: len(e) > 0, [ f.strip(" \n") for f in dirty_files ])

def handle_created_file(dir, pathname, name):
    if not dir:
        self.logger.debug("PROCESS_IN_CLOSE_WRITE: %s, name: %s, pathname: %s ", dir, name, pathname)

        if self.mmc.is_temp_file(name) :
            #file created is a tmp file which will be modified and then moved back to the original filename.
            #Easy Tag creates this when changing metadata of ogg files.
            self.temp_files[pathname] = None
        #file is being overwritten/replaced in GUI.
        elif "goutputstream" in pathname:
            self.temp_files[pathname] = None
        elif self.mmc.is_audio_file(name):
            if self.mmc.is_parent_directory(pathname, self.config.organize_directory):

                #file was created in /srv/airtime/stor/organize. Need to process and move
                #to /srv/airtime/stor/imported
                file_md = self.md_manager.get_md_from_file(pathname)
                playable = self.mmc.test_file_playability(pathname)

                if file_md and playable:
                    self.mmc.organize_new_file(pathname, file_md)
                else:
                    #move to problem_files
                    self.mmc.move_to_problem_dir(pathname)
            else:
                # only append to self.file_events if the file isn't going to be altered by organize_new_file(). If file is going
                # to be altered by organize_new_file(), then process_IN_MOVED_TO event will handle appending it to self.file_events
                is_recorded = self.mmc.is_parent_directory(pathname, self.config.recorded_directory)
                self.file_events.append({'mode': self.config.MODE_CREATE, 'filepath': pathname, 'is_recorded_show': is_recorded})
def handle_removed_file(dir, pathname):
    logger.info("Deleting %s", pathname)
    if not dir:
        if mmc.is_audio_file(pathname):
            if pathname in self.ignore_event:
                logger.info("pathname in ignore event")
                ignore_event.remove(pathname)
            elif not self.mmc.is_parent_directory(pathname, self.config.organize_directory):
                logger.info("deleting a file not in organize")
                #we don't care if a file was deleted from the organize directory.
                file_events.append({'filepath': pathname, 'mode': self.config.MODE_DELETE})

"""
This function takes in a path name provided by the database (and its corresponding row id)
and reads the list of files in the local file system. Its purpose is to discover which files
exist on the file system but not in the database and vice versa, as well as which files have
been modified since the database was last updated. In each case, this method will call an
appropiate method to ensure that the database actually represents the filesystem.
dir_id -- row id of the directory in the cc_watched_dirs database table
dir    -- pathname of the directory
"""
def sync_database_to_filesystem(dir_id, dir,syncdb, last_ran=0):
    # TODO: is this line even necessary?
    dir = os.path.normpath(dir)+"/"
    """
    set to hold new and/or modified files. We use a set to make it ok if files are added
    twice. This is because some of the tests for new files return result sets that are not
    mutually exclusive from each other.
    """
    removed_files = set() # Not used in the original code either
    db_known_files_set = set()
    files = syncdb.id_get_files(dir_id)
    for f in files:
        db_known_files_set.add(f)
    all_files = clean_dirty_file_paths( scan_dir_for_new_files(dir) )
    all_files_set = set()
    for file_path in all_files:
        all_files_set.add(file_path[len(dir):])
    if last_ran > 0:
        """find files that have been modified since the last time media-monitor process started."""
        time_diff_sec = time.time() - last_ran
        command = find_command(directory=dir, extra_arguments=("-type f -readable -mmin -%d" % (time_diff_sec/60+1)))
    else:
        command = find_command(directory=dir, extra_arguments="-type f -readable")
    logger.debug(command)
    stdout = exec_command(command)
    if stdout is None: new_files = []
    else: new_files = stdout.splitlines()
    new_and_modified_files = set()
    for file_path in new_files:
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
    logger.info(u"Deleted files: \n%s\n\n", deleted_files_set)
    logger.info(u"New files: \n%s\n\n", new_files_set)
    logger.info(u"Modified files: \n%s\n\n", modified_files_set)
    for file_path in deleted_files_set:
        logger.debug("deleted file")
        full_file_path = os.path.join(dir, file_path)
        logger.debug(full_file_path)
        self.pe.handle_removed_file(False, full_file_path)
    for file_set, debug_message, handle_attribute in [(new_files_set, "new file", "handle_created_file"),
                                                        (modified_files_set, "modified file", "handle_modified_file")]:
        for file_path in file_set:
            logger.debug(debug_message)
            full_file_path = os.path.join(dir, file_path)
            logger.debug(full_file_path)
            if os.path.exists(full_file_path):
                getattr(self.pe,handle_attribute)(False,full_file_path, os.path.basename(full_file_path))
