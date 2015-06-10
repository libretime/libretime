# -*- coding: utf-8 -*-
import copy
from subprocess import Popen, PIPE
import subprocess
import os
import math
import wave
import contextlib
import shutil, pipes
import re
import sys
import stat
import hashlib
import locale
import operator as op

from os.path   import normpath
from itertools import takewhile
# you need to import reduce in python 3
try: from functools import reduce
except: pass
from configobj import ConfigObj

from exceptions import FailedToSetLocale, FailedToCreateDir

supported_extensions = [u"mp3", u"ogg", u"oga", u"flac", u"wav",
                        u'm4a', u'mp4', 'opus']

unicode_unknown = u'unknown'

path_md = ['MDATA_KEY_TITLE', 'MDATA_KEY_CREATOR', 'MDATA_KEY_SOURCE',
            'MDATA_KEY_TRACKNUMBER', 'MDATA_KEY_BITRATE']

class LazyProperty(object):
    """
    meant to be used for lazy evaluation of an object attribute.
    property should represent non-mutable data, as it replaces itself.
    """
    def __init__(self,fget):
        self.fget      = fget
        self.func_name = fget.__name__

    def __get__(self,obj,cls):
        if obj is None: return None
        value = self.fget(obj)
        setattr(obj,self.func_name,value)
        return value

class IncludeOnly(object):
    """
    A little decorator to help listeners only be called on extensions
    they support
    NOTE: this decorator only works on methods and not functions. Maybe
    fix this?
    """
    def __init__(self, *deco_args):
        self.exts = set([])
        for arg in deco_args:
            if isinstance(arg,str): self.add(arg)
            elif hasattr(arg, '__iter__'):
                for x in arg: self.exts.add(x)
    def __call__(self, func):
        def _wrap(moi, event, *args, **kwargs):
            ext = extension(event.pathname)
            # Checking for emptiness b/c we don't want to skip direcotries
            if (ext.lower() in self.exts) or event.dir:
                return func(moi, event, *args, **kwargs)
        return _wrap

def partition(f, alist):
    """
    Partition is very similar to filter except that it also returns the
    elements for which f return false but in a tuple.
    >>> partition(lambda x : x > 3, [1,2,3,4,5,6])
    ([4, 5, 6], [1, 2, 3])
    """
    return (filter(f, alist), filter(lambda x: not f(x), alist))

def is_file_supported(path):
    """
    Checks if a file's path(filename) extension matches the kind that we
    support note that this is case insensitive.
    >>> is_file_supported("test.mp3")
    True
    >>> is_file_supported("/bs/path/test.mP3")
    True
    >>> is_file_supported("test.txt")
    False
    """
    return extension(path).lower() in supported_extensions

# TODO : In the future we would like a better way to find out whether a show
# has been recorded
def is_airtime_recorded(md):
    """ Takes a metadata dictionary and returns True if it belongs to a
    file that was recorded by Airtime. """
    if not 'MDATA_KEY_CREATOR' in md: return False
    return md['MDATA_KEY_CREATOR'] == u'Airtime Show Recorder'

def read_wave_duration(path):
    """ Read the length of .wav file (mutagen does not handle this) """
    with contextlib.closing(wave.open(path,'r')) as f:
        frames   = f.getnframes()
        rate     = f.getframerate()
        duration = frames/float(rate)
        return duration

def clean_empty_dirs(path):
    """ walks path and deletes every empty directory it finds """
    # TODO : test this function
    if path.endswith('/'): clean_empty_dirs(path[0:-1])
    else:
        for root, dirs, _ in os.walk(path, topdown=False):
            full_paths = ( os.path.join(root, d) for d in dirs )
            for d in full_paths:
                if os.path.exists(d):
                    #Try block avoids a race condition where a file is added AFTER listdir
                    #is run but before removedirs. (Dir is not empty and removedirs throws
                    #an exception in that case then.)
                    try: 
                        if not os.listdir(d): os.rmdir(d)
                    except OSError:
                        pass

def extension(path):
    """
    return extension of path, empty string otherwise. Prefer to return empty
    string instead of None because of bad handling of "maybe" types in python.
    I.e. interpreter won't enforce None checks on the programmer
    >>> extension("testing.php")
    'php'
    >>> extension("a.b.c.d.php")
    'php'
    >>> extension('/no/extension')
    ''
    >>> extension('/path/extension.ml')
    'ml'
    """
    ext = path.split(".")
    if len(ext) < 2: return ""
    else: return ext[-1]

