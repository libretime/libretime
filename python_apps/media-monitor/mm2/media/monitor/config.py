# -*- coding: utf-8 -*-
import os
import copy
from configobj import ConfigObj

from exceptions import NoConfigFile, ConfigAccessViolation
import pure as mmp

class MMConfig(object):
    def __init__(self, path):
        if not os.path.exists(path): raise NoConfigFile(path)
        self.cfg = ConfigObj(path)

    def __getitem__(self, key):
        """ We always return a copy of the config item to prevent
        callers from doing any modifications through the returned
        objects methods """
        return copy.deepcopy(self.cfg[key])

    def __setitem__(self, key, value):
        """ We use this method not to allow anybody to mess around with
        config file any settings made should be done through MMConfig's
        instance methods """
        raise ConfigAccessViolation(key)

    def save(self): self.cfg.write()

    def last_ran(self):
        """ Returns the last time media monitor was ran by looking at
        the time when the file at 'index_path' was modified """
        return mmp.last_modified(self.cfg['media-monitor']['index_path'])

