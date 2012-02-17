from subprocess import Popen
import os
import sys

if os.geteuid() != 0:
    print "Please run this as root."
    sys.exit(1)
    
try:
    if os.environ["disable_auto_start_services"] == "f": 
        #register init.d script
        p = Popen("update-rc.d airtime-show-recorder defaults >/dev/null 2>&1", shell=True)
        sts = os.waitpid(p.pid, 0)[1]
        
        #start daemon
        print "* Waiting for show-recorder processes to start..."
        """
        p = Popen("/etc/init.d/airtime-show-recorder stop", shell=True)
        sts = os.waitpid(p.pid, 0)[1]
        """
        p = Popen("/etc/init.d/airtime-show-recorder start-no-monit", shell=True)
        sts = os.waitpid(p.pid, 0)[1]
except Exception, e:
    print e
