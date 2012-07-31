# -*- coding: utf-8 -*-
import os
from media.monitor.log import Loggable

class SyncDB(Loggable):
    def __init__(self, apc):
        self.apc = apc
        dirs = self.apc.list_all_watched_dirs()

        directories = None
        try:
            directories = dirs['dirs']
        except KeyError as e:
            self.logger.error("Could not find index 'dirs' in dictionary: %s", str(dirs))
            self.logger.error(e)
            raise
        # self.directories is a dictionary where a key is the directory and the
        # value is the directory's id in the db
        self.directories = dict( (v,k) for k,v in directories.iteritems() )
        # Just in case anybody wants to lookup a directory by its id we haev
        self.id_lookup = directories

    def reload_directories(self):
        # dirs_setup is a dict with keys:
        # u'watched_dirs' and u'stor' which point to lists of corresponding
        # dirs
        dirs_setup = self.apc.setup_media_monitor()
        self.base_storage = dirs_setup[u'stor']
        self.watched_directories = set(dirs_setup[u'watched_dirs'])

    def organize_path(self): return os.path.join(self.base_storage, 'organize')
    def problem_path(self): return os.path.join(self.base_storage, 'problem_files')
    def import_path(self): return os.path.join(self.base_storage, 'imported')
    def recorded_path(self): return os.path.join(self.base_storage, 'recorded')

    def list_directories(self):
        """
        returns a list of all the watched directories in the datatabase.
        (Includes the imported directory)
        """
        return self.directories.keys()

    def directory_get_files(self, directory):
        """
        returns all the files(recursively) in a directory. a directory is an "actual" directory
        path instead of its id.
        """
        return set( [ os.path.normpath(os.path.join(directory,f)) \
                for f in self.apc.list_all_db_files(self.directories[directory]) ] )

    def id_get_files(self, dir_id):
        """
        returns all the files given some dir_id. this method is here for "symmetry". it's not actually used anywhere
        """
        return self.directory_get_files(self.id_lookup[dir_id])


