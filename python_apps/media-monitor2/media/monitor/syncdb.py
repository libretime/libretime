# -*- coding: utf-8 -*-
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

    def list_directories(self):
        return self.directories.keys()

    def directory_get_files(self, directory):
        print("trying to access dir id: %s" % self.directories[directory])
        self.apc.list_all_db_files(self.directories[directory])


