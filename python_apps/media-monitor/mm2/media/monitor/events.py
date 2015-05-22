# -*- coding: utf-8 -*-
import os
import abc
import re
import pure         as mmp
from pure           import LazyProperty
from metadata       import Metadata
from log            import Loggable
from exceptions     import BadSongFile
from ..saas.thread  import getsig, user

class PathChannel(object):
    """ Simple struct to hold a 'signal' string and a related 'path'.
    Basically used as a named tuple """
    def __init__(self, signal, path):
        self.signal = getsig(signal)
        self.path   = path

# TODO : Move this to it's file. Also possible unsingleton and use it as a
# simple module just like m.m.owners
class EventRegistry(object):
    """ This class's main use is to keep track all events with a cookie
    attribute. This is done mainly because some events must be 'morphed'
    into other events because we later detect that they are move events
    instead of delete events. """
    def __init__(self):
        self.registry = {}
    def register(self,evt): self.registry[evt.cookie] = evt
    def unregister(self,evt): del self.registry[evt.cookie]
    def registered(self,evt): return evt.cookie in self.registry
    def matching(self,evt):
        event = self.registry[evt.cookie]
        # Want to disallow accessing the same event twice
        self.unregister(event)
        return event

class EventProxy(Loggable):
    """ A container object for instances of BaseEvent (or it's
    subclasses) used for event contractor """
    def __init__(self, orig_evt):
        self.orig_evt   = orig_evt
        self.evt        = orig_evt
        self.reset_hook()
        if hasattr(orig_evt, 'path'): self.path = orig_evt.path

    def set_pack_hook(self, l):
        self._pack_hook = l

    def reset_hook(self):
        self._pack_hook = lambda : None

    def run_hook(self):
        self._pack_hook()

    def safe_pack(self):
        self.run_hook()
        # make sure that cleanup hook is never called twice for the same event
        self.reset_hook()
        return self.evt.safe_pack()

    def merge_proxy(self, proxy):
        self.evt = proxy.evt

    def is_event(self, real_event):
        return isinstance(self.evt, real_event)

    def same_event(self, proxy):
        return self.evt.__class__ == proxy.evt.__class__


class HasMetaData(object):
    """ Any class that inherits from this class gains the metadata
    attribute that loads metadata from the class's 'path' attribute.
    This is done lazily so there is no performance penalty to inheriting
    from this and subsequent calls to metadata are cached """
    __metaclass__ = abc.ABCMeta
    @LazyProperty
    def metadata(self): return Metadata(self.path)

class BaseEvent(Loggable):
    __metaclass__ = abc.ABCMeta
    def __init__(self, raw_event):
        # TODO : clean up this idiotic hack
        # we should use keyword constructors instead of this behaviour checking
        # bs to initialize BaseEvent
        if hasattr(raw_event,"pathname"):
            self._raw_event = raw_event
            self.path = os.path.normpath(raw_event.pathname)
        else: self.path = raw_event
        self.owner = user().owner.get_owner(self.path)
        owner_re = re.search('stor/imported/(?P<owner>\d+)/', self.path)
        if owner_re: 
            self.logger.info("matched path: %s" % self.path)
            self.owner = owner_re.group('owner')
        else:
            self.logger.info("did not match path: %s" % self.path)
        self._pack_hook = lambda: None # no op
        # into another event

    # TODO : delete this method later
    def reset_hook(self):
        """ Resets the hook that is called after an event is packed.
        Before resetting the hook we execute it to make sure that
        whatever cleanup operations were queued are executed. """
        self._pack_hook()
        self._pack_hook = lambda: None

    def exists(self): return os.path.exists(self.path)

    @LazyProperty
    def cookie(self): return getattr( self._raw_event, 'cookie', None )

    def __str__(self):
        return "Event(%s). Path(%s)" % ( self.path, self.__class__.__name__)

    # TODO : delete this method later
    def add_safe_pack_hook(self,k):
        """ adds a callable object (function) that will be called after
        the event has been "safe_packed" """
        self._pack_hook = k

    def proxify(self):
        return EventProxy(self)

    # As opposed to unsafe_pack...
    def safe_pack(self):
        """ returns exceptions instead of throwing them to be consistent
        with events that must catch their own BadSongFile exceptions
        since generate a set of exceptions instead of a single one """
        try:
            self._pack_hook()
            ret = self.pack()
            # Remove owner of this file only after packing. Otherwise packing
            # will not serialize the owner correctly into the airtime request
            user().owner.remove_file_owner(self.path)
            return ret
        except BadSongFile as e: return [e]
        except Exception as e:
            self.unexpected_exception(e)
            return[e]

    # nothing to see here, please move along
    def morph_into(self, evt):
        self.logger.info("Morphing %s into %s" % ( str(self), str(evt) ) )
        self._raw_event   = evt._raw_event
        self.path         = evt.path
        self.__class__    = evt.__class__
        # Clean up old hook and transfer the new events hook
        self.reset_hook()
        self.add_safe_pack_hook( evt._pack_hook )
        return self

    def assign_owner(self,req):
        """ Packs self.owner to req if the owner is valid. I.e. it's not
        -1. This method is used by various events that would like to
        pass owner as a parameter. NewFile for example. """
        if self.owner != -1: req['MDATA_KEY_OWNER_ID'] = self.owner

