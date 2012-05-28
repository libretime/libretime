import os
import sys
import subprocess

if os.geteuid() != 0:
    print "Please run this as root."
    sys.exit(1)

try:      
    #stop pypo and liquidsoap processes
    print "Waiting for pypo processes to stop...",
    try:
        os.remove("/usr/bin/airtime-liquidsoap")
    except Exception, e:
        pass
    if (os.path.exists('/etc/init.d/airtime-playout')):
        subprocess.call("invoke-rc.d airtime-playout stop", shell=True)
        print "OK"
    else:
        print "Wasn't running"
except Exception, e:
    print e
