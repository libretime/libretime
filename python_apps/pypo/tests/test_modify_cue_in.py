from pypopush import PypoPush
from threading import Lock
from queue import Queue

import datetime

pypoPush_q = Queue()
telnet_lock = Lock()

pp = PypoPush(pypoPush_q, telnet_lock)

def test_modify_cue_in():
    link = pp.modify_first_link_cue_point([])
    assert len(link) == 0

    min_ago = datetime.datetime.utcnow() - datetime.timedelta(minutes = 1)
    link = [{"start":min_ago.strftime("%Y-%m-%d-%H-%M-%S"),
             "cue_in":"0", "cue_out":"30"}]
    link = pp.modify_first_link_cue_point(link)
    assert len(link) == 0

    link = [{"start":min_ago.strftime("%Y-%m-%d-%H-%M-%S"),
             "cue_in":"0", "cue_out":"70"}]
    link = pp.modify_first_link_cue_point(link)
    assert len(link) == 1

