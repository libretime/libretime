#!/usr/bin/env python
# -*- coding: utf-8 -*-


"""
cue script that gets called by liquidsoap if a file in the playlist
gives orders to cue (in/out). eg:
cue_file:cue_in=90.0,cue_out=110.0:annotate:***

cue_in is number of seconds from the beginning of the file.
cue_out is number of seconds from the end of the file.

params:  path_to_file, cue_in [float, seconds], cue_out [float, seconds]
returns: path to the cued temp-file

examples:
calling: ./cue_file.py /storage/pypo/cache/2010-06-25-15-05-00/35.mp3 10 120.095
returns: /tmp/lstf_UwDKcEngvF

In this example, the first 10 seconds and last 120.095 seconds are cut off. The 
middle part of the file is returned.

One thing to mention here:
The way pypo (ab)uses liquidsoap can bring in some unwanted effects. liquidsoap 
is built in a way that it tries to collect the needed files to playout in advance.
we 'force' liquidsoap to immediately start playing a newly loaded list, so ls has
no time to prepare the files. If a file is played without cues, this does not affect
the playout too much. My testing on a lame VM added a delay of +/- 10ms.

If the first file in  a playlist is cued, the "mp3cut" command takes time to execute.
On the same VM this takes an additional 200ms for an average size mp3-file.
So the playout will start a bit delayed. This should not be a too big issue, but
think about this behaviour if you eg access the files via network (nas) as the reading
of files could take some time as well.

So maybe we should think about a different implementation. One way would be to do the
cueing during playlist preparation, so all the files would be pre-cut when they are 
passed to liquidsoap. 
Additionally this would allow to run an unpathed version of ls.

"""

import sys
import shutil
import random
import string
import time
from datetime import timedelta
import os

from mutagen.mp3 import MP3
from mutagen.oggvorbis import OggVorbis

TEMP_DIR = '/tmp/';


sys.stderr.write('\n** starting mp3/ogg cutter **\n\n')

try: src = sys.argv[1]
except Exception, e: 
    sys.stderr.write('No file given. Exiting...\n')
    sys.exit()

try: cue_in = float(sys.argv[2])
except Exception, e: 
    cue_in = float(0)
    pass

try: cue_out = float(sys.argv[3])
except Exception, e:
    cue_out = float(0)
    pass

sys.stderr.write('in: %s - out: %s file: %s \n' % (cue_in, cue_out, src))
dst = TEMP_DIR + 'lstf_' + "".join( [random.choice(string.letters) for i in xrange(10)] )
#TODO, there is no checking whether this randomly generated file name already exists!


# get length of track using mutagen. 
#audio
#command
if src.lower().endswith('.mp3'):
    audio = MP3(src)
    dur = round(audio.info.length, 3)

    sys.stderr.write('duration: ' + str(dur) + '\n')

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

    sys.stderr.write('in:       ' + str_cue_in + '\n')
    sys.stderr.write('abs:      ' + str(str_cue_out) + '\n\n')

    command = 'mp3cut -o %s -t %s-%s %s' % (dst, str_cue_in, str_cue_out, src)
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
else:
    sys.stderr.write('File name with invalid extension. Exiting...\n')
    sys.exit()



sys.stderr.write(command + '\n\n\n')
os.system(command + ' > /dev/null 2>&1')

print dst + "\n";
