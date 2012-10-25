# -*- coding: utf-8 -*-
import pyinotify
from pydispatch import dispatcher

import media.monitor.pure as mmp
from media.monitor.pure import IncludeOnly
from media.monitor.events import OrganizeFile, NewFile, MoveFile, DeleteFile, \
                                 DeleteDir, EventRegistry, MoveDir,\
                                 DeleteDirWatch
from media.monitor.log import Loggable, get_logger

# Note: Because of the way classes that inherit from pyinotify.ProcessEvent
# interact with constructors. you should only instantiate objects from them
# using keyword arguments. For example:
# OrganizeListener('watch_signal') <= wrong
# OrganizeListener(signal='watch_signal') <= right

class FileMediator(object):
    """
    FileMediator is used an intermediate mechanism that filters out certain
    events.
    """
    ignored_set = set([]) # for paths only
    logger = get_logger()

    @staticmethod
    def is_ignored(path): return path in FileMediator.ignored_set
    @staticmethod
    def ignore(path): FileMediator.ignored_set.add(path)
    @staticmethod
    def unignore(path): FileMediator.ignored_set.remove(path)

def mediate_ignored(fn):
    def wrapped(self, event, *args,**kwargs):
        event.pathname = unicode(event.pathname, "utf-8")
        if FileMediator.is_ignored(event.pathname):
            FileMediator.logger.info("Ignoring: '%s' (once)" % event.pathname)
            FileMediator.unignore(event.pathname)
        else: return fn(self, event, *args, **kwargs)
    return wrapped

class BaseListener(object):
    def __str__(self):
        return "Listener(%s), Signal(%s)" % \
                (self.__class__.__name__, self.  signal)
    def my_init(self, signal): self.signal = signal

class OrganizeListener(BaseListener, pyinotify.ProcessEvent, Loggable):
    def process_IN_CLOSE_WRITE(self, event):
        #self.logger.info("===> handling: '%s'" % str(event))
        self.process_to_organize(event)

    def process_IN_MOVED_TO(self, event):
        #self.logger.info("===> handling: '%s'" % str(event))
        self.process_to_organize(event)

    def flush_events(self, path):
        """
        organize the whole directory at path. (pretty much by doing what
        handle does to every file
        """
        flushed = 0
        for f in mmp.walk_supported(path, clean_empties=True):
            self.logger.info("Bootstrapping: File in 'organize' directory: \
                    '%s'" % f)
            if not mmp.file_locked(f):
                dispatcher.send(signal=self.signal, sender=self,
                        event=OrganizeFile(f))
            flushed += 1
        #self.logger.info("Flushed organized directory with %d files" % flushed)

    @IncludeOnly(mmp.supported_extensions)
    def process_to_organize(self, event):
        dispatcher.send(signal=self.signal, sender=self,
                event=OrganizeFile(event))

class StoreWatchListener(BaseListener, Loggable, pyinotify.ProcessEvent):
    def process_IN_CLOSE_WRITE(self, event):
        self.process_create(event)
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
        # evt can be none whenever event points that a file that would be
        # ignored by @IncludeOnly
        if hasattr(event,'cookie') and (evt != None):
            EventRegistry.register(evt)
    def process_IN_DELETE(self,event): self.process_delete(event)
    def process_IN_MOVE_SELF(self, event):
        if '-unknown-path' in event.pathname:
            event.pathname = event.pathname.replace('-unknown-path','')
            self.delete_watch_dir(event)

    def delete_watch_dir(self, event):
        e = DeleteDirWatch(event)
        dispatcher.send(signal='watch_move', sender=self, event=e)
        dispatcher.send(signal=self.signal, sender=self, event=e)

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
        if event.dir : evt = DeleteDir(event)
        else         : evt = DeleteFile(event)
        dispatcher.send(signal=self.signal, sender=self, event=evt)
        return evt

    @mediate_ignored
    def process_delete_dir(self, event):
        evt = DeleteDir(event)
        dispatcher.send(signal=self.signal, sender=self, event=evt)
        return evt

    def flush_events(self, path):
        """
        walk over path and send a NewFile event for every file in this
        directory.  Not to be confused with bootstrapping which is a more
        careful process that involved figuring out what's in the database
        first.
        """
        # Songs is a dictionary where every key is the watched the directory
        # and the value is a set with all the files in that directory.
        added = 0
        for f in mmp.walk_supported(path, clean_empties=False):
            added += 1
            dispatcher.send( signal=self.signal, sender=self, event=NewFile(f) )
        self.logger.info( "Flushed watch directory. added = %d" % added )

