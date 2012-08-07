# -*- coding: utf-8 -*-
import pyinotify
from pydispatch import dispatcher

import media.monitor.pure as mmp
from media.monitor.pure import IncludeOnly
from media.monitor.events import OrganizeFile, NewFile, MoveFile, DeleteFile, \
                                 DeleteDir, EventRegistry, MoveDir,\
                                 DeleteDirWatch
from media.monitor.log import Loggable, get_logger

# We attempt to document a list of all special cases and hacks that the
# following classes should be able to handle.
# TODO : implement all of the following special cases
#
# - Recursive directories being added to organized dirs are not handled
# properly as they only send a request for the dir and not for every file. Also
# more hacks are needed to check that the directory finished moving/copying?
#
# - In the case when a 'watched' directory's subdirectory is delete we should
# send a special request telling ApiController to delete a whole dir. This is
# done becasue pyinotify will not send an individual file delete event for
# every file in that directory
#
# - Special move events are required whenever a file is moved from a 'watched'
# directory into another 'watched' directory (or subdirectory). In this case we
# must identify the file by its md5 signature instead of it's filepath like we
# usually do. Maybe it's best to always identify a file based on its md5
# signature?. Of course that's not possible for some modification events
# because the md5 signature will change...

# Note: Because of the way classes that inherit from pyinotify.ProcessEvent
# interact with constructors. you should only instantiate objects from them
# using keyword arguments. For example:
# OrganizeListener('watch_signal') <= wrong
# OrganizeListener(signal='watch_signal') <= right

# This could easily be a module
class FileMediator(object):
    ignored_set = set([]) # for paths only
    # TODO : unify ignored and skipped.
    # for "special" conditions. could be generalized but too lazy now.
    skip_checks = set([])
    logger = get_logger()

    @staticmethod
    def is_ignored(path): return path in FileMediator.ignored_set
    @staticmethod
    def ignore(path): FileMediator.ignored_set.add(path)
    @staticmethod
    def unignore(path): FileMediator.ignored_set.remove(path)
    @staticmethod
    def skip_next(*what_to_skip,**kwargs):
        # Poor man's default arguments
        if 'key' not in kwargs: kwargs['key'] = 'maskname'
        for skip in what_to_skip:
            # standard nasty hack, too long to explain completely in comments but
            # the gist of it is:
            # 1. python's scoping rules are sometimes strange.
            # 2. workaround is very similar to what you do in javascript when
            # you write stuff like (function (x,y) { console.log(x+y); })(2,4)
            # to be avoid clobbering peoples' namespace.
            skip_check = (lambda skip: lambda v: getattr(v,kwargs['key']) == skip)(skip)
            FileMediator.skip_checks.add( skip_check )

def mediate_ignored(fn):
    def wrapped(self, event, *args,**kwargs):
        event.pathname = unicode(event.pathname, "utf-8")
        skip_events = [s_check for s_check in FileMediator.skip_checks if s_check(event)]
        for s_check in skip_events:
            FileMediator.skip_checks.remove( s_check )
            # Only process skip_checks one at a time
            return
        if FileMediator.is_ignored(event.pathname):
            FileMediator.logger.info("Ignoring: '%s' (once)" % event.pathname)
            FileMediator.unignore(event.pathname)
        else: return fn(self, event, *args, **kwargs)
    return wrapped

class BaseListener(object):
    def my_init(self, signal):
        self.signal = signal

class OrganizeListener(BaseListener, pyinotify.ProcessEvent, Loggable):
    # this class still don't handle the case where a dir was copied recursively

    def process_IN_CLOSE_WRITE(self, event): self.process_to_organize(event)
    # got cookie
    def process_IN_MOVED_TO(self, event): self.process_to_organize(event)

    def flush_events(self, path):
        """organize the whole directory at path. (pretty much by doing what
        handle does to every file"""
        flushed = 0
        for f in mmp.walk_supported(path, clean_empties=True):
            self.logger.info("Bootstrapping: File in 'organize' directory: '%s'" % f)
            dispatcher.send(signal=self.signal, sender=self, event=OrganizeFile(f))
            flushed += 1
        self.logger.info("Flushed organized directory with %d files" % flushed)

    @mediate_ignored
    @IncludeOnly(mmp.supported_extensions)
    def process_to_organize(self, event):
        dispatcher.send(signal=self.signal, sender=self, event=OrganizeFile(event))

class StoreWatchListener(BaseListener, Loggable, pyinotify.ProcessEvent):
    # TODO : must intercept DeleteDirWatch events somehow
    def process_IN_CLOSE_WRITE(self, event): self.process_create(event)
    def process_IN_MOVED_TO(self, event):
        if EventRegistry.registered(event):
            # We need this trick because we don't how to "expand" dir events
            # into file events until we know for sure if we deleted or moved
            morph = MoveDir(event) if event.dir else MoveFile(event)
            EventRegistry.matching(event).morph_into(morph)
        else: self.process_create(event)
    def process_IN_MOVED_FROM(self, event):
        # Is either delete dir or delete file
        evt = self.process_delete(event)
        if hasattr(event,'cookie'): EventRegistry.register(evt)
    def process_IN_DELETE(self,event): self.process_delete(event)
    def process_IN_MOVE_SELF(self, event):
        if '-unknown-path' in event.pathname:
            event.pathname = event.pathname.replace('-unknown-path','')
            self.delete_watch_dir(event)
    # Capturing modify events is too brittle and error prone
    # def process_IN_MODIFY(self,event): self.process_modify(event)

    def delete_watch_dir(self, event):
        e = DeleteDirWatch(event)
        dispatcher.send(signal='watch_move', sender=self, event=e)
        dispatcher.send(signal=self.signal, sender=self, event=e)
    # TODO : Remove this code. Later decided we will ignore modify events
    # since it's too difficult to tell which ones should be handled. Much
    # easier to just intercept IN_CLOSE_WRITE and decide what to do on the php
    # side
    #@mediate_ignored
    #@IncludeOnly(mmp.supported_extensions)
    #def process_modify(self, event):
        #FileMediator.skip_next('IN_MODIFY','IN_CLOSE_WRITE',key='maskname')
        #evt = ModifyFile(event)
        #dispatcher.send(signal=self.signal, sender=self, event=evt)

    @mediate_ignored
    @IncludeOnly(mmp.supported_extensions)
    def process_create(self, event):
        evt = NewFile(event)
        dispatcher.send(signal=self.signal, sender=self, event=evt)
        return evt

    @mediate_ignored
    @IncludeOnly(mmp.supported_extensions)
    def process_delete(self, event):
        evt = None
        if event.dir: evt = DeleteDir(event)
        else: evt = DeleteFile(event)
        dispatcher.send(signal=self.signal, sender=self, event=evt)
        return evt

    @mediate_ignored
    def process_delete_dir(self, event):
        evt = DeleteDir(event)
        dispatcher.send(signal=self.signal, sender=self, event=evt)
        return evt

    def flush_events(self, path):
        """
        walk over path and send a NewFile event for every file in this directory.
        Not to be confused with bootstrapping which is a more careful process that
        involved figuring out what's in the database first.
        """
        # Songs is a dictionary where every key is the watched the directory
        # and the value is a set with all the files in that directory.
        added = 0
        for f in mmp.walk_supported(path, clean_empties=False):
            added += 1
            dispatcher.send( signal=self.signal, sender=self, event=NewFile(f) )
        self.logger.info( "Flushed watch directory. added = %d" % added )

