Backing up the server
--------------------

The following shell commands can be used for database backup and restore on a
running *PostgreSQL* server in an LibreTime system.

You can dump the entire database to a zipped file with the combination of the
**pg\_dumpall** command and **gzip**. The **pg\_dumpall** command is executed
as the user *postgres*, by using the **sudo** command and the **-u** switch. It
is separated from the **gzip** command with the pipe symbol.

```bash
sudo -u postgres pg_dumpall | gzip -c > libretime-backup.gz
```

This command can be automated to run on a regular basis using the standard
**cron** tool on your server.

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
gunzip libretime-backup.gz
sudo -u postgres psql -f libretime-backup
```

You should now be able to log in to the LibreTime web interface in the usual way.

For safety reasons, your regular database backups should be kept in a directory
which is backed up by your storage backup tool of choice; for example, the
*/srv/airtime/database\_backups* directory. This should ensure that a storage
restore can be made along with a matching and complete version of the LibreTime
database from the day that the storage backup was made. 

Storage backup
--------------

Backing up the LibreTime database with **pg\_dumpall** will not back up the
LibreTime media storage server, which is likely to need a great deal more backup
space. Creating a compressed file from hundreds of gigabytes of storage server
contents is likely to take a very long time, and may have little benefit for the
amount of CPU power used, if the media files are already stored in a highly
compressed format. It is also impractical to copy very large backup files across
the network on a daily basis.

Instead, it is preferable to use an incremental backup technique to synchronize
the production LibreTime server storage with a backup server each day or night. If
the backup server also contains an LibreTime installation, it should be possible
to switch playout to this second machine relatively quickly, in case of a
hardware failure or other emergency on the production server.

A standard incremental backup tool on GNU/Linux servers is *rsync*
[(http://rsync.samba.org/)](http://rsync.samba.org/)) which can be installed
using the package manager of your GNU/Linux distribution. However, incremental
backup alone cannot help in the scenario where a file which later proves to be
important has been deleted by an administrator. For backups that can be rolled
back to restore from an earlier date than the current backup, the tool
*rdiff-backup*
[(http://www.nongnu.org/rdiff-backup/](http://www.nongnu.org/rdiff-backup/)) can
be deployed.  
