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
        #print "Copying directory "+os.path.realpath(src_dir)+" to "+os.path.realpath(dest_dir)
        shutil.copytree(src_dir, dest_dir)
        
def create_dir(path):
    try:
        os.makedirs(path)
    except Exception, e:
        pass
        
PATH_INI_FILE = '/etc/airtime/pypo.cfg'

try:               
    # Absolute path this script is in
    current_script_dir = get_current_script_dir()
    
    if not os.path.exists(PATH_INI_FILE):
        shutil.copy('%s/../pypo.cfg'%current_script_dir, PATH_INI_FILE)
    
    # load config file
    try:
        config = ConfigObj(PATH_INI_FILE)
    except Exception, e:
        print 'Error loading config file: ', e
        sys.exit(1)
    
    #copy monit files
    shutil.copy('%s/../../monit/monit-airtime-generic.cfg'%current_script_dir, '/etc/monit/conf.d/')
    shutil.copy('%s/../../monit/monit-airtime-rabbitmq-server.cfg'%current_script_dir, '/etc/monit/conf.d/')
    if os.environ["disable_auto_start_services"] == "f": 
        shutil.copy('%s/../monit-airtime-liquidsoap.cfg'%current_script_dir, '/etc/monit/conf.d/')
        shutil.copy('%s/../monit-airtime-playout.cfg'%current_script_dir, '/etc/monit/conf.d/')

    #create pypo log dir
    create_dir(config['pypo_log_dir'])
    os.system("chown -R pypo:pypo "+config["pypo_log_dir"])

    #create liquidsoap log dir
    create_dir(config['liquidsoap_log_dir'])
    os.system("chown -R pypo:pypo "+config["liquidsoap_log_dir"])

    #create cache, tmp, file dirs
    create_dir(config['cache_dir'])
    create_dir(config['file_dir'])
    create_dir(config['tmp_dir'])
    
    create_dir(config["base_recorded_files"])

    #copy files to bin dir
    copy_dir("%s/.."%current_script_dir, config["bin_dir"]+"/bin/")

    # delete /usr/lib/airtime/pypo/bin/liquidsoap_scripts/liquidsoap.cfg 
    # as we don't use it anymore.(CC-2552)
    os.remove(config["bin_dir"]+"/bin/liquidsoap_scripts/liquidsoap.cfg")

    #set permissions in bin dir and cache dir
    os.system("chmod 755 "+os.path.join(config["bin_dir"], "bin/liquidsoap_scripts/notify.sh"))
    os.system("chown -R pypo:pypo "+config["bin_dir"])
    os.system("chown -R pypo:pypo "+config["cache_base_dir"])
    os.system("chown -R pypo:pypo "+config["base_recorded_files"])

    #copy init.d script
    shutil.copy(config["bin_dir"]+"/bin/airtime-playout-init-d", "/etc/init.d/airtime-playout")

    #copy log rotate script
    shutil.copy(config["bin_dir"]+"/bin/liquidsoap_scripts/airtime-liquidsoap.logrotate", "/etc/logrotate.d/airtime-liquidsoap")
    
except Exception, e:
    print e


