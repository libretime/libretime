---
title: Backup
sidebar_position: 10
---

:::info

At the moment, there is no script to cleanly restore a LibreTime backup. To restore a LibreTime backup, install a fresh copy, go through the standard setup process, and reupload the backed-up media files.

:::

A backup script is supplied for your convenience in the `utils/` folder of the LibreTime repo.
Run it using:

```
sudo bash libretime-backup.sh  # backs up to user's home folder
# or
sudo bash libretime-backup.sh /backupdir/
```

The backup process can be automated with Cron. Simply add the following to the root user's
crontab with `sudo crontab -e`:

```
0 0 1 * * /locationoflibretimerepo/libretime/backup.sh
```

> For more information on how Cron works, check out [this Redhat guide](https://www.redhat.com/sysadmin/automate-linux-tasks-cron).

If you wish to deploy your own backup solution, the following files and folders need to
be backed up.

```
/srv
  /airtime
    /stor
      /imported - Successfully imported media
      /organize - A temporary holding place for uploaded media as the importer works
/etc
  /airtime
    airtime.conf - The main LibreTime configuration
    icecast_pass - Holds the password for the Icecast server
    liquidsoap.cfg - The main configuration file for Liquidsoap
```

In addition, you should keep a copy of the database current to the backup. The below code
can be used to export the LibreTime database to a file.

```
sudo -u postgres pg_dumpall filename
# or to a zipped archive
sudo -u postgres pg_dumpall | gzip -c > archivename.gz
```

It is recommended to use an incremental backup technique to synchronize
the your LibreTime track library with a backup server regularly. (If
the backup server also contains an LibreTime installation, it should be possible
to switch playout to this second machine relatively quickly, in case of a
hardware failure or other emergency on the production server.)

Two notible backup tools are [rsync](https://rsync.samba.org/) (without version control) and
[rdiff-backup](https://rdiff-backup.net/) (with version control). _rsync_ comes
preinstalled with Ubuntu Server.

:::note

Standard rsync backups, which are used by the backup script, cannot restore files deleted in the backup itself

:::
