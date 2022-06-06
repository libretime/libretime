---
title: Migrate from Airtime
sidebar_position: 90
---

This guide walk you though the steps required to migrate your data from Airtime to LibreTime.

:::info

Airtime **linked files** and **watched folders** features are either deprecated or not working in LibreTime.

:::

LibreTime dropped support for Ubuntu 16.04, which is the last supported version of Ubuntu that Airtime supports.

## Make a backup

<!-- TODO: Airtime backup process might be different from the LibreTime one, we might need to write a dedicated backup guide here. -->

Follow [the backup guide](../backup.md) to make a backup of your current Airtime installation.

## Install

Install LibreTime on a new system by [running the installer](./install.md#run-the-installer), and **don't run the setup tasks**.

## Restore the backup

Restore [the Airtime backup](../backup.md#restore) on the newly installed LibreTime server.

You have to restore the **database**, the **files storage** and the **configuration files**.

## Update the configuration files

Update the configuration file to match the new configuration schema and update any changed values. See the [configuration](./configuration.md) documentation for more details.

Edit the Icecast password in `/etc/icecast2/icecast.xml` to reflect the password used in Airtime.

## Finish

Restart the LibreTime services and navigate to the LibreTime web-page.
