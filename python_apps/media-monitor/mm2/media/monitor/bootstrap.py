import os
from pydispatch       import dispatcher
from events           import NewFile, DeleteFile, ModifyFile
from log              import Loggable
from ..saas.thread    import getsig
import pure as mmp

class Bootstrapper(Loggable):
    """
    Bootstrapper reads all the info in the filesystem flushes organize events
    and watch events
    """
    def __init__(self,db,watch_signal):
        """
        db - AirtimeDB object; small layer over api client
        last_ran - last time the program was ran.
        watch_signal - the signals should send events for every file on.
        """
        self.db           = db
        self.watch_signal = getsig(watch_signal)

    def flush_all(self, last_ran):
        """
        bootstrap every single watched directory. only useful at startup note
        that because of the way list_directories works we also flush the import
        directory as well I think
        """
        for d in self.db.list_storable_paths(): self.flush_watch(d, last_ran)

    def flush_watch(self, directory, last_ran, all_files=False):
        """
        flush a single watch/imported directory. useful when wanting to to
        rescan, or add a watched/imported directory
        """
        songs = set([])
        added = modded = deleted = 0
        for f in mmp.walk_supported(directory, clean_empties=False):
            songs.add(f)
            # We decide whether to update a file's metadata by checking its
            # system modification date. If it's above the value self.last_ran
            # which is passed to us that means media monitor wasn't aware when
            # this changes occured in the filesystem hence it will send the
            # correct events to sync the database with the filesystem
            if os.path.getmtime(f) > last_ran:
                modded += 1
                dispatcher.send(signal=self.watch_signal, sender=self,
                        event=ModifyFile(f))
        db_songs = set(( song for song in self.db.directory_get_files(directory,
            all_files)
            if mmp.sub_path(directory,song) ))
        # Get all the files that are in the database but in the file
        # system. These are the files marked for deletions
        for to_delete in db_songs.difference(songs):
            dispatcher.send(signal=self.watch_signal, sender=self,
                            event=DeleteFile(to_delete))
            deleted += 1
        for to_add in songs.difference(db_songs):
            dispatcher.send(signal=self.watch_signal, sender=self,
                            event=NewFile(to_add))
            added += 1
        self.logger.info( "Flushed watch directory (%s). \
                (added, modified, deleted) = (%d, %d, %d)"
                % (directory, added, modded, deleted) )
