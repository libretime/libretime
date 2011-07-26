from airtimefilemonitor.mediamonitorcommon import MediaMonitorCommon
from airtimefilemonitor.mediaconfig import AirtimeMediaConfig

import logging
import logging.config
import sys
import os
import json
import ConfigParser
import pwd
import grp
import subprocess

import os.path

# configure logging
try:
    logging.config.fileConfig("logging.cfg")
except Exception, e:
    print 'Error configuring logging: ', e
    sys.exit(1)

logger = logging.getLogger()
mmconfig = AirtimeMediaConfig(logger)

#get stor folder location from /etc/airtime/airtime.conf
config = ConfigParser.RawConfigParser()
config.read('/etc/airtime/airtime.conf')
stor_dir = config.get('general', 'base_files_dir') + "/stor"
organize_dir = stor_dir + '/organize'

mmconfig.storage_directory = os.path.normpath(stor_dir)
mmconfig.imported_directory = os.path.normpath(stor_dir + '/imported')
mmconfig.organize_directory = os.path.normpath(organize_dir)

mmc = MediaMonitorCommon(mmconfig)

try:
    os.makedirs(organize_dir)
except Exception, e:
    print e

#older versions of Airtime installed from repository at least had owner of stor dir as "root"
mmc.set_needed_file_permissions(stor_dir, True)
mmc.set_needed_file_permissions(organize_dir, True)

#read list of all files in stor location.....and one-by-one pass this through to
#mmc.organize_files. print out json encoding of before and after
pairs = []
for root, dirs, files in os.walk(mmconfig.storage_directory):
    for f in files:
        old_filepath = os.path.join(root, f)
        new_filepath = mmc.organize_new_file(os.path.join(root, f))
        pair = old_filepath, new_filepath
        pairs.append(pair)
        mmc.set_needed_file_permissions(new_filepath, False)

#need to set all the dirs in imported to be owned by www-data.
command = "chown -R www-data " + stor_dir
subprocess.call(command.split(" "))

print json.dumps(pairs)