def no_extension_basename(path):
    """
    returns the extensionsless basename of a filepath
    >>> no_extension_basename("/home/test.mp3")
    u'test'
    >>> no_extension_basename("/home/test")
    u'test'
    >>> no_extension_basename('blah.ml')
    u'blah'
    >>> no_extension_basename('a.b.c.d.mp3')
    u'a.b.c.d'
    """
    base = unicode(os.path.basename(path))
    if extension(base) == "": return base
    else: return '.'.join(base.split(".")[0:-1])

def walk_supported(directory, clean_empties=False):
    """ A small generator wrapper around os.walk to only give us files
    that support the extensions we are considering. When clean_empties
    is True we recursively delete empty directories left over in
    directory after the walk. """
    if directory is None:
        return

    for root, dirs, files in os.walk(directory):
        full_paths = ( os.path.join(root, name) for name in files
                if is_file_supported(name) )
        for fp in full_paths: yield fp
    if clean_empties: clean_empty_dirs(directory)


def file_locked(path):
    #Capture stderr to avoid polluting py-interpreter.log
    proc = Popen(["lsof", path], stdout=PIPE, stderr=PIPE)
    out = proc.communicate()[0].strip('\r\n')
    return bool(out)

def magic_move(old, new, after_dir_make=lambda : None):
    """ Moves path old to new and constructs the necessary to
    directories for new along the way """
    new_dir = os.path.dirname(new)
    if not os.path.exists(new_dir): os.makedirs(new_dir)
    # We need this crusty hack because anytime a directory is created we must
    # re-add it with add_watch otherwise putting files in it will not trigger
    # pyinotify events
    after_dir_make()
    shutil.move(old,new)

def move_to_dir(dir_path,file_path):
    """ moves a file at file_path into dir_path/basename(filename) """
    bs = os.path.basename(file_path)
    magic_move(file_path, os.path.join(dir_path, bs))

def apply_rules_dict(d, rules):
    """ Consumes a dictionary of rules that maps some keys to lambdas
    which it applies to every matching element in d and returns a new
    dictionary with the rules applied. If a rule returns none then it's
    not applied """
    new_d = copy.deepcopy(d)
    for k, rule in rules.iteritems():
        if k in d:
            new_val = rule(d[k])
            if new_val is not None: new_d[k] = new_val
    return new_d

def default_to_f(dictionary, keys, default, condition):
    new_d = copy.deepcopy(dictionary)
    for k in keys:
        if condition(dictionary=new_d, key=k): new_d[k] = default
    return new_d

def default_to(dictionary, keys, default):
    """ Checks if the list of keys 'keys' exists in 'dictionary'. If
    not then it returns a new dictionary with all those missing keys
    defaults to 'default' """
    cnd = lambda dictionary, key: key not in dictionary
    return default_to_f(dictionary, keys, default, cnd)

def remove_whitespace(dictionary):
    """ Remove values that empty whitespace in the dictionary """
    nd = copy.deepcopy(dictionary)
    bad_keys = []
    for k,v in nd.iteritems():
        if hasattr(v,'strip'):
            stripped = v.strip()
            # ghetto and maybe unnecessary
            if stripped == '' or stripped == u'': bad_keys.append(k)
    for bad_key in bad_keys: del nd[bad_key]
    return nd

def parse_int(s):
    # TODO : this function isn't used anywhere yet but it may useful for emf
    """
    Tries very hard to get some sort of integer result from s. Defaults to 0
    when it fails
    >>> parse_int("123")
    '123'
    >>> parse_int("123saf")
    '123'
    >>> parse_int("asdf")
    None
    """
    if s.isdigit(): return s
    else:
        try   : return str(reduce(op.add, takewhile(lambda x: x.isdigit(), s)))
        except: return None


