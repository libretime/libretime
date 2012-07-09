import os
import mutagen
import abc

# Note: this isn't really good design...
# Anyone who expects a BaseEvent object should be able to handle any instances
# of its subclasses by the substitution principle. CLearly not the case with
# the DeleteFile subclass.

# It would be good if we could parameterize this class by the attribute
# that would contain the path to obtain the meta data. But it would be too much
# work for little reward
class HasMetaData(object):
    __metaclass__ = abc.ABCMeta
    def __init__(self, *args, **kwargs):
        self.__metadata = None
        self.__loaded = False
    @property
    def metadata(self):
        if self.__loaded: return self.__metadata
        else:
            f  = mutagen.File(self.path, easy=True)
            self.__metadata = f
            self.__loaded = True
            return self.metadata

class BaseEvent(object):
    __metaclass__ = abc.ABCMeta
    def __init__(self, raw_event):
        self.__raw_event = raw_event
        self.path = os.path.normpath(raw_event.pathname)
        super(BaseEvent, self).__init__()
    def exists(self): return os.path.exists(self.path)
    def __str__(self):
        return "Event. Path: %s" % self.__raw_event.pathname

class OrganizeFile(BaseEvent, HasMetaData): pass
class NewFile(BaseEvent, HasMetaData): pass
class DeleteFile(BaseEvent): pass

