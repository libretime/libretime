import os
import shutil
import sys
from configobj import ConfigObj

if os.geteuid() != 0:
    print "Please run this as root."
    sys.exit(1)

PATH_INI_FILE = '/etc/airtime/pypo.cfg'

def remove_file(path):
    try:
        os.remove(path)
    except Exception, e:
        pass

# load config file
try:
    config = ConfigObj(PATH_INI_FILE)
except Exception, e:
    print 'Error loading config file: ', e
    sys.exit(1)

try:
    #remove log rotate script
    print " * Removing Pypo Log Rotate Script"
    remove_file("/etc/logrotate.d/airtime-liquidsoap")

    #remove init.d script
    print " * Removing Pypo init.d Script"
    remove_file("/etc/init.d/airtime-playout")

    #remove bin, cache, tmp and file dir
    print " * Removing Pypo Program Directory"
    shutil.rmtree(config['bin_dir'], ignore_errors=True)
    shutil.rmtree(config['cache_dir'], ignore_errors=True)
    shutil.rmtree(config['file_dir'], ignore_errors=True)
    shutil.rmtree(config['tmp_dir'], ignore_errors=True)

    #remove liquidsoap and pypo log dir
    print " * Removing Pypo Log Directories"
    shutil.rmtree(config['liquidsoap_log_dir'], ignore_errors=True)
    shutil.rmtree(config['pypo_log_dir'], ignore_errors=True)

    #remove monit files
    print " * Removing Pypo Monit Files"
    remove_file("/etc/monit/conf.d/monit-airtime-playout.cfg")
    remove_file("/etc/monit/conf.d/monit-airtime-liquidsoap.cfg")
    remove_file("/etc/monit/conf.d/monit-airtime-generic.cfg")

except Exception, e:
    print e
