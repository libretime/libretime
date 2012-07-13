# -*- coding: utf-8 -*-
class SyncDB(object):
    """
    Represents the database returned by airtime_mvc. We do not use a list or some other
    fixed data structure because we might want to change the internal representation for
    performance reasons later on.
    """
    def __init__(self, source):
        self.source = source
    def has_file(self, path):
        return True
    def file_mdata(self, path):
        return None
