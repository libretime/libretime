# -*- coding: utf-8 -*-
class BadSongFile(Exception):
    def __init__(self, path): self.path = path
    def __str__(self): return "Can't read %s" % self.path

class NoConfigFile(Exception):
    def __init__(self, path): self.path = path
    def __str__(self):
        return "Path '%s' for config file does not exit" % self.path

class ConfigAccessViolation(Exception):
    def __init__(self,key): self.key = key
    def __str__(self): return "You must not access key '%s' directly" % self.key

class FailedToSetLocale(Exception):
    def __str__(self): return "Failed to set locale"

class FailedToObtainLocale(Exception):
    def __init__(self, path, cause):
        self.path = path
        self.cause = cause
    def __str__(self): return "Failed to obtain locale from '%s'" % self.path

class CouldNotCreateIndexFile(Exception):
    """exception whenever index file cannot be created"""
    def __init__(self, path, cause=None):
        self.path = path
        self.cause = cause
    def __str__(self): return "Failed to create touch file '%s'" % self.path

class DirectoryIsNotListed(Exception):
    def __init__(self,dir_id,cause=None):
        self.dir_id = dir_id
        self.cause = cause
    def __str__(self):
        return "%d was not listed as a directory in the database" % self.dir_id

class FailedToCreateDir(Exception):
    def __init__(self,path, parent):
        self.path = path
        self.parent = parent
    def __str__(self): return "Failed to create path '%s'" % self.path

class NoDirectoryInAirtime(Exception):
    def __init__(self,path, does_exist):
        self.path = path
        self.does_exist = does_exist
    def __str__(self):
        return "Directory '%s' does not exist in Airtime.\n \
                However: %s do exist." % (self.path, self.does_exist)

class InvalidMetadataElement(Exception):
    def __init__(self, parent, key, path):
        self.parent = parent
        self.key    = key
        self.path   = path
    def __str__(self):
        return "InvalidMetadataElement: (key,path) = (%s,%s)" \
                % (self.key, self.path)

