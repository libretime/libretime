# -*- coding: utf-8 -*-
import os
from log            import Loggable
from exceptions     import NoDirectoryInAirtime
from ..saas.thread  import user
from os.path        import normpath, join
import pure         as mmp

class AirtimeDB(Loggable):
    def __init__(self, apc, reload_now=True):
        self.apc = apc
        if reload_now: self.reload_directories()

    def reload_directories(self):
        """ this is the 'real' constructor, should be called if you ever
        want the class reinitialized. there's not much point to doing
        it yourself however, you should just create a new AirtimeDB
        instance. """

        saas = user().root_path

        try:
            # dirs_setup is a dict with keys:
            # u'watched_dirs' and u'stor' which point to lists of corresponding
            # dirs
            dirs_setup = self.apc.setup_media_monitor()
            dirs_setup[u'stor'] = normpath( join(saas, dirs_setup[u'stor'] ) )
            dirs_setup[u'watched_dirs'] = map(lambda p: normpath(join(saas,p)),
                dirs_setup[u'watched_dirs'])
            dirs_with_id = dict([ (k,normpath(v)) for k,v in
                self.apc.list_all_watched_dirs()['dirs'].iteritems() ])

            self.id_to_dir = dirs_with_id
            self.dir_to_id = dict([ (v,k) for k,v in dirs_with_id.iteritems() ])

            self.base_storage = dirs_setup[u'stor']
            self.storage_paths = mmp.expand_storage( self.base_storage )
            self.base_id = self.dir_to_id[self.base_storage]

            # hack to get around annoying schema of airtime db
            self.dir_to_id[ self.recorded_path() ] = self.base_id
            self.dir_to_id[ self.import_path() ] = self.base_id

            # We don't know from the x_to_y dict which directory is watched or
            # store...
            self.watched_directories = set([ os.path.normpath(p) for p in
                dirs_setup[u'watched_dirs'] ])
        except Exception, e:
            self.logger.info(str(e))


    def to_id(self, directory):
        """ directory path -> id """
        return self.dir_to_id[ directory ]

    def to_directory(self, dir_id):
        """ id -> directory path """
        return self.id_to_dir[ dir_id ]

    def storage_path(self)  : return self.base_storage
    def organize_path(self) : return self.storage_paths['organize']
    def problem_path(self)  : return self.storage_paths['problem_files']
    def import_path(self)   : return self.storage_paths['imported']
    def recorded_path(self) : return self.storage_paths['recorded']

    def list_watched(self):
        """ returns all watched directories as a list """
        return list(self.watched_directories)

    def list_storable_paths(self):
        """ returns a list of all the watched directories in the
        datatabase. (Includes the imported directory and the recorded
        directory) """
        l = self.list_watched()
        l.append(self.import_path())
        l.append(self.recorded_path())
        return l

    def dir_id_get_files(self, dir_id, all_files=True):
        """ Get all files in a directory with id dir_id """
        base_dir = self.id_to_dir[ dir_id ]
        return set(( join(base_dir,p) for p in
            self.apc.list_all_db_files( dir_id, all_files ) ))

    def directory_get_files(self, directory, all_files=True):
        """ returns all the files(recursively) in a directory. a
        directory is an "actual" directory path instead of its id. This
        is super hacky because you create one request for the recorded
        directory and one for the imported directory even though they're
        the same dir in the database so you get files for both dirs in 1
        request... """
        normal_dir = os.path.normpath(unicode(directory))
        if normal_dir not in self.dir_to_id:
            raise NoDirectoryInAirtime( normal_dir, self.dir_to_id )
        all_files = self.dir_id_get_files( self.dir_to_id[normal_dir],
                all_files )
        if normal_dir == self.recorded_path():
            all_files = [ p for p in all_files if
                    mmp.sub_path( self.recorded_path(), p ) ]
        elif normal_dir == self.import_path():
            all_files = [ p for p in all_files if
                    mmp.sub_path( self.import_path(), p ) ]
        elif normal_dir == self.storage_path():
            self.logger.info("Warning, you're getting all files in '%s' which \
                    includes imported + record" % normal_dir)
        return set(all_files)


