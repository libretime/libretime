import os
from pydispatch import dispatcher
from media.monitor.events import NewFile, DeleteFile
from media.monitor.log import Loggable
import media.monitor.pure as mmp

class Bootstrapper(Loggable):
    """
    Bootstrapper reads all the info in the filesystem flushes organize
    events and watch events
    """
    def __init__(self,db,watch_signal):
        """
        db - SyncDB object; small layer over api client
        last_ran - last time the program was ran.
        watch_signal - the signals should send events for every file on.
        """
        self.db = db
        self.watch_signal = watch_signal

    def flush_all(self, last_ran):
        """
        bootstrap every single watched directory. only useful at startup
        """
        for d in self.db.list_directories():
            self.flush_watch(d, last_ran)

    def flush_watch(self, directory, last_ran):
        """
        flush a single watch/imported directory. useful when wanting to to rescan,
        or add a watched/imported directory
        """
        songs = set([])
        modded = deleted = 0
        for f in mmp.walk_supported(directory, clean_empties=False):
            songs.add(f)
            # We decide whether to update a file's metadata by checking
            # its system modification date. If it's above the value
            # self.last_ran which is passed to us that means media monitor
            # wasn't aware when this changes occured in the filesystem
            # hence it will send the correct events to sync the database
            # with the filesystem
            if os.path.getmtime(f) > last_ran:
                modded += 1
                dispatcher.send(signal=self.watch_signal, sender=self, event=DeleteFile(f))
                dispatcher.send(signal=self.watch_signal, sender=self, event=NewFile(f))
        db_songs = self.db.directory_get_files(directory)
        # Get all the files that are in the database but in the file
        # system. These are the files marked for deletions
        for to_delete in db_songs.difference(songs):
            dispatcher.send(signal=self.watch_signal, sender=self, event=DeleteFile(to_delete))
            deleted += 1
        self.logger.info( "Flushed watch directories. (modified, deleted) = (%d, %d)"
                        % (modded, deleted) )


