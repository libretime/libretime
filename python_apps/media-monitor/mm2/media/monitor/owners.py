# -*- coding: utf-8 -*-
from log import Loggable

class Owner(Loggable):
    def __init__(self):
        # hash: 'filepath' => owner_id
        self.owners = {}

    def get_owner(self,f):
        """ Get the owner id of the file 'f' """
        o = self.owners[f] if f in self.owners else -1
        self.logger.info("Received owner for %s. Owner: %s" % (f, o))
        return o


    def add_file_owner(self,f,owner):
        """ Associate file f with owner. If owner is -1 then do we will not record
        it because -1 means there is no owner. Returns True if f is being stored
        after the function. False otherwise.  """
        if owner == -1: return False
        if f in self.owners:
            if owner != self.owners[f]: # check for fishiness
                self.logger.info("Warning ownership of file '%s' changed from '%d' to '%d'"
                        % (f, self.owners[f], owner))
            else: return True
        self.owners[f] = owner
        return True

    def has_owner(self,f):
        """ True if f is owned by somebody. False otherwise. """
        return f in self.owners

    def remove_file_owner(self,f):
        """ Try and delete any association made with file f. Returns true if
        the the association was actually deleted. False otherwise. """
        if f in self.owners:
            del self.owners[f]
            return True
        else: return False