def organized_path(old_path, root_path, orig_md):
    """
    old_path  - path where file is store at the moment <= maybe not necessary?
    root_path - the parent directory where all organized files go
    orig_md - original meta data of the file as given by mutagen AFTER being
    normalized
    return value: new file path
    """
    filepath = None
    ext = extension(old_path)
    def default_f(dictionary, key):
        if key in dictionary: return len(str(dictionary[key])) == 0
        else: return True
    # We set some metadata elements to a default "unknown" value because we use
    # these fields to create a path hence they cannot be empty Here "normal"
    # means normalized only for organized path

    # MDATA_KEY_BITRATE is in bytes/second i.e. (256000) we want to turn this
    # into 254kbps

    # Some metadata elements cannot be empty, hence we default them to some
    # value just so that we can create a correct path
    normal_md = default_to_f(orig_md, path_md, unicode_unknown, default_f)
    try:
        formatted = str(int(normal_md['MDATA_KEY_BITRATE']) / 1000)
        normal_md['MDATA_KEY_BITRATE'] = formatted + 'kbps'
    except:
        normal_md['MDATA_KEY_BITRATE'] = unicode_unknown

    if is_airtime_recorded(normal_md):
        # normal_md['MDATA_KEY_TITLE'] = 'show_name-yyyy-mm-dd-hh:mm:ss'
        r = "(?P<show>.+)-(?P<date>\d+-\d+-\d+)-(?P<time>\d+:\d+:\d+)$"
        title_re    = re.match(r, normal_md['MDATA_KEY_TITLE'])
        show_name   = title_re.group('show')
        #date        = title_re.group('date')
        yyyy, mm, dd = normal_md['MDATA_KEY_YEAR'].split('-',2)
        fname_base  = '%s-%s-%s.%s' % \
                (title_re.group('time'), show_name,
                        normal_md['MDATA_KEY_BITRATE'], ext)
        filepath = os.path.join(root_path, yyyy, mm, dd, fname_base)
    elif len(normal_md['MDATA_KEY_TRACKNUMBER']) == 0:
        fname = u'%s-%s.%s' % (normal_md['MDATA_KEY_TITLE'],
                normal_md['MDATA_KEY_BITRATE'], ext)
        path = os.path.join(root_path, normal_md['MDATA_KEY_CREATOR'],
                            normal_md['MDATA_KEY_SOURCE'] )
        filepath = os.path.join(path, fname)
    else: # The "normal" case
        fname = u'%s-%s-%s.%s' % (normal_md['MDATA_KEY_TRACKNUMBER'],
                                  normal_md['MDATA_KEY_TITLE'],
                                  normal_md['MDATA_KEY_BITRATE'], ext)
        path = os.path.join(root_path, normal_md['MDATA_KEY_CREATOR'],
                            normal_md['MDATA_KEY_SOURCE'])
        filepath = os.path.join(path, fname)
    return filepath

# TODO : Get rid of this function and every one of its uses. We no longer use
# the md5 signature of a song for anything
def file_md5(path,max_length=100):
    """ Get md5 of file path (if it exists). Use only max_length
    characters to save time and memory. Pass max_length=-1 to read the
    whole file (like in mm1) """
    if os.path.exists(path):
        with open(path, 'rb') as f:
            m = hashlib.md5()
            # If a file is shorter than "max_length" python will just return
            # whatever it was able to read which is acceptable behaviour
            m.update(f.read(max_length))
            return m.hexdigest()
    else: raise ValueError("'%s' must exist to find its md5" % path)

def encode_to(obj, encoding='utf-8'):
    # TODO : add documentation + unit tests for this function
    if isinstance(obj, unicode): obj = obj.encode(encoding)
    return obj

def convert_dict_value_to_utf8(md):
    """ formats a dictionary to send as a request to api client """
    return dict([(item[0], encode_to(item[1], "utf-8")) for item in md.items()])

def get_system_locale(locale_path='/etc/default/locale'):
    """ Returns the configuration object for the system's default
    locale. Normally requires root access. """
    if os.path.exists(locale_path):
        try:
            config = ConfigObj(locale_path)
            return config
        except Exception as e: raise FailedToSetLocale(locale_path,cause=e)
    else: raise ValueError("locale path '%s' does not exist. \
            permissions issue?" % locale_path)

def configure_locale(config):
    """ sets the locale according to the system's locale. """
    current_locale = locale.getlocale()
    if current_locale[1] is None:
        default_locale = locale.getdefaultlocale()
        if default_locale[1] is None:
            lang = config.get('LANG')
            new_locale = lang
        else: new_locale = default_locale
        locale.setlocale(locale.LC_ALL, new_locale)
    reload(sys)
    sys.setdefaultencoding("UTF-8")
    current_locale_encoding = locale.getlocale()[1].lower()
    if current_locale_encoding not in ['utf-8', 'utf8']:
        raise FailedToSetLocale()

def fondle(path,times=None):
    # TODO : write unit tests for this
    """ touch a file to change the last modified date. Beware of calling
    this function on the same file from multiple threads. """
    with file(path, 'a'): os.utime(path, times)

def last_modified(path):
    """ return the time of the last time mm2 was ran. path refers to the
    index file whose date modified attribute contains this information.
    In the case when the file does not exist we set this time 0 so that
    any files on the filesystem were modified after it """
    if os.path.exists(path): return os.path.getmtime(path)
    else: return 0

