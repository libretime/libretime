# -*- coding: utf-8 -*-
from media.monitor.log import get_logger
log = get_logger()
# hash: 'filepath' => owner_id
owners = {}

def add_file_owner(f,owner):
    """
    Associate file f with owner. If owner is -1 then do we will not record it
    because -1 means there is no owner
    """
    if owner == -1: return None
    if f in owners:
        if owner != owners[f]: # check for fishiness
            log.info("Warning ownership of file '%s' changed from '%d' to '%d'"
                    % (f, owners[f], owner))
    owners[f] = owner

def has_owner(f):
    """
    True if f is owned by somebody. False otherwise.
    """
    return f in owners

def remove_file_owner(f):
    """
    Try and delete any association made with file f. Returns true if the the
    association was actually deleted. False otherwise.
    """
    if f in owners:
        del owners[f]
        return True
    else:
        log.warn("Trying to delete file that does not exist: '%s'" % f)
        return False

