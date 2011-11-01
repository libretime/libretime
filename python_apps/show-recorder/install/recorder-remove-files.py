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

PATH_INI_FILE = '/etc/airtime/recorder.cfg'

# load config file
try:
    config = ConfigObj(PATH_INI_FILE)
except Exception, e:
    print 'Error loading config file: ', e
    sys.exit(1)

try:
    #remove init.d script
    print " * Removing Show-Recorder init.d script"
    remove_file('/etc/init.d/airtime-show-recorder')
    
    #remove bin dir
    print " * Removing Show-Recorder Program Directories"
    shutil.rmtree(config["bin_dir"], ignore_errors=True)
    
    #remove log dir
    print " * Removing Show-Recorder Log Directory"
    shutil.rmtree(config["log_dir"], ignore_errors=True)
    
    #remove temporary media-storage dir
    print " * Removing Show-Recorder Temporary Directory"
    shutil.rmtree(config["base_recorded_files"], ignore_errors=True)
    
    #remove monit files
    print " * Removing Show-Recorder Monit Files"
    remove_file("/etc/monit/conf.d/monit-airtime-show-recorder.cfg")
    remove_file("/etc/monit/conf.d/monit-airtime-generic.cfg")
    
except Exception, e:
    print "Error %s" % e
