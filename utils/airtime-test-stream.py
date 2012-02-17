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
    print "airtime-test-stream [-v] [-o icecast | shoutcast ] [-H hostname] [-P port] [-u username] [-p password] [-m mount]"
    print " Where: "
    print "     -v verbose mode"
    print "     -o stream server type (default: icecast)"
    print "     -H hostname (default: localhost) "
    print "     -P port (default: 8000) "
    print "     -u port (default: source) "
    print "     -p password (default: hackme) "
    print "     -m mount (default: test) "


optlist, args = getopt.getopt(sys.argv[1:], 'hvo:H:P:u:p:')
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
    
    url = "http://%s:%s/%s" % (host, port, mount)
    print "Outputting to %s streaming server. You should be able to hear a monotonous tone on %s. Press ctrl-c to quit." % (stream_type, url)
        
    if stream_type == "icecast":
        command = "liquidsoap 'output.icecast(%%vorbis, host = \"%s\", port = %s, user= \"%s\", password = \"%s\", mount=\"%s\", sine())'" % (host, port, user, password, mount)
    else:
        command = "liquidsoap 'output.shoutcast(%%mp3, host=\"%s\", port = %s, user= \"%s\", password = \"%s\", mount=\"%s\",  sine())'" % (host, port, user, password, mount)
        
    if not verbose:
        command += " > /dev/null"
    
    #print command
    rv = subprocess.call(command, shell=True)
    
except KeyboardInterrupt, ki:
    print "Exiting"
except Exception, e:
    raise
