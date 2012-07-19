# -*- coding: utf-8 -*-
import copy
import os
import shutil
import hashlib
supported_extensions =  ["mp3", "ogg"]
unicode_unknown = u'unknown'

class LazyProperty(object):
    """
    meant to be used for lazy evaluation of an object attribute.
    property should represent non-mutable data, as it replaces itself.
    """
    def __init__(self,fget):
        self.fget = fget
        self.func_name = fget.__name__

    def __get__(self,obj,cls):
        if obj is None: return None
        value = self.fget(obj)
        setattr(obj,self.func_name,value)
        return value


class IncludeOnly(object):
    """
    A little decorator to help listeners only be called on extensions they support
    NOTE: this decorator only works on methods and not functions. Maybe fix this?
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
            if ext in self.exts: func(moi, event, *args, **kwargs)
        return _wrap

def is_file_supported(path):
    return extension(path) in supported_extensions

# In the future we would like a better way to find out
# whether a show has been recorded
def is_airtime_recorded(md):
    return md['MDATA_KEY_CREATOR'] == u'Airtime Show Recorder'

def clean_empty_dirs(path):
    """ walks path and deletes every empty directory it finds """
    if path.endswith('/'): clean_empty_dirs(path[0:-1])
    else:
        for root, dirs, _ in os.walk(path, topdown=False):
            full_paths = ( os.path.join(root, d) for d in dirs )
            for d in full_paths:
                if os.path.exists(d):
                    if not os.listdir(d): os.removedirs(d)

def extension(path):
    """
    return extension of path, empty string otherwise. Prefer
    to return empty string instead of None because of bad handling of "maybe"
    types in python. I.e. interpreter won't enforce None checks on the programmer
    >>> extension("testing.php")
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
    'test'
    >>> no_extension_basename("/home/test")
    'test'
    >>> no_extension_basename('blah.ml')
    'blah'
    """
    base = os.path.basename(path)
    if extension(base) == "": return base
    else: return base.split(".")[-2]

def walk_supported(directory, clean_empties=False):
    """
    A small generator wrapper around os.walk to only give us files that support the extensions
    we are considering. When clean_empties is True we recursively delete empty directories
    left over in directory after the walk.
    """
    for root, dirs, files in os.walk(directory):
        full_paths = ( os.path.join(root, name) for name in files if is_file_supported(name) )
        for fp in full_paths: yield fp
    if clean_empties: clean_empty_dirs(directory)

def magic_move(old, new):
    # TODO : document this
    new_dir = os.path.dirname(new)
    if not os.path.exists(new_dir): os.makedirs(new_dir)
    shutil.move(old,new)

def apply_rules_dict(d, rules):
    # TODO : document this
    new_d = copy.deepcopy(d)
    for k, rule in rules.iteritems():
        if k in d: new_d[k] = rule(d[k])
    return new_d

def default_to(dictionary, keys, default):
    # TODO : document default_to
    new_d = copy.deepcopy(dictionary)
    for k in keys:
        if not (k in new_d): new_d[k] = default
    return new_d

def normalized_metadata(md, original_path):
    """ consumes a dictionary of metadata and returns a new dictionary with the
    formatted meta data. We also consume original_path because we must set
    MDATA_KEY_CREATOR based on in it sometimes """
    new_md = copy.deepcopy(md)
    # replace all slashes with dashes
    for k,v in new_md.iteritems():
        new_md[k] = str(v).replace('/','-')
    # Specific rules that are applied in a per attribute basis
    format_rules = {
        # It's very likely that the following isn't strictly necessary. But the old
        # code would cast MDATA_KEY_TRACKNUMBER to an integer as a byproduct of
        # formatting the track number to 2 digits.
        'MDATA_KEY_TRACKNUMBER' : lambda x: int(x),
        'MDATA_KEY_BITRATE' : lambda x: str(int(x) / 1000) + "kbps",
        # note: you don't actually need the lambda here. It's only used for clarity
        'MDATA_KEY_FILEPATH' : lambda x: os.path.normpath(x),
    }
    path_md = ['MDATA_KEY_TITLE', 'MDATA_KEY_CREATOR', 'MDATA_KEY_SOURCE',
               'MDATA_KEY_TRACKNUMBER', 'MDATA_KEY_BITRATE']
    # note that we could have saved a bit of code by rewriting new_md using
    # defaultdict(lambda x: "unknown"). But it seems to be too implicit and
    # could possibly lead to subtle bugs down the road. Plus the following
    # approach gives us the flexibility to use different defaults for
    # different attributes
    new_md = apply_rules_dict(new_md, format_rules)
    new_md = default_to(dictionary=new_md, keys=['MDATA_KEY_TITLE'], default=no_extension_basename(original_path))
    new_md = default_to(dictionary=new_md, keys=path_md, default=unicode_unknown)
    # In the case where the creator is 'Airtime Show Recorder' we would like to
    # format the MDATA_KEY_TITLE slightly differently
    # Note: I don't know why I'm doing a unicode string comparison here
    # that part is copied from the original code
    if is_airtime_recorded(new_md):
        hour,minute,second,name = md['MDATA_KEY_TITLE'].split("-",4)
        # We assume that MDATA_KEY_YEAR is always given for airtime recorded
        # shows
        new_md['MDATA_KEY_TITLE'] = '%s-%s-%s:%s:%s' % \
            (name, new_md['MDATA_KEY_YEAR'], hour, minute, second)
        # IMPORTANT: in the original code. MDATA_KEY_FILEPATH would also
        # be set to the original path of the file for airtime recorded shows
        # (before it was "organized"). We will skip this procedure for now
        # because it's not clear why it was done
    return new_md

def organized_path(old_path, root_path, normal_md):
    """
    old_path - path where file is store at the moment <= maybe not necessary?
    root_path - the parent directory where all organized files go
    normal_md - original meta data of the file as given by mutagen AFTER being normalized
    return value: new file path
    """
    filepath = None
    ext = extension(old_path)
    # The blocks for each if statement look awfully similar. Perhaps there is a
    # way to simplify this code
    if is_airtime_recorded(normal_md):
        fname = u'%s-%s-%s.%s' % ( normal_md['MDATA_KEY_YEAR'], normal_md['MDATA_KEY_TITLE'],
                normal_md['MDATA_KEY_BITRATE'], ext )
        yyyy, mm, _ = normal_md['MDATA_KEY_YEAR'].split('-',3)
        path = os.path.join(root_path, yyyy, mm)
        filepath = os.path.join(path,fname)
    elif normal_md['MDATA_KEY_TRACKNUMBER'] == unicode_unknown:
        fname = u'%s-%s.%s' % (normal_md['MDATA_KEY_TITLE'], normal_md['MDATA_KEY_BITRATE'], ext)
        path = os.path.join(root_path, normal_md['MDATA_KEY_CREATOR'],
                            normal_md['MDATA_KEY_SOURCE'] )
        filepath = os.path.join(path, fname)
    else: # The "normal" case
        fname = u'%s-%s-%s.%s' % (normal_md['MDATA_KEY_TRACKNUMBER'], normal_md['MDATA_KEY_TITLE'],
                                  normal_md['MDATA_KEY_BITRATE'], ext)
        path = os.path.join(root_path, normal_md['MDATA_KEY_CREATOR'],
                            normal_md['MDATA_KEY_SOURCE'])
        filepath = os.path.join(path, fname)
    return filepath

def file_md5(path,max_length=100):
    """
    Get md5 of file path (if it exists). Use only max_length characters to save time and
    memory
    """
    if os.path.exists(path):
        with open(path, 'rb') as f:
            m = hashlib.md5()
            # If a file is shorter than "max_length" python will just return
            # whatever it was able to read which is acceptable behaviour
            m.update(f.read(max_length))
            return m.hexdigest()
    else: raise ValueError("'%s' must exist to find its md5")

if __name__ == '__main__':
    import doctest
    doctest.testmod()
