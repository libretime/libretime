# -*- coding: utf-8 -*-
import os
from configobj import ConfigObj
import copy

from media.monitor.exceptions import NoConfigFile, ConfigAccessViolation

class MMConfig(object):
    def __init__(self, path):
        if not os.path.exists(path):
            raise NoConfigFile(path)
        self.cfg = ConfigObj(path)

    def __getitem__(self, key):
        """
        We always return a copy of the config item to prevent callers from doing any modifications
        through the returned objects methods
        """
        return copy.deepcopy(self.cfg[key])

    def __setitem__(self, key, value):
        """
        We use this method not to allow anybody to mess around with config file
        any settings made should be done through MMConfig's instance methods
        """
        raise ConfigAccessViolation(key)

    def save(self): self.cfg.write()

    # Remove this after debugging...
    def haxxor_set(self, key, value): self.cfg[key] = value
    def haxxor_get(self, key): return self.cfg[key]


