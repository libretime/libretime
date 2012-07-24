# -*- coding: utf-8 -*-
import pyinotify
from pydispatch import dispatcher

import media.monitor.pure as mmp
from media.monitor.pure import IncludeOnly
from media.monitor.events import OrganizeFile, NewFile, DeleteFile

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


class BaseListener(object):
    def my_init(self, signal):
        self.signal = signal

class OrganizeListener(BaseListener, pyinotify.ProcessEvent):
    # this class still don't handle the case where a dir was copied recursively

    def process_IN_CLOSE_WRITE(self, event): self.process_to_organize(event)
    # got cookie
    def process_IN_MOVED_TO(self, event): self.process_to_organize(event)

    def flush_events(self, path):
        """organize the whole directory at path. (pretty much by doing what
        handle does to every file"""
        # TODO : implement me
        pass

    @IncludeOnly(mmp.supported_extensions)
    def process_to_organize(self, event):
        dispatcher.send(signal=self.signal, sender=self, event=OrganizeFile(event))

class StoreWatchListener(BaseListener, pyinotify.ProcessEvent):

    def process_IN_CLOSE_WRITE(self, event): self.process_create(event)
    def process_IN_MOVED_TO(self, event): self.process_create(event)
    def process_IN_MOVED_FROM(self, event): self.process_delete(event)
    def process_IN_DELETE(self,event): self.process_delete(event)

    @IncludeOnly(mmp.supported_extensions)
    def process_create(self, event):
        dispatcher.send(signal=self.signal, sender=self, event=NewFile(event))

    @IncludeOnly(mmp.supported_extensions)
    def process_delete(self, event):
        dispatcher.send(signal=self.signal, sender=self, event=DeleteFile(event))


