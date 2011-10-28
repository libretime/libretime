from subprocess import Popen
import os
import sys

if os.geteuid() != 0:
    print "Please run this as root."
    sys.exit(1)

try:      
    #stop pypo and liquidsoap processes
    print "Waiting for pypo processes to stop..."
    p = Popen("/etc/init.d/airtime-playout stop", shell=True)
    sts = os.waitpid(p.pid, 0)[1]
except Exception, e:
    print e
