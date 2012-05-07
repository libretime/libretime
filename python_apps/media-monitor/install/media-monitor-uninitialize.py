import subprocess
import os
import sys

if os.geteuid() != 0:
    print "Please run this as root."
    sys.exit(1)

try:
    print "Waiting for media-monitor processes to stop...",
    if (os.path.exists('/etc/init.d/airtime-media-monitor')):
        subprocess.call("invoke-rc.d airtime-media-monitor stop", shell=True)
        print "OK"
    else:
        print "Wasn't running"
except Exception, e:
    print e
