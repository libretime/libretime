#!/usr/local/bin/python

# -----------------------------------------------------------------------
# Example of an audio file volume normalizer (peak amplitude).
# 
# 1. Takes the filename to be processed from the command line.
# 2. Runs the audio file through ecasound's volume analyzer.
# 3. If the signal isn't normalized (gain factor > 1), it is amplified
#    (gain factor from the analyzer) and the original file is replaced.
# 4. Removes the temp file and exits.
#
# Known "bugs":
#  - if ecasound's internal sample rate doesn't match audio file's
#    sample rate, signal will get resampled twice
#  - might change the audio format of the processed files
# -----------------------------------------------------------------------

import os
import sys
from tempfile import mktemp
from pyeca import *

# check arguments
if len(sys.argv) < 2: sys.exit(-1)
filename = sys.argv[1]
if not os.path.isfile(filename): sys.exit(-1)
tmpfile = mktemp(".wav")

# create and configure the 'analyze' chainsetup
e = ECA_CONTROL_INTERFACE()
e.command("cs-add analyze")
e.command("c-add 1")
print "Normalizing file " + filename
print "Using tempfile " + tmpfile
e.command("ai-add " + filename)
e.command("ao-add " + tmpfile)
e.command("cop-add -ev")
print "Analyzing sample data."
e.command("cs-connect")
e.command("run")
e.command("cop-select 1")
e.command("copp-select 2")
e.command("copp-get") 
gain_factor = e.last_float()
e.command("cs-disconnect")
if gain_factor <= 1:
    print "File already normalized!"
    sys.exit(0)

# create and config the 'apply' chainsetup
a = ECA_CONTROL_INTERFACE()
a.command("cs-add apply")
a.command("c-add 1")
a.command("ai-add " + tmpfile)
a.command("ao-add " + filename)
print "Applying gain factor: ", gain_factor
a.command("cop-add -ea:100")
a.command("cop-select 1")
a.command("copp-select 1")
a.command_float_arg("copp-set", gain_factor * 100)
a.command("cs-connect")
a.command("run")

# remove the tempfile and exit
os.remove(tmpfile)
print "Done!"
sys.exit(0)
