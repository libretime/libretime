from __future__ import print_function
from pypoliqqueue import PypoLiqQueue
from telnetliquidsoap import DummyTelnetLiquidsoap, TelnetLiquidsoap


from Queue import Queue
from threading import Lock

import sys
import signal
import logging
from datetime import datetime
from datetime import timedelta

def keyboardInterruptHandler(signum, frame):
    logger = logging.getLogger()
    logger.info('\nKeyboard Interrupt\n')
    sys.exit(0)
signal.signal(signal.SIGINT, keyboardInterruptHandler)

# configure logging
format = '%(levelname)s - %(pathname)s - %(lineno)s - %(asctime)s - %(message)s'
logging.basicConfig(level=logging.DEBUG, format=format)
logging.captureWarnings(True)

telnet_lock = Lock()
pypoPush_q = Queue()


pypoLiq_q = Queue()
liq_queue_tracker = {
        "s0": None,
        "s1": None,
        "s2": None,
        "s3": None,
        }

#dummy_telnet_liquidsoap = DummyTelnetLiquidsoap(telnet_lock, logging)
dummy_telnet_liquidsoap = TelnetLiquidsoap(telnet_lock, logging, \
        "localhost", \
        1234)

plq = PypoLiqQueue(pypoLiq_q, telnet_lock, logging, liq_queue_tracker, \
        dummy_telnet_liquidsoap)
plq.daemon = True
plq.start()


print("Time now: {:s}".format(datetime.utcnow()))

media_schedule = {}

start_dt = datetime.utcnow() + timedelta(seconds=1)
end_dt = datetime.utcnow() + timedelta(seconds=6)

media_schedule[start_dt] = {"id": 5, \
        "type":"file", \
        "row_id":9, \
        "uri":"", \
        "dst":"/home/martin/Music/ipod/Hot Chocolate - You Sexy Thing.mp3", \
        "fade_in":0, \
        "fade_out":0, \
        "cue_in":0, \
        "cue_out":300, \
        "start": start_dt, \
        "end": end_dt, \
        "show_name":"Untitled", \
        "replay_gain": 0, \
        "independent_event": True \
        }



start_dt = datetime.utcnow() + timedelta(seconds=2)
end_dt = datetime.utcnow() + timedelta(seconds=6)

media_schedule[start_dt] = {"id": 5, \
        "type":"file", \
        "row_id":9, \
        "uri":"", \
        "dst":"/home/martin/Music/ipod/Good Charlotte - bloody valentine.mp3", \
        "fade_in":0, \
        "fade_out":0, \
        "cue_in":0, \
        "cue_out":300, \
        "start": start_dt, \
        "end": end_dt, \
        "show_name":"Untitled", \
        "replay_gain": 0, \
        "independent_event": True \
        }
pypoLiq_q.put(media_schedule)

plq.join()





