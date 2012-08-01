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
    global_inst = None
    all_signals = set(['add_watch', 'remove_watch'])
    def __init__(self):
        self.wm = pyinotify.WatchManager()
        # These two instance variables are assumed to be constant
        self.watch_channel = 'watch'
        self.organize_channel = 'organize'
        self.watch_listener = StoreWatchListener(signal=self.watch_channel)
        self.organize = {
            'organize_path' : None,
            'store_path' : None,
            # This guy doesn't need to be changed, always the same.
            # Gets hooked by wm to different directories
            'organize_listener' : OrganizeListener(signal=self.organize_channel),
            # Also stays the same as long as its target, the directory
            # which the "organized" files go to, isn't changed.
            'organizer' : None,
        }
        # A private mapping path => watch_descriptor
        # we use the same dictionary for organize, watch, store wd events.
        # this is a little hacky because we are unable to have multiple wd's
        # on the same path.
        self.__wd_path = {}
        # The following set isn't really necessary anymore. should be
        # removed...
        self.watched_directories = set([])
        Manager.global_inst = self

    def watch_signal(self):
        return self.watch_listener.self.signal

    def __remove_watch(self,path):
        if path in self.__wd_path: # only delete if dir is actually being watched
            wd = self.__wd_path[path]
            self.wm.rm_watch(wd, rec=True)
            del(self.__wd_path[path])

    def __add_watch(self,path,listener):
        wd = self.wm.add_watch(path, pyinotify.ALL_EVENTS, rec=True, auto_add=True,
                               proc_fun=listener)
        self.__wd_path[path] = wd.values()[0]

    def __create_organizer(self, target_path):
        """
        private constructor for organizer so that we don't have to repeat
        adding the channel/signal as a parameter to the original constructor
        every time
        """
        return Organizer(channel=self.organize_channel,target_path=target_path)

    def get_organize_path(self):
        """
        returns the current path that is being watched for organization
        """
        return self.organize['organize_path']

    def set_organize_path(self, new_path):
        """
        sets the organize path to be new_path. Under the current scheme there is
        only one organize path but there is no reason why more cannot be supported
        """
        # if we are already organizing a particular directory we remove the
        # watch from it first before organizing another directory
        self.__remove_watch(self.organize['organize_path'])
        self.organize['organize_path'] = new_path
        # the OrganizeListener instance will walk path and dispatch an organize
        # event for every file in that directory
        self.organize['organize_listener'].flush_events(new_path)
        self.__add_watch(new_path, self.organize['organize_listener'])

    organize_path = property(get_organize_path, set_organize_path)

    def get_store_path(self):
        """
        returns the store_path (should be accessed through the property usually)
        """
        return self.organize['store_path']

    def set_store_path(self,new_path):
        """
        set the directory where organized files go to
        """
        self.__remove_watch(self.organize['store_path'])
        self.organize['store_path'] = new_path
        self.organize['organizer'] = self.__create_organizer(new_path)
        # flush all the files in the new store_directory. this is done so that
        # new files are added to the database. Note that we are not responsible
        # for removing songs in the old store directory from the database
        # we assume that this is already done for us.
        # self.watch_listener.flush_events(new_path)
        self.__add_watch(new_path, self.watch_listener)

    store_path = property(get_store_path, set_store_path)

    def has_watch(self, path):
        """
        returns true if the path is being watched or not. Any kind of watch:
        organize, store, watched.
        """
        return path in self.__wd_path

    def add_watch_directory(self, new_dir):
        """
        adds a directory to be "watched". "watched" directories are those that
        are being monitored by media monitor for airtime in this context and
        not directories pyinotify calls watched
        """
        if self.has_watch(new_dir):
            self.logger.info("Cannot add '%s' to watched directories. It's \
                    already being watched" % new_dir)
        else:
            self.logger.info("Adding watched directory: '%s'" % new_dir)
            self.__add_watch(new_dir, self.watch_listener)

    def remove_watch_directory(self, watch_dir):
        """
        removes a directory from being "watched". Undoes add_watch_directory
        """
        if self.has_watch(watch_dir):
            self.logger.info("Removing watched directory: '%s'", watch_dir)
            self.__remove_watch(watch_dir)
        else:
            self.logger.info("'%s' is not being watched, hence cannot be removed"
                             % watch_dir)

    def pyinotify(self):
        return pyinotify.Notifier(self.wm)


    def loop(self):
        """
        block until we receive pyinotify events
        """
        pyinotify.Notifier(self.wm).loop()
        # Experiments with running notifier in different modes
        # There are 3 options: normal, async, threaded.
        #import asyncore
        #pyinotify.AsyncNotifier(self.wm).loop()
        #asyncore.loop()
