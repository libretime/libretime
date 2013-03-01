import os
import sys
import shutil

#check if root
if os.geteuid() != 0:
    print 'Must be a root user.'
    sys.exit(1)

#ask if we should backup config files
backup_config = True

#ask if we should backup database
backup_database = True

#ask if we should backup stor directory
backup_stor = True

#ask if we should backup all watched directories
backup_watched = True

#create airtime-backup directory
os.mkdir("airtime_backup")

if backup_config:
    backup_config_dir = "airtime_backup/config"
    os.mkdir(backup_config_dir)
    #TODO check if directory exists
    config_dir = "/etc/airtime"
    files = os.listdir()
    for f in files:
        shutil.copy(os.path.join(config_dir, f), \
                    os.path.join(backup_config_dir, f)

if backup_database:
    os.mkdir("airtime_backup/database")
    #TODO: get database name
    #TODO use abs path
    "pg_dump airtime > database.dump.sql"

#TODO this might not be necessary
os.mkdir("airtime_backup/files")

if backup_stor:
    #TODO use abs path
    backup_stor_dir = "airtime_backup/files/stor"
    os.mkdir(backup_stor_dir)
    shutil.copytree("/srv/airtime/stor", backup_stor_dir)

if backup_watched:
    pass

