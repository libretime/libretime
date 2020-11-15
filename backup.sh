#/bin/bash

# Replace /backup with backup folder location!
# Used for backing up the media library
sudo rsync -aE --delete --info=progress2 /srv/airtime/stor/ /backup

# Used for backing up the database
sudo -u postgres pg_dumpall | gzip -c > libretime-db-backup.gz
sudo mv libretime-db-backup.gz /backup

# Used for backing up Libretime configs
sudo cp /etc/airtime/airtime.conf backup/airtime.conf.backup

# Write date/time of backup
date >> /backup/datelog.txt