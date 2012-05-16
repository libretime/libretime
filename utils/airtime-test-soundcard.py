import subprocess
import os
import pwd
import grp
import sys

import getopt

"""
we need to run the program as non-root because Liquidsoap refuses to run as root.
It is possible to run change the effective user id (seteuid) before calling Liquidsoap
but this introduces other problems (fake root user is not part of audio group after calling seteuid)
"""
if os.geteuid() == 0:
    print "Please run this program as non-root"
    sys.exit(1)

def printUsage():
    print "airtime-test-soundcard [-v] [-o alsa | ao | oss | portaudio | pulseaudio ] [-h]"
    print " Where: "
    print "     -v verbose mode"
    print "     -o Linux Sound API (default: alsa)"
    print "     -h show help menu "
    
def find_liquidsoap_binary():
    """
    Starting with Airtime 2.0, we don't know the exact location of the Liquidsoap
    binary because it may have been installed through a debian package. Let's find
    the location of this binary.
    """
    
    rv = subprocess.call("which airtime-liquidsoap > /dev/null", shell=True)
    if rv == 0:
        return "airtime-liquidsoap"

    return None

try:
    optlist, args = getopt.getopt(sys.argv[1:], 'hvo:')
except getopt.GetoptError, g:
    printUsage()
    sys.exit(1)
    
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
    if "-h" == o and len(optlist) == 1:
        printUsage()
        sys.exit(0)

try:
    print "Sound API: %s" % sound_api
    print "Outputting to soundcard. You should be able to hear a monotonous tone. Press ctrl-c to quit."
        
    liquidsoap_exe = find_liquidsoap_binary()
    
    if liquidsoap_exe is None:
        raise Exception("Liquidsoap not found!")
        
    command = "%s 'output.%s(sine())'" % (liquidsoap_exe, sound_api)
    
    if not verbose:
        command += " > /dev/null"
    
    #print command
    rv = subprocess.call(command, shell=True)
    
    #if we reach this point, it means that our subprocess exited without the user
    #doing a keyboard interrupt. This means there was a problem outputting to the 
    #soundcard. Print appropriate message.
    print "There was an error using the selected sound API. Please select a different API " + \
        "and run this program again. Use the -h option for help"
    
except KeyboardInterrupt, ki:
    print "\nExiting"
except Exception, e:
    raise
