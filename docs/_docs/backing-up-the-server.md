---
layout: article
title: Backing Up Libretime
category: admin
---

## Backup

A backup script is supplied for your convenience in the *utils/* folder of the Libretime repo.

```
sudo bash libretime-backup.sh  # backs up to user's home folder
# or
sudo bash libretime-backup.sh /backupdir/
```

### Backup Methods

You can dump the entire *PostgreSQL* database to a zipped file with the combination of the
**pg\_dumpall** command and **gzip**. The **pg\_dumpall** command is executed
as the user *postgres*, by using the **sudo** command and the **-u** switch. It
is separated from the **gzip** command with the pipe symbol.

This command can be automated to run on a regular basis using the standard
**cron** tool on your server.

It is recommended to use an incremental backup technique to synchronize
the your LibreTime track library with a backup server regularly. (If
the backup server also contains an LibreTime installation, it should be possible
to switch playout to this second machine relatively quickly, in case of a
hardware failure or other emergency on the production server.)

Two notible backup tools are [rsync](http://rsync.samba.org/) (without version control) and 
[rdiff-backup](http://www.nongnu.org/rdiff-backup/) (with version control). *rsync* comes
preinstalled with Ubuntu Server.

> **Note:** Standard *rsync* backups cannot restore files deleted in the backup itself

## Restore from a Backup

When restoring a production database on a cleanly installed LibreTime system, it
may be necessary to drop the empty database that was created during the new
installation, by using the **dropdb** command. Again, this command is executed
with **sudo** as the user *postgres*: 

```bash
sudo -u postgres dropdb airtime
```

This **dropdb** command above is necessary to avoid 'already exists' errors on
table creation when overwriting an empty LibreTime database in the next step.
These errors might prevent some data from being restored, such as user account
data.

To restore, first unzip the backup file with **gunzip**, then use the **psql**
command as the *postgres* user:

```bash
gunzip libretime-db-backup.gz
sudo -u postgres psql -f libretim-db-backup
```

You should now be able to log in to the LibreTime web interface in the usual way.

For safety reasons, your regular database backups should be kept in a directory
which is backed up by your storage backup tool of choice; for example, the
*/srv/airtime/database\_backups* directory. This should ensure that a storage
restore can be made along with a matching and complete version of the LibreTime
database from the day that the storage backup was made. 
