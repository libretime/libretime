from subprocess import Popen
import os
import sys

if os.geteuid() != 0:
    print "Please run this as root."
    sys.exit(1)

try:      
    #stop pypo and liquidsoap processes
    print "Waiting for pypo processes to stop...",
    if (os.path.exists('/etc/init.d/airtime-playout')):
        p = Popen("invoke-rc.d airtime-playout stop", shell=True)
        sts = os.waitpid(p.pid, 0)[1]
        print "OK"
    else:
        print "Wasn't running"
except Exception, e:
    print e
