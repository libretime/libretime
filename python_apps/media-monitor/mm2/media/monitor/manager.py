import pyinotify
import time
import os
from pydispatch     import dispatcher

from os.path        import normpath
from events         import PathChannel
from log            import Loggable
from listeners      import StoreWatchListener, OrganizeListener
from handler        import ProblemFileHandler
from organizer      import Organizer
from ..saas.thread  import InstanceInheritingThread, getsig
import pure         as mmp


class ManagerTimeout(InstanceInheritingThread,Loggable):
    """ The purpose of this class is to flush the organize directory
    every 3 secnods. This used to be just a work around for cc-4235
    but recently became a permanent solution because it's "cheap" and
    reliable """
    def __init__(self, manager, interval=1.5):
        # TODO : interval should be read from config and passed here instead
        # of just using the hard coded value
        super(ManagerTimeout, self).__init__()
        self.manager  = manager
        self.interval = interval
    def run(self):
        while True:
            time.sleep(self.interval)
            self.manager.flush_organize()

class Manager(Loggable):
    # NOTE : this massive class is a source of many problems of mm and
    # is in dire need of breaking up and refactoring.
    """ An abstraction over media monitors core pyinotify functions.
    These include adding watched,store, organize directories, etc.
    Basically composes over WatchManager from pyinotify """
    def __init__(self):
        self.wm = pyinotify.WatchManager()
        # These two instance variables are assumed to be constant
        self.watch_channel    = getsig('watch')
        self.organize_channel = getsig('organize')
        self.watch_listener   = StoreWatchListener(signal = self.watch_channel)
        self.__timeout_thread = ManagerTimeout(self)
        self.__timeout_thread.daemon = True
        self.__timeout_thread.start()
        self.organize = {
            'organize_path'      : None,
            'imported_path'      : None,
            'recorded_path'      : None,
            'problem_files_path' : None,
            'organizer'          : None,
            'problem_handler'    : None,
            'organize_listener'  : OrganizeListener(signal=
                self.organize_channel),
        }
        def dummy(sender, event): self.watch_move( event.path, sender=sender )
        dispatcher.connect(dummy, signal=getsig('watch_move'), 
                sender=dispatcher.Any, weak=False)
        def subwatch_add(sender, directory):
            self.__add_watch(directory, self.watch_listener)
        dispatcher.connect(subwatch_add, signal=getsig('add_subwatch'),
                sender=dispatcher.Any, weak=False)
        # A private mapping path => watch_descriptor
        # we use the same dictionary for organize, watch, store wd events.
        # this is a little hacky because we are unable to have multiple wd's
        # on the same path.
        self.__wd_path = {}
        # The following set isn't really necessary anymore. Should be
        # removed...
        self.watched_directories = set([])

    # This is the only event that we are unable to process "normally". I.e.
    # through dedicated handler objects. Because we must have access to a
    # manager instance. Hence we must slightly break encapsulation.
    def watch_move(self, watch_dir, sender=None):
        """ handle 'watch move' events directly sent from listener """
        self.logger.info("Watch dir '%s' has been renamed (hence removed)" %
                watch_dir)
        self.remove_watch_directory(normpath(watch_dir))

    def watch_signal(self):
        """ Return the signal string our watch_listener is reading
        events from """
        return getsig(self.watch_listener.signal)

    def __remove_watch(self,path):
        """ Remove path from being watched (first will check if 'path'
        is watched) """
        # only delete if dir is actually being watched
        if path in self.__wd_path:
            wd = self.__wd_path[path]
            self.wm.rm_watch(wd, rec=True)
            del(self.__wd_path[path])

    def __add_watch(self,path,listener):
        """ Start watching 'path' using 'listener'. First will check if
        directory is being watched before adding another watch """

        self.logger.info("Attempting to add listener to path '%s'" % path)
        self.logger.info( 'Listener: %s' % str(listener) )

        if not self.has_watch(path):
            wd = self.wm.add_watch(path, pyinotify.ALL_EVENTS, rec=True,
                    auto_add=True, proc_fun=listener)
            if wd: self.__wd_path[path] = wd.values()[0]

    def __create_organizer(self, target_path, recorded_path):
        """ creates an organizer at new destination path or modifies the
        old one """
        # TODO : find a proper fix for the following hack
        # We avoid creating new instances of organize because of the way
        # it interacts with pydispatch. We must be careful to never have
        # more than one instance of OrganizeListener but this is not so
        # easy. (The singleton hack in Organizer) doesn't work. This is
        # the only thing that seems to work.
        if self.organize['organizer']:
            o               = self.organize['organizer']
            o.channel       = self.organize_channel
            o.target_path   = target_path
            o.recorded_path = recorded_path
        else:
            self.organize['organizer'] = Organizer(channel=
                    self.organize_channel, target_path=target_path,
                    recorded_path=recorded_path)

    def get_problem_files_path(self):
        """ returns the path where problem files should go """
        return self.organize['problem_files_path']

    def set_problem_files_path(self, new_path):
        """ Set the path where problem files should go """
        self.organize['problem_files_path'] = new_path
        self.organize['problem_handler'] = \
            ProblemFileHandler( PathChannel(signal=getsig('badfile'),
                path=new_path) )

    def get_recorded_path(self):
        """ returns the path of the recorded directory """
        return self.organize['recorded_path']

    def set_recorded_path(self, new_path):
        self.__remove_watch(self.organize['recorded_path'])
        self.organize['recorded_path'] = new_path
        self.__create_organizer( self.organize['imported_path'], new_path)
        self.__add_watch(new_path, self.watch_listener)

    def get_organize_path(self):
        """ returns the current path that is being watched for
        organization """
        return self.organize['organize_path']

    def set_organize_path(self, new_path):
        """ sets the organize path to be new_path. Under the current
        scheme there is only one organize path but there is no reason
        why more cannot be supported """
        # if we are already organizing a particular directory we remove the
        # watch from it first before organizing another directory
        self.__remove_watch(self.organize['organize_path'])
        self.organize['organize_path'] = new_path
        # the OrganizeListener instance will walk path and dispatch an organize
        # event for every file in that directory
        self.organize['organize_listener'].flush_events(new_path)
        #self.__add_watch(new_path, self.organize['organize_listener'])

    def flush_organize(self):
        path = self.organize['organize_path']
        self.organize['organize_listener'].flush_events(path)

    def get_imported_path(self):
        return self.organize['imported_path']

    def set_imported_path(self,new_path):
        """ set the directory where organized files go to. """
        self.__remove_watch(self.organize['imported_path'])
        self.organize['imported_path'] = new_path
        self.__create_organizer( new_path, self.organize['recorded_path'])
        self.__add_watch(new_path, self.watch_listener)

    def change_storage_root(self, store):
        """ hooks up all the directories for you. Problem, recorded,
        imported, organize. """
        store_paths = mmp.expand_storage(store)
        # First attempt to make sure that all paths exist before adding any
        # watches
        for path_type, path in store_paths.iteritems():
            try: mmp.create_dir(path)
            except mmp.FailedToCreateDir as e: self.unexpected_exception(e)

        os.chmod(store_paths['organize'], 0775) 

        self.set_problem_files_path(store_paths['problem_files'])
        self.set_imported_path(store_paths['imported'])
        self.set_recorded_path(store_paths['recorded'])
        self.set_organize_path(store_paths['organize'])

    def has_watch(self, path):
        """ returns true if the path is being watched or not. Any kind
        of watch: organize, store, watched. """
        return path in self.__wd_path

    def add_watch_directory(self, new_dir):
        """ adds a directory to be "watched". "watched" directories are
        those that are being monitored by media monitor for airtime in
        this context and not directories pyinotify calls watched """
        if self.has_watch(new_dir):
            self.logger.info("Cannot add '%s' to watched directories. It's \
                    already being watched" % new_dir)
        else:
            self.logger.info("Adding watched directory: '%s'" % new_dir)
            self.__add_watch(new_dir, self.watch_listener)

    def remove_watch_directory(self, watch_dir):
        """ removes a directory from being "watched". Undoes
        add_watch_directory """
        if self.has_watch(watch_dir):
            self.logger.info("Removing watched directory: '%s'", watch_dir)
            self.__remove_watch(watch_dir)
        else:
            self.logger.info("'%s' is not being watched, hence cannot be \
                    removed" % watch_dir)
            self.logger.info("The directories we are watching now are:")
            self.logger.info( self.__wd_path )

    def loop(self):
        """ block until we receive pyinotify events """
        notifier = pyinotify.Notifier(self.wm)
        notifier.coalesce_events()
        notifier.loop()
        #notifier = pyinotify.ThreadedNotifier(self.wm, read_freq=1)
        #notifier.coalesce_events()
        #notifier.start()
        #return notifier
        #import asyncore
        #notifier = pyinotify.AsyncNotifier(self.wm)
        #asyncore.loop()
