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
    print "airtime-test-stream [-v] [-o icecast | shoutcast ] [-H hostname] [-P port] [-u username] [-p password] [-m mount]"
    print " Where: "
    print "     -v verbose mode"
    print "     -o stream server type (default: icecast)"
    print "     -H hostname (default: localhost) "
    print "     -P port (default: 8000) "
    print "     -u user (default: source) "
    print "     -p password (default: hackme) "
    print "     -m mount (default: test) "
    print "     -h show help menu"


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

optlist, args = getopt.getopt(sys.argv[1:], 'hvo:H:P:u:p:m:')
stream_types = set(["shoutcast", "icecast"])

verbose = False
stream_type = "icecast"

host = "localhost"
port = 8000
user = "source"
password = "hackme"
mount = "test"

for o, a in optlist:
    if "-v" == o:
        verbose = True
    if "-o" == o:
        if a.lower() in stream_types:
            stream_type = a.lower()
        else:
            print "Unknown stream type\n"
            printUsage()
            sys.exit(1)
    if "-h" == o:
        printUsage()
        sys.exit(0)
    if "-H" == o:
        host = a
    if "-P" == o:
        port = a
    if "-u" == o:
        user = a
    if "-p" == o:
        password = a
    if "-m" == o:
        mount = a

try:

    print "Protocol: %s " % stream_type
    print "Host: %s" % host
    print "Port: %s" % port
    print "User: %s" % user
    print "Password: %s" % password
    if stream_type == "icecast":
        print "Mount: %s\n" % mount

    url = "http://%s:%s/%s" % (host, port, mount)
    print "Outputting to %s streaming server. You should be able to hear a monotonous tone on '%s'. Press ctrl-c to quit." % (stream_type, url)

    liquidsoap_exe = find_liquidsoap_binary()

    if liquidsoap_exe is None:
        raise Exception("Liquidsoap not found!")

    if stream_type == "icecast":
        command = "%s 'output.icecast(%%vorbis, host = \"%s\", port = %s, user= \"%s\", password = \"%s\", mount=\"%s\", sine())'" % (liquidsoap_exe, host, port, user, password, mount)
    else:
        command = "%s /usr/lib/airtime/pypo/bin/liquidsoap_scripts/library/pervasives.liq 'output.shoutcast(%%mp3, host=\"%s\", port = %s, user= \"%s\", password = \"%s\", sine())'" \
        % (liquidsoap_exe, host, port, user, password)

    if not verbose:
        command += " 2>/dev/null | grep \"failed\""
    else:
        print command

    #print command
    rv = subprocess.call(command, shell=True)

    #if we reach this point, it means that our subprocess exited without the user
    #doing a keyboard interrupt. This means there was a problem outputting to the
    #stream server. Print appropriate message.
    print "There was an error with your stream configuration. Please review your configuration " + \
        "and run this program again. Use the -h option for help"

except KeyboardInterrupt, ki:
    print "\nExiting"
except Exception, e:
    raise
