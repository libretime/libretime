import os
import shutil
import sys
from configobj import ConfigObj

if os.geteuid() != 0:
    print "Please run this as root."
    sys.exit(1)
    
def get_current_script_dir():
    current_script_dir = os.path.realpath(__file__)
    index = current_script_dir.rindex('/')
    return current_script_dir[0:index]
  
def copy_dir(src_dir, dest_dir):
    if (os.path.exists(dest_dir)) and (dest_dir != "/"):
        shutil.rmtree(dest_dir)
    if not (os.path.exists(dest_dir)):
        print "Copying directory "+os.path.realpath(src_dir)+" to "+os.path.realpath(dest_dir)
        shutil.copytree(src_dir, dest_dir)
        
def create_dir(path):
    try:
        os.makedirs(path)
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
    # Absolute path this script is in
    current_script_dir = get_current_script_dir()
    
    #copy monit files
    shutil.copy('%s/../monit-airtime-media-monitor.cfg'%current_script_dir, '/etc/monit/conf.d/')
    shutil.copy('%s/../../monit/monit-airtime-generic.cfg'%current_script_dir, '/etc/monit/conf.d/')
    
    #create log dir
    create_dir(config['log_dir'])

    #copy python files
    copy_dir("%s/.."%current_script_dir, config["bin_dir"])

    #set executable permissions on python files
    os.system("chown -R pypo:pypo "+config["bin_dir"])

    #copy init.d script
    shutil.copy(config["bin_dir"]+"/airtime-media-monitor-init-d", "/etc/init.d/airtime-media-monitor")
    
except Exception, e:
    print e


