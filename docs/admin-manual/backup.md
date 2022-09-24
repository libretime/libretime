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

Feel free to pick the backup software of your choice, but know that rsync and similar tools aren't backup tools. You could use [restic](https://restic.net/) or [borg](https://borgbackup.readthedocs.io/).

:::

### Backup the configuration

On common setups, you need to backup the entire `/etc/libretime` folder.

### Backup the database

You need to backup the PostgreSQL database, which holds the entire data of your installation.

Here is an example to dump your PostgreSQL database:

```bash
sudo -u postgres pg_dump --file=libretime.sql libretime
```

:::note

Consider using the `--no-owner` and `--no-privileges` flags to ignore roles
and permissions details about the database. This can be useful when restoring
to database or role that have different names.

:::

Please read the `pg_dump` usage for additional details.

### Backup the storage

You need to backup the entire file storage, which holds all the files of your installation.

The path to your storage is defined in the [configuration](./setup/configuration.md) file.

## Restore a backup

### Install or cleanup

If you are restoring a backup on a fresh system, we recommend that you first [install LibreTime](./setup/install.md), and **stop before the [setup tasks](./setup/install.md#setup)**.

If you are restoring a backup on an existing system, make sure to clean the old **database** and **files storage**.

### Restore the configuration

Copy the backed configuration files back to the [configuration](./setup/configuration.md) folder.

If you are upgrading LibreTime, edit the configuration file to match the new configuration schema and update any changed values. See the [configuration](./setup/configuration.md) documentation for more details.

### Restore the database

Restore the database by using the following command:

```bash
sudo -u postgres pg_restore --dbname=libretime libretime.sql
```

:::note

Consider using the `--no-owner` and `--no-privileges` flags to ignore roles
and permissions details about the database. This can be useful when restoring
to database or role that have different names.

:::

If you are upgrading LibreTime, make sure to apply the [database migration](./setup/upgrade.md#apply-migrations).

### Restore the storage

Copy the entire backed file storage back to the storage path.

The path to your storage is defined in the [configuration](./setup/configuration.md) file.
