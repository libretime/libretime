---
title: How to update liquidsoap to support AAC streaming
---

This tutorials walks you though the steps required to replace the liquidsoap package with a version that supports AAC streaming.

:::warning

Replacing the liquidsoap package has security implications, since this will remove the package from the system's package manager. This means that the package manager will not be able to update the liquidsoap package in the future. This includes backports of security fixes.

Libretime is NOT compatible with Liquidsoap 2.x at the time of this writing. Future versions of Libretime will support Liquidsoap 2.x which will render these instructions obsolete.

:::info

Lets assume you already [installed LibreTime using the native OS installer](../install/install-using-the-installer.md). Execute the following commands as the libretime user.

:::

## 1. Obtain liquidsoap with AAC support

For Ubuntu 20.04 LTS ('focal'), use the following file:

```bash
wget https://github.com/savonet/liquidsoap/releases/download/v1.4.4/liquidsoap-v1.4.4_1.4.4-ubuntu-focal-amd64-1_amd64.deb
```

For Debian 11 ('Bullseye'), use the following file:

```bash
wget https://github.com/savonet/liquidsoap/releases/download/v1.4.4/liquidsoap-v1.4.4_1.4.4-debian-stable-amd64-1_amd64.deb
```

## 2. Install and replace the liquidsoap package

Install the package using `apt`, then remove the old liquidsoap dependencies:

```bash
sudo apt -y install ./liquidsoap-v1.4.4_1.4.4-*-amd64-1_amd64.deb
sudo apt -y autoremove
```

## 3. Configure LibreTime to use the new liquidsoap package

Nothing to do, this is a drop-in replacement. Just restart the libretime target once and then check the status page in the LibreTime web interface to see if the liquidsoap service is running.

```bash
sudo systemctl restart libretime.target
```

:::warning

If you want to update LibreTime in the future, you'll need to re-run the installer schript. This will replace the liquidsoap package with the version that doesn't support AAC streaming. Add `--packages-excludes liquidsoap` to the installer command to prevent this from happening.

:::
