from log    import Loggable
from events import DeleteFile

class EventContractor(Loggable):
    def __init__(self):
        self.store = {}
    def event_registered(self, evt):
        """
        returns true if the event is registered which means that there is
        another "unpacked" event somewhere out there with the same path
        """
        return evt.path in self.store

    def get_old_event(self, evt):
        """
        get the previously registered event with the same path as 'evt'
        """
        return self.store[ evt.path ]

    def register(self, evt):
        if self.event_registered(evt):
            ev_proxy = self.get_old_event(evt)
            if ev_proxy.same_event(evt):
                ev_proxy.merge_proxy(evt)
                return False
            # delete overrides any other event
            elif evt.is_event(DeleteFile):
                ev_proxy.merge_proxy(evt)
                return False
            else:
                ev_proxy.run_hook()
                ev_proxy.reset_hook()

        self.store[ evt.path ] = evt
        evt.set_pack_hook( lambda : self.__unregister(evt) )
        return True

    def __unregister(self, evt):
        del self.store[evt.path]

