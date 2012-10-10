# -*- coding: utf-8 -*-
from contextlib import contextmanager
from media.monitor.pure import truncate_to_length, toposort
from media.monitor.log import Loggable
import mutagen


class MetadataAbsent(Exception):
    def __init__(self, name): self.name = name
    def __str__(self): return "Could not obtain element '%s'" % self.name

class MetadataElement(Loggable):

    def __init__(self,name):
        self.name = name
        # "Sane" defaults
        self.__deps          = set()
        self.__normalizer    = lambda x: x
        self.__optional      = True
        self.__default       = None
        self.__is_normalized = lambda _ : True
        self.__max_length    = -1
        self.__translator    = None

    def max_length(self,l):
        self.__max_length = l

    def optional(self, setting):
        self.__optional = setting

    def is_optional(self):
        return self.__optional

    def depends(self, *deps):
        self.__deps = set(deps)

    def dependencies(self):
        return self.__deps

    def translate(self, f):
        self.__translator = f

    def is_normalized(self, f):
        self.__is_normalized = f

    def normalize(self, f):
        self.__normalizer = f

    def default(self,v):
        self.__default = v

    def get_default(self):
        if hasattr(self.__default, '__call__'): return self.__default()
        else: return self.__default

    def has_default(self):
        return self.__default is not None

    def path(self):
        return self.__path

    def __slice_deps(self, d):
        """
        returns a dictionary of all the key value pairs in d that are also
        present in self.__deps
        """
        return dict( (k,v) for k,v in d.iteritems() if k in self.__deps)

    def __str__(self):
        return "%s(%s)" % (self.name, ' '.join(list(self.__deps)))

    def read_value(self, path, original, running={}):

        # If value is present and normalized then we only check if it's
        # normalized or not. We normalize if it's not normalized already

        if self.name in original:
            v = original[self.name]
            if self.__is_normalized(v): return v
            else: return self.__normalizer(v)

        # We slice out only the dependencies that are required for the metadata
        # element.
        dep_slice_orig    = self.__slice_deps(original)
        dep_slice_running = self.__slice_deps(running)
        # TODO : remove this later
        dep_slice_special = self.__slice_deps({'path' : path})
        # We combine all required dependencies into a single dictionary
        # that we will pass to the translator
        full_deps         = dict( dep_slice_orig.items()
                                + dep_slice_running.items()
                                + dep_slice_special.items())

        # check if any dependencies are absent
        # note: there is no point checking the case that len(full_deps) >
        # len(self.__deps) because we make sure to "slice out" any supefluous
        # dependencies above.
        if len(full_deps) != len(self.dependencies()) or \
            len(self.dependencies()) == 0:
            # If we have a default value then use that. Otherwise throw an
            # exception
            if self.has_default(): return self.get_default()
            else: raise MetadataAbsent(self.name)

        # We have all dependencies. Now for actual for parsing
        def def_translate(dep):
            def wrap(k):
                e = [ x for x in dep ][0]
                return k[e]
            return wrap

        # Only case where we can select a default translator
        if self.__translator is None:
            self.translate(def_translate(self.dependencies()))
            if len(self.dependencies()) > 2: # dependencies include themselves
                self.logger.info("Ignoring some dependencies in translate %s"
                                 % self.name)
                self.logger.info(self.dependencies())

        r = self.__normalizer( self.__translator(full_deps) )
        if self.__max_length != -1:
            r = truncate_to_length(r, self.__max_length)
        return r

def normalize_mutagen(path):
    """
    Consumes a path and reads the metadata using mutagen. normalizes some of
    the metadata that isn't read through the mutagen hash
    """
    m = mutagen.File(path, easy=True)
    md = {}
    for k,v in m.iteritems():
        if type(v) is list:
            if len(v) > 0: md[k] = v[0]
        else: md[k] = v
    # populate special metadata values
    md['length']      = getattr(m.info, 'length', 0.0)
    md['bitrate']     = getattr(m.info, 'bitrate', u'')
    md['sample_rate'] = getattr(m.info, 'sample_rate', 0)
    md['mime']        = m.mime[0] if len(m.mime) > 0 else u''
    md['path']        = path
    return md

class MetadataReader(object):
    def __init__(self):
        self.clear()

    def register_metadata(self,m):
        self.__mdata_name_map[m.name] = m
        d = dict( (name,m.dependencies()) for name,m in
                self.__mdata_name_map.iteritems() )
        new_list = list( toposort(d) )
        self.__metadata = [ self.__mdata_name_map[name] for name in new_list
                if name in self.__mdata_name_map]

    def clear(self):
        self.__mdata_name_map = {}
        self.__metadata       = []

    def read(self, path, muta_hash):
        normalized_metadata = {}
        for mdata in self.__metadata:
            try:
                normalized_metadata[mdata.name] = mdata.read_value(
                        path, muta_hash, normalized_metadata)
            except MetadataAbsent:
                if not mdata.is_optional(): raise
        return normalized_metadata

global_reader = MetadataReader()

@contextmanager
def metadata(name):
    t = MetadataElement(name)
    yield t
    global_reader.register_metadata(t)
