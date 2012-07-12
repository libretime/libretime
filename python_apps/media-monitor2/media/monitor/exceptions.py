# -*- coding: utf-8 -*-

class BadSongFile(Exception):
    def __init__(self, path):
        self.path = path
    def __str__(self):
        return "Can't read %s" % self.path
