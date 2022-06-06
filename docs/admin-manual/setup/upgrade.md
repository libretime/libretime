---
title: Upgrade
sidebar_position: 80
---

This guide walk you though the steps required to upgrade LibreTime.

:::tip

You should always have proper backups and a rollback scenario in place before updating. If the update does not go smoothly, it may cause significant downtime, so you should always have a fallback system available during the update to ensure **broadcast continuity**.

:::

## Stop the services

Run the following commands to apply the database migrations:

```bash
sudo systemctl stop libretime.target
```

## Make a backup

Follow [the backup guide](../backup.md) to make an extra backup of your installation and prepare a rollback scenario in case of accidental data loss during the upgrade process.

## Install the new version

Follow [the install guide](./install.md#download) to download and install the new version, and re-run the `./install` script with the same arguments you used during the initial install.

## Apply upgrade instructions

Be sure to carefully read **all** the [releases notes](../../releases/README.md), from your current version to the targeted version, to apply upgrade or breaking changes instructions to your installation.

## Apply migrations

Run the following command to apply the database migrations:

```bash
sudo -u www-data libretime-api migrate
```

## Restart the services

Restart all the services to make sure all the changes are applied.

```bash
sudo systemctl restart libretime.target
```

## Verify

Verify that all the services are still running after the install process:

```bash
sudo systemctl --all --plain | egrep 'libretime|apache2'
```

Verify for any error in the logs after the install process:

```bash
sudo tail -f -n 100 "/var/log/syslog" | grep "libretime-"
```

Log into the interface and verify for any error after the install process.

If you encounter issues with the new interface, you may need to clear your web browser's cache.
