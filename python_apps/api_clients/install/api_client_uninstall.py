import os
import sys
from configobj import ConfigObj

def remove_path(path):
    os.system('rm -rf "%s"' % path)
    
def get_current_script_dir():
    return os.path.dirname(os.path.realpath(__file__))
    
current_script_dir = get_current_script_dir()
    
"""load config file"""
try:
    config = ConfigObj("%s/../api_client.cfg" % current_script_dir)
except Exception, e:
    print 'Error loading config file: ', e
    sys.exit(1)
    
print "Removing API Client files"
remove_path(config["bin_dir"])
