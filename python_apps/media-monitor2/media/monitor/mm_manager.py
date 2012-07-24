import pyinotify

from media.monitor.log import Loggable
from media.monitor.listeners import StoreWatchListener, OrganizeListener
from media.monitor.organizer import Organizer

class Manager(Loggable):
    """
    An abstraction over media monitors core pyinotify functions. These include
    adding watched,store, organize directories, etc. Basically composes over
    WatchManager from pyinotify
    """
    all_signals = set(['add_watch', 'remove_watch'])
    # TODO : get rid of config object being passed? It's not actually being
    # used
    def __init__(self,config):

        self.wm = pyinotify.WatchManager()
        # These two instance variables are assumed to be constant
        self.watch_channel = 'watch'
        self.organize_channel = 'organize'

        self.watch_listener = StoreWatchListener(self.watch_channel)

        self.organize = {
            'organize_path' : None,
            'store_path' : None,
            # This guy doesn't need to be changed, always the same.
            # Gets hooked by wm to different directories
            'organize_listener' : OrganizeListener(self.organize_channel),
            # Also stays the same as long as its target, the directory
            # which the "organized" files go to, isn't changed.
            'organizer' : None,
        }

        self.watched_directories = set([])

    def __remove_watch(self,path):
        old_watch = self.wm.get_wd(path)
        if old_watch: # only delete if dir is actually being watched
            self.rm_watch(old_watch, rec=True)

    def __create_organizer(self, target_path):
        return Organizer(channel=self.organize_channel,target_path=target_path)

    def set_organize_path(self, new_path):
        # if we are already organizing a particular directory we remove the
        # watch from it first before organizing another directory
        self.__remove_watch(self.organize['organize_path'])
        self.organize['organize_path'] = new_path
        # the OrganizeListener instance will walk path and dispatch an organize
        # event for every file in that directory
        self.organize['organize_listener'].flush_events(new_path)
        self.wm.add_watch(new_path, pyinotify.ALL_EVENTS, rec=True, auto_add=True,
                proc_fun=self.organize['organize_listener'])

    def set_store_path(self,new_path):
        """set the directory where organized files go to"""
        self.__remove_watch(self.organize['store_path'])
        self.organize['store_path'] = new_path
        self.organize['organizer'] = self.__create_organizer(new_path)
        # flush all the files in the new store_directory. this is done so that
        # new files are added to the database. Note that we are not responsible
        # for removing songs in the old store directory from the database
        # we assume that this is already done for us.
        self.watch_listener.flush_events(new_path)
        self.wm.add_watch(new_path, pyinotify.ALL_EVENTS, rec=True, auto_add=True,
                proc_fun=self.watch_listener)

    def add_watch_directory(self, new_dir):
        if new_dir in self.watched_directories:
            self.logger.info("Cannot add '%s' to watched directories. It's \
                    already being watched" % new_dir)
        else:
            self.logger.info("Adding watched directory: '%s'" % new_dir)
            self.watched_directories.add(new_dir)
            self.wm.add_watch(new_dir, pyinotify.ALL_EVENTS, rec=True, auto_add=True,
                    proc_fun=self.watch_listener)

    def remove_watch_directory(self, watch_dir):
        if watch_dir in self.watched_directories:
            self.watched_directories.remove(watch_dir)
            self.logger.info("Removing watched directory: '%s'", watch_dir)
            self.__remove_watch(watch_dir)
        else:
            self.logger.info("'%s' is not being watched, henced cannot be removed"
                    % watch_dir)

    def loop(self):
        pyinotify.Notifier(self.wm).loop()
