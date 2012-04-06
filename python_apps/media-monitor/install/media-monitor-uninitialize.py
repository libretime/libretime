from subprocess import Popen
import os
import sys

if os.geteuid() != 0:
    print "Please run this as root."
    sys.exit(1)

try:
    print "Waiting for media-monitor processes to stop...",
    if (os.path.exists('/etc/init.d/airtime-media-monitor')):
        p = Popen("invoke-rc.d airtime-media-monitor stop", shell=True)
        sts = os.waitpid(p.pid, 0)[1]
        print "OK"
    else:
        print "Wasn't running"
except Exception, e:
    print e
