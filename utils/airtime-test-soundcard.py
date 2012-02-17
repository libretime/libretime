import subprocess
import os
import pwd
import grp
import sys

import getopt

if os.geteuid() == 0:
    print "Please run this program as non-root"
    sys.exit(1)

def printUsage():
    print "airtime-test-soundcard [-v] [-o alsa | ao | oss | portaudio | pulseaudio ]"
    print " Where: "
    print "     -v verbose mode "
    print "     -o Linux Sound API "


optlist, args = getopt.getopt(sys.argv[1:], 'hvo:')
sound_api_types = set(["alsa", "ao", "oss", "portaudio", "pulseaudio"])

verbose = False
sound_api = "alsa"
for o, a in optlist:
    if "-v" == o:
        verbose = True
    if "-o" == o:
        if a.lower() in sound_api_types:
            sound_api = a.lower()
        else:
            print "Unknown sound api type\n"
            printUsage()
            sys.exit(1)
    if "-h" == o:
        printUsage()
        sys.exit(0)

try:
    print "Outputting to soundcard with '%s' sound API. You should be able to hear a monotonous tone. Press ctrl-c to quit." % sound_api
        
    command = "/usr/lib/airtime/pypo/bin/liquidsoap_bin/liquidsoap 'output.%s(sine())'" % sound_api
    
    if not verbose:
        command += " > /dev/null"
    
    #print command
    rv = subprocess.call(command, shell=True)
    
except KeyboardInterrupt, ki:
    print "Exiting"
except Exception, e:
    raise