def expand_storage(store):
    """ A storage directory usually consists of 4 different
    subdirectories. This function returns their paths """
    store = os.path.normpath(store)
    return {
        'organize'      : os.path.join(store, 'organize'),
        'recorded'      : os.path.join(store, 'recorded'),
        'problem_files' : os.path.join(store, 'problem_files'),
        'imported'      : os.path.join(store, 'imported'),
    }

def create_dir(path):
    """ will try and make sure that path exists at all costs. raises an
    exception if it fails at this task. """
    if not os.path.exists(path):
        try                   : os.makedirs(path)
        except Exception as e : raise FailedToCreateDir(path, e)
        else: # if no error occurs we still need to check that dir exists
            if not os.path.exists: raise FailedToCreateDir(path)

def sub_path(directory,f):
    """
    returns true if 'f' is in the tree of files under directory.
    NOTE: does not look at any symlinks or anything like that, just looks at
    the paths.
    """
    normalized = normpath(directory)
    common     = os.path.commonprefix([ normalized, normpath(f) ])
    return common == normalized

def owner_id(original_path):
    """ Given 'original_path' return the file name of the of
    'identifier' file. return the id that is contained in it. If no file
    is found or nothing is read then -1 is returned. File is deleted
    after the number has been read """
    fname = "%s.identifier" % original_path
    owner_id = -1
    try:
        f = open(fname)
        for line in f:
            owner_id = int(line)
            break
        f.close()
    except Exception: pass
    else:
        try: os.unlink(fname)
        except Exception: raise
    return owner_id

def file_playable(pathname):
    """ Returns True if 'pathname' is playable by liquidsoap. False
    otherwise. """

    #currently disabled because this confuses inotify....
    return True
    #remove all write permissions. This is due to stupid taglib library bug
    #where all files are opened in write mode. The only way around this is to
    #modify the file permissions
    os.chmod(pathname, stat.S_IRUSR | stat.S_IRGRP | stat.S_IROTH)

    # when there is an single apostrophe inside of a string quoted by
    # apostrophes, we can only escape it by replace that apostrophe with
    # '\''. This breaks the string into two, and inserts an escaped
    # single quote in between them.
    command = ("airtime-liquidsoap -c 'output.dummy" + \
        "(audio_to_stereo(single(\"%s\")))' > /dev/null 2>&1") % \
        pathname.replace("'", "'\\''")

    return_code = subprocess.call(command, shell=True)

    #change/restore permissions to acceptable
    os.chmod(pathname, stat.S_IRUSR | stat.S_IRGRP | stat.S_IROTH | \
             stat.S_IWUSR | stat.S_IWGRP | stat.S_IWOTH)
    return (return_code == 0)

def toposort(data):
    """
    Topological sort on 'data' where 'data' is of the form:
        data = [
            'one'   : set('two','three'),
            'two'   : set('three'),
            'three' : set()
        ]
    """
    for k, v in data.items():
        v.discard(k) # Ignore self dependencies
    extra_items_in_deps = reduce(set.union, data.values()) - set(data.keys())
    data.update(dict((item,set()) for item in extra_items_in_deps))
    while True:
        ordered = set(item for item,dep in data.items() if not dep)
        if not ordered: break
        for e in sorted(ordered): yield e
        data = dict((item,(dep - ordered)) for item,dep in data.items()
                if item not in ordered)
    assert not data, "A cyclic dependency exists amongst %r" % data

def truncate_to_length(item, length):
    """ Truncates 'item' to 'length' """
    if isinstance(item, int): item = str(item)
    if isinstance(item, basestring):
        if len(item) > length: return item[0:length]
        else: return item

def truncate_to_value(item, value):
    """ Truncates 'item' to 'value' """
    if isinstance(item, basestring): item = int(item)
    if isinstance(item, int):
        item = abs(item)
        if item > value: item = value
    return str(item)

def format_length(mutagen_length):
    if convert_format(mutagen_length):
        """ Convert mutagen length to airtime length """
        t = float(mutagen_length)
        h = int(math.floor(t / 3600))
        t = t % 3600
        m = int(math.floor(t / 60))
        s = t % 60
        # will be ss.uuu
        s = str('{0:f}'.format(s))
        seconds = s.split(".")
        s = seconds[0]
        # have a maximum of 6 subseconds.
        if len(seconds[1]) >= 6: ss = seconds[1][0:6]
        else: ss = seconds[1][0:]
        return "%s:%s:%s.%s" % (h, m, s, ss)

def convert_format(value):
    regCompiled = re.compile("^[0-9][0-9]:[0-9][0-9]:[0-9][0-9](\.\d+)?$")
    if re.search(regCompiled, str(value)) is None:
        return True
    else:
        return False

if __name__ == '__main__':
    import doctest
    doctest.testmod()
