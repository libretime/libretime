---
title: Upgrade
sidebar_position: 80
---

This guide walk you though the steps required to upgrade LibreTime.

:::tip

You should always have proper backups and a rollback scenario in place before updating. If the update doesn't go smoothly, it may cause significant downtime, so you should always have a fallback system available during the update to ensure **broadcast continuity**.

:::

## Stop the services

Run the following commands to stop the services:

```bash
sudo systemctl stop libretime.target
# Or
sudo systemctl stop libretime-analyzer.service
sudo systemctl stop libretime-api.service
sudo systemctl stop libretime-liquidsoap.service
sudo systemctl stop libretime-playout.service
sudo systemctl stop libretime-worker.service
```

## Make a backup

Follow [the backup guide](../backup.md) to make an extra backup of your installation and prepare a rollback scenario in case of accidental data loss during the upgrade process.

## Apply upgrade instructions

Be sure to carefully read **all** the [releases notes](../../releases/README.md), from your current version to the targeted version, to apply upgrade or breaking changes instructions to your installation.

:::caution

You might need to run steps before and after the install procedure. Be sure to follow these steps thoroughly.

:::

## Install the new version

Follow [the install guide](./install.md#download) to download and install the new version, and re-run the `./install` script with the same arguments you used during the initial install.

## Apply migrations

Run the following command to apply the database migrations:

```bash
sudo -u libretime libretime-api migrate
```

## Restart the services

Restart all the services to make sure all the changes are applied.

```bash
sudo systemctl restart libretime.target
```

## Verify

Verify that all the services are still running after the install process:

```bash
sudo systemctl --all --plain | egrep 'libretime|nginx|php.*-fpm'
```

Verify for any error in the logs after the install process:

```bash
sudo tail -f -n 100 "/var/log/syslog" | grep "libretime-"
```

Log into the interface and verify for any error after the install process.

If you encounter issues with the new interface, you may need to clear your web browser's cache.
