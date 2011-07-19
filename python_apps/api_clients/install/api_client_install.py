import os
import shutil

def get_current_script_dir():
    return os.path.dirname(os.path.realpath(__file__))

def copy_dir(src_dir, dest_dir):
    if (os.path.exists(dest_dir)) and (dest_dir != "/"):
        shutil.rmtree(dest_dir)
    if not (os.path.exists(dest_dir)):
        print "Copying directory "+os.path.realpath(src_dir)+" to "+os.path.realpath(dest_dir)
        shutil.copytree(src_dir, dest_dir)
    
current_script_dir = get_current_script_dir()
    
"""load config file"""
try:
    config = ConfigObj("current_script_dir/../config.cfg")
except Exception, e:
    print 'Error loading config file: ', e
    sys.exit(1)

copy_dir("%s/../../api_clients"%current_script_dir, config["bin_dir"]+"/api_clients/")