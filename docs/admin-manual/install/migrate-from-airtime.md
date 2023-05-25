---
title: Migrate from Airtime
sidebar_position: 90
---

This guide walk you though the steps required to migrate your data from Airtime to LibreTime.

:::info

Airtime **linked files** and **watched folders** features are either deprecated or not working in LibreTime.

:::

LibreTime dropped support for Ubuntu 16.04, which is the last supported version of Ubuntu that Airtime supports.

To have a better understanding of the next steps, please read the following documentation before you continue:

- [Upgrade documentation](./upgrade.md)
- [Backup documentation](../backup.md)
- [Install documentation](./install-using-the-installer.md)

## Make a backup

### Backup the configuration

On common setups, you need to backup the entire `/etc/airtime` folder.

### Backup the database

You need to backup the PostgreSQL database, which holds the entire data of your installation.

Here is an example to dump your PostgreSQL database:

```bash
sudo -u postgres pg_dump --no-owner --no-privileges airtime > airtime.sql
```

Please read the `pg_dump` usage for additional details.

### Backup the storage

You need to backup the entire file storage, which holds all the files of your installation.

The path to your storage was defined during the installation process, the default storage path is `/srv/airtime/stor`.

## Install

Install LibreTime on a new system by [running the installer](./install-using-the-installer.md#run-the-installer), and **don't run the setup tasks**.

## Restore the Airtime backup

Restore [the Airtime backup](../backup.md#restore) on the newly installed LibreTime server.

### Restore the storage

Restore the storage by moving the files the your new storage location, the **new default** storage path is `/srv/libretime`.

### Update the configuration

The [configuration](../configuration.md) file changed a lot between Airtime and LibreTime, please take the time to understand it.

Update the **new** LibreTime configuration file to match your previous Airtime settings. See the [configuration](../configuration.md) documentation for more details.

The installer already configured random passwords for Icecast. If you need to restore the Icecast passwords used in Airtime, you have to edit the Icecast password in `/etc/icecast2/icecast.xml` and in the LibreTime [configuration](../configuration.md#stream) file.

### Restore the database

Restore the database by using the following command:

```bash
sudo -u libretime libretime-api dbshell < airtime.sql
```

## Apply migrations

Run the following command to apply the database migrations:

```bash
sudo -u libretime libretime-api migrate
```

## Finish

[Restart the LibreTime services](upgrade.md#restart-the-services) and navigate to the LibreTime web-page.
