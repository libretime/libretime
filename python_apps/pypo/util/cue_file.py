#!/usr/bin/env python
# -*- coding: utf-8 -*-

import sys
import shutil
import random
import string
import time
from datetime import timedelta
import os
import logging

from mutagen.mp3 import MP3
from mutagen.oggvorbis import OggVorbis

class CueFile():

    def __init__(self):
        logger = logging.getLogger("cue_file")
        logger.debug("init")

    def cue(self, src, dst, cue_in, cue_out):
        
        logger = logging.getLogger("cue_file")
        logger.debug("cue file: %s %s %s %s", src, dst, cue_in, cue_out)
        
        if src.lower().endswith('.mp3'):
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
            
            logger.debug("in: %s", str_cue_in)
            logger.debug("out: %s", str(str_cue_out) )

            command = 'mp3cut -o %s -t %s-%s %s' % (dst + '.tmp.mp3', str_cue_in, str_cue_out, src);
            logger.info("command: %s", command)
            print command
            os.system(command + ' > /dev/null 2>&1')

            command = 'lame -b 128 %s %s' % (dst + '.tmp.mp3', dst);
            logger.info("command: %s", command)
            print command
            os.system(command + ' > /dev/null 2>&1')
        elif src.lower().endswith('.ogg'):
            audio = OggVorbis(src)
            dur = audio.info.length
            sys.stderr.write('duration: ' + str(dur) + '\n')
            
            cue_out = float(dur) - cue_out
            
            #convert input format of ss.mmm to milliseconds and to string<
            str_cue_in = str(int(round(cue_in*1000)))
            
            #convert input format of ss.mmm to milliseconds and to string
            str_cue_out = str(int(round(cue_out*1000)))
            
            command = 'oggCut -s %s -e %s %s %s' % (str_cue_in, str_cue_out, src, dst)
            logger.info("command: %s", command)
            os.system(command + ' > /dev/null 2>&1')
        else:
            logger.debug("in: %s", 'File name with invalid extension. File will not be cut\n')
            
        return dst
