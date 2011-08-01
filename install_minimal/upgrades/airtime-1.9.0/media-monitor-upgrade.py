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
import re

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
    #organize dir already exists. ( really shouldn't though )
    pass

#older versions of Airtime installed from repository at least had owner of stor dir as "root"
mmc.set_needed_file_permissions(stor_dir, True)
mmc.set_needed_file_permissions(organize_dir, True)

#read list of all files in stor location.....and one-by-one pass this through to
#mmc.organize_files. print out json encoding of before and after
pairs = []

f = open('storDump.txt','r')
for line in f.readlines():
    db_md = line.split("SF_BACKUP_1.9.0")

    #remove newlines.
    for i in range(0, len(db_md)):
        db_md[i] = db_md[i].strip()

    logger.debug(db_md)
    file_md = {}
    old_filepath = db_md[1]
    file_md["MDATA_KEY_FILEPATH"] = old_filepath

    #file is recorded
    #format 1 title year month day hour min
    if int(db_md[0]):
        file_md["MDATA_KEY_TITLE"] = db_md[2]
        match = re.search('^.*(?=\-\d{4}\-\d{2}\-\d{2}\-\d{2}:\d{2}:\d{2}\.(mp3|ogg))', file_md["MDATA_KEY_TITLE"])

        #"Show-Title-2011-03-28-17:15:00.mp3"
        if match:
            file_md["MDATA_KEY_TITLE"] = match.group(0)

        file_md["MDATA_KEY_TITLE"] = db_md[6]+"-"+db_md[7]+"-00-"+file_md["MDATA_KEY_TITLE"]
        file_md["MDATA_KEY_YEAR"] = db_md[3]+"-"+db_md[4]+"-"+db_md[5]

        file_md["MDATA_KEY_CREATOR"] = "Airtime Show Recorder".encode('utf-8')

    #file is regular audio file
    #format 0 title artist album track
    else:
        file_md["MDATA_KEY_TITLE"] = db_md[2]
        match = re.search('^.*(?=\.mp3|\.ogg)', file_md["MDATA_KEY_TITLE"])

        #"test.mp3" -> "test"
        if match:
            file_md["MDATA_KEY_TITLE"] = match.group(0)

        if len(db_md[3]) > 0:
            file_md["MDATA_KEY_CREATOR"] = db_md[3]

        if len(db_md[4]) > 0:
            file_md["MDATA_KEY_SOURCE"] = db_md[4]

        if len(db_md[5]) > 0:
            file_md["MDATA_KEY_TRACKNUMBER"] = int(db_md[5])

    mmc.md_manager.save_md_to_file(file_md)

        #new_filepath = mmc.organize_new_file(old_filepath)

        #if new_filepath is not None:
            #pair = old_filepath, new_filepath
            #pairs.append(pair)
            #mmc.set_needed_file_permissions(new_filepath, False)

f.close()

for root, dirs, files in os.walk(mmconfig.storage_directory):
    for f in files:
        old_filepath = os.path.join(root, f)
        new_filepath = mmc.organize_new_file(old_filepath)

        if new_filepath is not None:
            pair = old_filepath, new_filepath
            pairs.append(pair)
            mmc.set_needed_file_permissions(new_filepath, False)
        #incase file has a metadata problem.
        else:
            pair = old_filepath, old_filepath
            pairs.append(pair)
            mmc.set_needed_file_permissions(old_filepath, False)

#need to set all the dirs in imported to be owned by www-data.
command = "chown -R www-data " + stor_dir
subprocess.call(command.split(" "))

print json.dumps(pairs)
