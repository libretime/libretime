---
title: Upgrade
sidebar_position: 80
---

This guide walk you though the steps required to upgrade LibreTime.

:::tip

You should always have a fallback system available during the upgrade to ensure **broadcast continuity**.

:::

#### Make a backup

Follow [the backup guide](../backup.md) to make an extra backup of your installation in case of accidental data loss during
the upgrade process.

#### Install the new version

Follow [the install guide](./install.md) to download and install the new version.

#### Verify

Verify that all the services are still running after the install process:

```bash
sudo systemctl status \
   libretime-analyzer \
   libretime-api \
   libretime-celery \
   libretime-liquidsoap \
   libretime-playout \
   apache2
```

Verify for any error in the logs after the install process:

```bash
sudo tail -n 20 /var/log/libretime/**/*.log
```

Log into the interface and verify for any error after the install process.

If you encounter issues with the new interface, you may need to clear your web browser's cache.
