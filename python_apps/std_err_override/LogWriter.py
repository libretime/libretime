"""
    std_err_override.LogWriter
    ~~~~~~~~~

    This module presents a simple function to reroute output intended
    for stderr to the output log file.

    :author: (c) 2012 by Martin Konecny.
    :license: GPLv3, see LICENSE for more details.
"""

import sys

class _LogWriter():
    def __init__(self, logger):
        self.logger = logger
    
    def write(self, txt):
        self.logger.error(txt)

def override_std_err(logger):
    """
    Create wrapper to intercept any messages that would have been printed out 
    to stderr and write them to our logger instead.
    """
    sys.stderr = _LogWriter(logger)
