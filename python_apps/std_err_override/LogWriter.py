import sys

class LogWriter():
    def __init__(self, logger):
        self.logger = logger
    
    def write(self, txt):
        self.logger.error(txt)

def override_std_err(logger):
    sys.stderr = LogWriter(logger)
