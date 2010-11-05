#!/usr/bin/env python
# -*- coding: utf-8 -*-

# author Jonas Ohrstrom <jonas@digris.ch>

import sys
import shutil
import random
import string
import time
from datetime import timedelta
import os
import logging

from mutagen.mp3 import MP3


class CueFile():

    def __init__(self):
        logger = logging.getLogger("cue_file")
        logger.debug("init")

    def cue(self, src, dst, cue_in, cue_out):
        
        logger = logging.getLogger("cue_file.cue")
        logger.debug("cue file: %s %s %s %s", src, dst, cue_in, cue_out)
        
        # mutagen
        audio = MP3(src)
        dur = round(audio.info.length, 3)

        logger.debug("duration by mutagen: %s", dur)
        
        cue_out = round(float(dur) - cue_out, 3)
        
        str_cue_in = str(timedelta(seconds=cue_in)).replace(".", "+") # hh:mm:ss+mss, eg 00:00:20+000
        str_cue_out = str(timedelta(seconds=cue_out)).replace(".", "+") #
        
        """
        now a bit a hackish part, don't know how to do this better...
        need to cut the digits after the "+"
        """
        ts = str_cue_in.split("+")
        try:
            if len(ts[1]) == 6:
                ts[1] = ts[1][0:3]
                str_cue_in = "%s+%s" % (ts[0], ts[1])
        except Exception, e:
            pass
        
        ts = str_cue_out.split("+")
        try:
            if len(ts[1]) == 6:
                ts[1] = ts[1][0:3]
                str_cue_out = "%s+%s" % (ts[0], ts[1])
        except Exception, e:
            pass
        
        #sys.stderr.write(str(timedelta(seconds=cue_in)).replace(".", "+") + '\n\n')
        logger.debug("in: %s", str_cue_in)
        logger.debug("out: %s", str(str_cue_out) )

#        command = 'mp3cut -o %s -t %s-%s %s' % (dst, str_cue_in, str_cue_out, src);
#        logger.info("command: %s", command)
#        os.system(command + ' >/dev/null')
#
#        command = 'mp3val -f %s' % (dst);
#        logger.info("command: %s", command)
#        os.system(command + ' >/dev/null')

        command = 'mp3cut -o %s -t %s-%s %s' % (dst + '.tmp.mp3', str_cue_in, str_cue_out, src);
        logger.info("command: %s", command)
        os.system(command + ' >/dev/null')

        command = 'lame -b 32 %s %s' % (dst + '.tmp.mp3', dst);
        logger.info("command: %s", command)
        os.system(command + ' >/dev/null')

        
        return dst
