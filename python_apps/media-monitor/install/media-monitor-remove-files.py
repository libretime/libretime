import os
import shutil
import sys
from configobj import ConfigObj

if os.geteuid() != 0:
    print "Please run this as root."
    sys.exit(1)
    
def remove_file(path):
    try:
        os.remove(path)
    except Exception, e:
        pass

PATH_INI_FILE = '/etc/airtime/media-monitor.cfg'

# load config file
try:
    config = ConfigObj(PATH_INI_FILE)
except Exception, e:
    print 'Error loading config file: ', e
    sys.exit(1)

try:
    #remove init.d script
    print " * Removing Media-Monitor init.d Script"
    remove_file("/etc/init.d/airtime-media-monitor")

    #remove bin dir
    print " * Removing Media-Monitor Program Directory"
    shutil.rmtree(config['bin_dir'], ignore_errors=True)

    #remove log dir
    print " * Removing Media-Monitor Log Directory"
    shutil.rmtree(config['log_dir'], ignore_errors=True)

    #remove monit files
    print " * Removing Media-Monitor Monit Files"
    remove_file("/etc/monit/conf.d/monit-airtime-media-monitor.cfg")
    remove_file("/etc/monit/conf.d/monit-airtime-generic.cfg")
    
except Exception, e:
    print e
