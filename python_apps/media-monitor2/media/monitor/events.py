# -*- coding: utf-8 -*-
import os
import abc
from media.monitor.pure import LazyProperty
from media.monitor.metadata import Metadata

class PathChannel(object):
    """a dumb struct; python has no record types"""
    def __init__(self, signal, path):
        self.signal = signal
        self.path = path

# It would be good if we could parameterize this class by the attribute
# that would contain the path to obtain the meta data. But it would be too much
# work
class HasMetaData(object):
    __metaclass__ = abc.ABCMeta
    @LazyProperty
    def metadata(self):
        return Metadata(self.path)

class BaseEvent(object):
    __metaclass__ = abc.ABCMeta
    def __init__(self, raw_event):
        # TODO : clean up this idiotic hack
        # we should use keyword constructors instead of this behaviour checking
        # bs to initialize BaseEvent
        if hasattr(raw_event,"pathname"):
            self.__raw_event = raw_event
            self.path = os.path.normpath(raw_event.pathname)
        else: self.path = raw_event
    def exists(self): return os.path.exists(self.path)
    def __str__(self):
        return "Event. Path: %s" % self.__raw_event.pathname

class OrganizeFile(BaseEvent, HasMetaData):
    def __init__(self, *args, **kwargs): super(OrganizeFile, self).__init__(*args, **kwargs)
    def pack(self):
        raise AttributeError("What the hell are you doing? You can't send organize events to airtime!!!")

class NewFile(BaseEvent, HasMetaData):
    def __init__(self, *args, **kwargs): super(NewFile, self).__init__(*args, **kwargs)
    def pack(self):
        """
        packs turns an event into a media monitor request
        """
        req_dict = self.metadata.extract()
        req_dict['mode'] = u'create'
        req_dict['MDATA_KEY_FILEPATH'] = unicode( self.path )
        return req_dict

class DeleteFile(BaseEvent):
    def __init__(self, *args, **kwargs): super(DeleteFile, self).__init__(*args, **kwargs)
    def pack(self):
        req_dict = {}
        req_dict['mode'] = u'delete'
        req_dict['MDATA_KEY_FILEPATH'] = unicode( self.path )
        return req_dict

class MoveFile(BaseEvent, HasMetaData):
    """Path argument should be the new path of the file that was moved"""
    def __init__(self, *args, **kwargs): super(MoveFile, self).__init__(*args, **kwargs)
    def pack(self):
        req_dict = {}
        req_dict['mode'] = u'moved'
        req_dict['MDATA_KEY_MD5'] = self.metadata.extract()['MDATA_KEY_MD5']
        req_dict['MDATA_KEY_FILEPATH'] = unicode( self.path )
        return req_dict

class DeleteDir(BaseEvent):
    def __init__(self, *args, **kwargs): super(DeleteDir, self).__init__(*args, **kwargs)
    def pack(self):
        req_dict = {}
        req_dict['mode'] = u'delete_dir'
        req_dict['MDATA_KEY_FILEPATH'] = unicode( self.path )
        return req_dict

class ModifyFile(BaseEvent, HasMetaData):
    def __init__(self, *args, **kwargs): super(ModifyFile, self).__init__(*args, **kwargs)
    def pack(self):
        req_dict = self.metadata.extract()
        req_dict['mode'] = u'modify'
        # path to directory that is to be removed
        req_dict['MDATA_KEY_FILEPATH'] = unicode( self.path )
        return req_dict
