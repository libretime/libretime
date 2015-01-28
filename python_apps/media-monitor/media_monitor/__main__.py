import logging
import locale
import time
import sys
import os
import mm2.mm2 as mm2
from std_err_override import LogWriter
locale.setlocale(locale.LC_ALL, '')

def run():
    global_cfg = '/etc/airtime/airtime.conf'
    logging_cfg = os.path.join(os.path.dirname(__file__), 'logging.cfg')
    
    mm2.main( global_cfg, logging_cfg )

run()