import logging
import time
import sys
import mm2.mm2 as mm2
from std_err_override import LogWriter

global_cfg = '/etc/airtime/airtime.conf'
logging_cfg = '/usr/lib/airtime/media-monitor/logging.cfg'

mm2.main( global_cfg, logging_cfg )
