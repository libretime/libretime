#/bin/bash

# Replace /backup with backup folder location!
# Used for backing up the media library
rsync -aE --delete --info=progress2 /srv/airtime/stor/ /backup

# Used for backing up the database
-u postgres pg_dumpall | gzip -c > libretime-db-backup.gz
mv libretime-db-backup.gz /backup

# Used for backing up Libretime configs
cp /etc/airtime/airtime.conf backup/airtime.conf.backup

# Write date/time of backup
date >> /backup/datelog.txt