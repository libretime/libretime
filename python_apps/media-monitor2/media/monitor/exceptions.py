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
