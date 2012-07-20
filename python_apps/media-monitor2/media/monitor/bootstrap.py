import os
from pydispatch import dispatcher
from media.monitor.events import OrganizeFile, NewFile, DeleteFile
from media.monitor.log import Loggable
import media.monitor.pure as mmp

class Bootstrapper(Loggable):
    """
    Bootstrapper reads all the info in the filesystem flushes organize
    events and watch events
    """
    def __init__(self,db,last_ran,org_channels,watch_channels):
        self.db = db
        self.org_channels = org_channels
        self.watch_channels = watch_channels
        self.last_ran = last_ran

    def flush_organize(self):
        """
        walks the organize directories and sends an organize event for every file manually
        """
        flushed = 0
        for pc in self.org_channels:
            for f in mmp.walk_supported(pc.path, clean_empties=True):
                self.logger.info("Bootstrapping: File in 'organize' directory: '%s'" % f)
                dispatcher.send(signal=pc.signal, sender=self, event=OrganizeFile(f))
                flushed += 1
        self.logger.info("Flushed organized directory with %d files" % flushed)

    def flush_watch(self):
        """
        Syncs the file system into the database. Walks over deleted/new/modified files since
        the last run in mediamonitor and sends requests to make the database consistent with
        file system
        """
        songs = set()
        modded = deleted = 0
        for pc in self.watch_channels:
            for f in mmp.walk_supported(pc.path, clean_empties=False):
                songs.add(f)
                # We decide whether to update a file's metadata by checking
                # its system modification date. If it's above the value
                # self.last_run which is passed to us that means media monitor
                # wasn't aware when this changes occured in the filesystem
                # hence it will send the correct events to sync the database
                # with the filesystem
                if os.path.getmtime(f) > self.last_ran:
                    modded += 1
                    dispatcher.send(signal=pc.signal, sender=self, event=DeleteFile(f))
                    dispatcher.send(signal=pc.signal, sender=self, event=NewFile(f))
        # Want all files in the database that are not in the filesystem
        for to_delete in self.db.exclude(songs):
            for pc in self.watch_channels:
                if os.path.commonprefix([pc.path, to_delete]) == pc.path:
                    dispatcher.send(signal=pc.signal, sender=self, event=DeleteFile(f))
                    os.remove(to_delete)
                    deleted += 1
                    break
            else:
                self.logger.info("Error, could not find watch directory of would be deleted \
                                  file '%s'" % to_delete)
        self.logger.info("Flushed watch directories. (modified, deleted) = (%d, %d)"
                         % (modded, deleted) )


