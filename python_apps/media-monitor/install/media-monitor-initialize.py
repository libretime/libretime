import subprocess
import os

if os.geteuid() != 0:
    print "Please run this as root."
    sys.exit(1)

try:
    #create media-monitor dir under /var/tmp/airtime
    if not os.path.exists("/var/tmp/airtime/media-monitor"):
        os.makedirs("/var/tmp/airtime/media-monitor")

    #update-rc.d init script
    subprocess.call("update-rc.d airtime-media-monitor defaults >/dev/null 2>&1", shell=True)

    #Start media-monitor daemon
    print "* Waiting for media-monitor processes to start..."

    subprocess.call("invoke-rc.d airtime-media-monitor start-no-monit", shell=True)
except Exception, e:
    print e
