# -*- coding: utf-8 -*-
import os
import abc
from media.monitor.pure import LazyProperty
import media.monitor.pure as mmp
from media.monitor.metadata import Metadata
from media.monitor.log import Loggable
from media.monitor.exceptions import BadSongFile

class PathChannel(object):
    def __init__(self, signal, path):
        self.signal = signal
        self.path = path

class EventRegistry(object):
    """
    This class's main use is to keep track all events with a cookie attribute.
    This is done mainly because some events must be 'morphed' into other events
    because we later detect that they are move events instead of delete events.
    """
    registry = {}
    @staticmethod
    def register(evt): EventRegistry.registry[evt.cookie] = evt
    @staticmethod
    def unregister(evt): del EventRegistry.registry[evt.cookie]
    @staticmethod
    def registered(evt): return evt.cookie in EventRegistry.registry
    @staticmethod
    def matching(evt):
        event = EventRegistry.registry[evt.cookie]
        # Want to disallow accessing the same event twice
        EventRegistry.unregister(event)
        return event
    def __init__(self,*args,**kwargs):
        raise Exception("You can instantiate this class. Must only use class \
                methods")

class HasMetaData(object):
    """
    Any class that inherits from this class gains the metadata attribute that
    loads metadata from the class's 'path' attribute. This is done lazily so
    there is no performance penalty to inheriting from this and subsequent
    calls to metadata are cached
    """
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
        self._pack_hook = lambda _ : _ # no op
        self._morph_target = False # returns true if event was used to moprh
        # into another event
    def exists(self): return os.path.exists(self.path)
    @LazyProperty
    def cookie(self):
        return getattr( self._raw_event, 'cookie', None )

    def morph_target(self): return self._morph_target

    def __str__(self):
        return "Event(%s). Path(%s)" % ( self.path, self.__class__.__name__)
    def is_dir_event(self): return self._raw_event.dir

    def add_safe_pack_hook(self,k): self._pack_hook = k

    # As opposed to unsafe_pack...
    def safe_pack(self):
        """
        returns exceptions instead of throwing them to be consistent with
        events that must catch their own BadSongFile exceptions since generate
        a set of exceptions instead of a single one
        """
        # pack will only throw an exception if it processes one file but this
        # is a little bit hacky
        try:
            ret = self.pack()
            self._pack_hook()
            return ret
        except BadSongFile as e: return [e]

    # nothing to see here, please move along
    def morph_into(self, evt):
        self.logger.info("Morphing %s into %s" % ( str(self), str(evt) ) )
        self._raw_event = evt
        self.path = evt.path
        self.add_safe_pack_hook(evt._pack_hook)
        self.__class__ = evt.__class__
        evt._morph_target = True
        return self

class FakePyinotify(object):
    """
    sometimes we must create our own pyinotify like objects to
    instantiate objects from the classes below whenever we want to turn
    a single event into multiple events
    """
    def __init__(self, path):
        self.pathname = path

class OrganizeFile(BaseEvent, HasMetaData):
    def __init__(self, *args, **kwargs):
        super(OrganizeFile, self).__init__(*args, **kwargs)
    def pack(self):
        raise AttributeError("You can't send organize events to airtime!!!")

class NewFile(BaseEvent, HasMetaData):
    def __init__(self, *args, **kwargs):
        super(NewFile, self).__init__(*args, **kwargs)
    def pack(self):
        """
        packs turns an event into a media monitor request
        """
        req_dict = self.metadata.extract()
        req_dict['mode'] = u'create'
        req_dict['MDATA_KEY_FILEPATH'] = unicode( self.path )
        return [req_dict]

class DeleteFile(BaseEvent):
    def __init__(self, *args, **kwargs):
        super(DeleteFile, self).__init__(*args, **kwargs)
    def pack(self):
        req_dict = {}
        req_dict['mode'] = u'delete'
        req_dict['MDATA_KEY_FILEPATH'] = unicode( self.path )
        return [req_dict]

class MoveFile(BaseEvent, HasMetaData):
    """
    Path argument should be the new path of the file that was moved
    """
    def __init__(self, *args, **kwargs):
        super(MoveFile, self).__init__(*args, **kwargs)
    def pack(self):
        req_dict = {}
        req_dict['mode'] = u'moved'
        req_dict['MDATA_KEY_MD5'] = self.metadata.extract()['MDATA_KEY_MD5']
        req_dict['MDATA_KEY_FILEPATH'] = unicode( self.path )
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
    # -unknown-path should not appear in the path here but more testing
    # might be necessary
    for f in mmp.walk_supported(directory, clean_empties=False):
        try:
            for e in constructor( FakePyinotify(f) ).pack(): yield e
        except BadSongFile as e: yield e

class DeleteDir(BaseEvent):
    def __init__(self, *args, **kwargs):
        super(DeleteDir, self).__init__(*args, **kwargs)
    def pack(self):
        return map_events( self.path, DeleteFile )

class MoveDir(BaseEvent):
    def __init__(self, *args, **kwargs):
        super(MoveDir, self).__init__(*args, **kwargs)
    def pack(self):
        return map_events( self.path, MoveFile )

class DeleteDirWatch(BaseEvent):
    def __init__(self, *args, **kwargs):
        super(DeleteDirWatch, self).__init__(*args, **kwargs)
    def pack(self):
        req_dict = {}
        req_dict['mode'] = u'delete_dir'
        req_dict['MDATA_KEY_FILEPATH'] = unicode( self.path + "/" )
        return [req_dict]