class FakePyinotify(object):
    """ sometimes we must create our own pyinotify like objects to
    instantiate objects from the classes below whenever we want to turn
    a single event into multiple events """
    def __init__(self, path): self.pathname = path

class OrganizeFile(BaseEvent, HasMetaData):
    """ The only kind of event that does support the pack protocol. It's
    used internally with mediamonitor to move files in the organize
    directory. """
    def __init__(self, *args, **kwargs):
        super(OrganizeFile, self).__init__(*args, **kwargs)
    def pack(self):
        raise AttributeError("You can't send organize events to airtime!!!")

class NewFile(BaseEvent, HasMetaData):
    """ NewFile events are the only events that contain
    MDATA_KEY_OWNER_ID metadata in them. """
    def __init__(self, *args, **kwargs):
        super(NewFile, self).__init__(*args, **kwargs)
    def pack(self):
        """ packs turns an event into a media monitor request """
        req_dict = self.metadata.extract()
        req_dict['mode'] = u'create'
        req_dict['is_record'] = self.metadata.is_recorded()
        self.assign_owner(req_dict)
        req_dict['MDATA_KEY_FILEPATH'] = unicode( self.path )
        return [req_dict]

class DeleteFile(BaseEvent):
    """ DeleteFile event only contains the path to be deleted. No other
    metadata can be or is included. (This is because this event is fired
    after the deletion occurs). """
    def __init__(self, *args, **kwargs):
        super(DeleteFile, self).__init__(*args, **kwargs)
    def pack(self):
        req_dict = {}
        req_dict['mode'] = u'delete'
        req_dict['MDATA_KEY_FILEPATH'] = unicode( self.path )
        return [req_dict]

class MoveFile(BaseEvent, HasMetaData):
    """ Path argument should be the new path of the file that was moved """
    def __init__(self, *args, **kwargs):
        super(MoveFile, self).__init__(*args, **kwargs)
    def old_path(self):
        return self._raw_event.src_pathname
    def pack(self):
        req_dict                            = {}
        req_dict['mode']                    = u'moved'
        req_dict['MDATA_KEY_ORIGINAL_PATH'] = self.old_path()
        req_dict['MDATA_KEY_FILEPATH']      = unicode( self.path )
        req_dict['MDATA_KEY_MD5'] = self.metadata.extract()['MDATA_KEY_MD5']
        return [req_dict]

class ModifyFile(BaseEvent, HasMetaData):
    def __init__(self, *args, **kwargs):
        super(ModifyFile, self).__init__(*args, **kwargs)
    def pack(self):
        req_dict = self.metadata.extract()
        req_dict['mode'] = u'modify'
        # path to directory that is to be removed
        req_dict['MDATA_KEY_FILEPATH'] = unicode( self.path )
        return [req_dict]

def map_events(directory, constructor):
    """ Walks 'directory' and creates an event using 'constructor'.
    Returns a list of the constructed events. """
    # -unknown-path should not appear in the path here but more testing
    # might be necessary
    for f in mmp.walk_supported(directory, clean_empties=False):
        try:
            for e in constructor( FakePyinotify(f) ).pack(): yield e
        except BadSongFile as e: yield e

class DeleteDir(BaseEvent):
    """ A DeleteDir event unfolds itself into a list of DeleteFile
    events for every file in the directory. """
    def __init__(self, *args, **kwargs):
        super(DeleteDir, self).__init__(*args, **kwargs)
    def pack(self):
        return map_events( self.path, DeleteFile )

class MoveDir(BaseEvent):
    """ A MoveDir event unfolds itself into a list of MoveFile events
    for every file in the directory. """
    def __init__(self, *args, **kwargs):
        super(MoveDir, self).__init__(*args, **kwargs)
    def pack(self):
        return map_events( self.path, MoveFile )

class DeleteDirWatch(BaseEvent):
    """ Deleting a watched directory is different from deleting any
    other directory. Hence we must have a separate event to handle this
    case """
    def __init__(self, *args, **kwargs):
        super(DeleteDirWatch, self).__init__(*args, **kwargs)
    def pack(self):
        req_dict = {}
        req_dict['mode']               = u'delete_dir'
        req_dict['MDATA_KEY_FILEPATH'] = unicode( self.path + "/" )
        return [req_dict]

