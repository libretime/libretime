---
title: Backup
sidebar_position: 10
---

## Create a backup

This guide walk you though the steps required to create a full backup of your installation.

:::info

Remember to **automate** and **test** the backup process and to have it run regularly. Having an **automated** and **tested** restoring process is also recommended.

:::

:::caution

Feel free to pick the backup software of your choice, but know that rsync and similar tools are not backup tools. You could use [restic](https://restic.net/) or [borg](https://borgbackup.readthedocs.io/).

:::

### Backup the configuration

On common setups, you need to backup the entire `/etc/libretime` folder.

### Backup the database

You need to backup the PostgreSQL database, which holds the entire data of your installation.

Here is an example to dump your PostgreSQL database:

```bash
sudo -u postgres pg_dump --file=libretime.sql libretime
```

Please read the `pg_dump` usage for additional details.

### Backup the storage

You need to backup the entire file storage, which holds all the files of your installation.

The path to your storage was defined during the installation process.

## Restore a backup

### Restore the configuration

Copy the backed configuration files back to the configuration folder.

### Restore the database

:construction:

### Restore the storage

:construction:
