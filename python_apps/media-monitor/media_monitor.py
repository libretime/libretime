import logging
import time
import sys
from std_err_override import LogWriter
# configure logging
try:
    logging.config.fileConfig("logging.cfg")

    #need to wait for Python 2.7 for this..
    #logging.captureWarnings(True)

    logger = logging.getLogger()
    LogWriter.override_std_err(logger)

except Exception, e:
    print 'Error configuring logging: ', e
    sys.exit(1)

while True:
    print("testing")
    time.sleep(5)
