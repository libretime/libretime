from media.monitor.log import Loggable
from media.monitor.events import DeleteFile

class EventContractor(Loggable):
    def __init__(self):
        self.store = {}

    def event_registered(self, evt):
        return evt.path in self.store

    def get_old_event(self, evt):
        return self.store[ evt.path ]

    def register(self, evt):
        if self.event_registered(evt):
            old_e = self.get_old_event(evt)
            # If two events are of the same type we can safely discard the old
            # one
            if evt.__class__ == old_e.__class__:
                old_e.morph_into(evt)
            # delete overrides any other event
            elif isinstance(evt, DeleteFile):
                old_e.morph_into(evt)
        else:
            evt.add_safe_pack_hook( lambda : self.__unregister(evt) )
            self.store[ evt.path ] = evt

    # events are unregistered automatically no need to screw around with them
    def __unregister(self, evt):
        del self.store[evt.path]
