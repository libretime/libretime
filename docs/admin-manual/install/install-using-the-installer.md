---
title: Install using the installer
sidebar_position: 20
---

import Tabs from '@theme/Tabs';
import TabItem from '@theme/TabItem';
import CodeBlock from '@theme/CodeBlock';
import vars from '@site/vars';

This guide walk you though the steps required to install LibreTime on your system using the installer.

The installer is shipped in the released tarballs or directly in the project repository.

Installing on one of the following [distribution releases](../../releases/README.md#distributions-releases-support) is recommend:

- [Debian 11](https://www.debian.org/releases/)
- [Ubuntu 20.04 LTS](https://wiki.ubuntu.com/Releases)

## Before installing

Before installing LibreTime, you need to make sure you operating system is **up to date** and configured.

### Operating system time configuration

Check your operating system time configuration using the following command:

```bash
sudo timedatectl
```

```
               Local time: Fri 2022-08-05 12:43:39 CEST
           Universal time: Fri 2022-08-05 10:43:39 UTC
                 RTC time: Fri 2022-08-05 10:43:40
                Time zone: Europe/Berlin (CEST, +0200)
System clock synchronized: yes
              NTP service: active
          RTC in local TZ: no
```

Make sure that your time zone is configured, if not you can set it using the [`timedatectl set-timezone` command](https://www.freedesktop.org/software/systemd/man/timedatectl.html#set-timezone%20%5BTIMEZONE%5D). The following command configure the timezone to `Europe/Paris`, make sure to set your own timezone:

```bash
sudo timedatectl set-timezone Europe/Paris
```

If the NTP service is inactive, you should consider enabling it using the [`timedatectl set-ntp` command](https://www.freedesktop.org/software/systemd/man/timedatectl.html#set-ntp%20%5BBOOL%5D). The following command enables the `NTP service`:

```bash
sudo timedatectl set-ntp true
```

Finally, check that everything was applied by running `timedatectl`:

```bash
sudo timedatectl
```

## Download

You can either download the latest released tarball or clone the repository.

<Tabs>
<TabItem label="Release tarball" value="tarball" default>

Download the [latest released](https://github.com/libretime/libretime/releases) tarball from Github.

Or directly from the command-line:

<CodeBlock language="bash">
wget https://github.com/libretime/libretime/releases/download/{vars.version}/libretime-{vars.version}.tar.gz
</CodeBlock>

And extract the tarball:

<CodeBlock language="bash">
tar -xvf libretime-{vars.version}.tar.gz && cd libretime
</CodeBlock>

</TabItem>
<TabItem label="Git repository" value="git">

Clone the project repository:

```bash
git clone https://github.com/libretime/libretime
cd libretime
```

:::caution

Don't use the https://github.com/libretime/libretime-debian-packaging repository, it's only used to create LibreTime packages.

:::

:::info

When upgrading, you should clean the local repository, pull the latest changes and finally check out the desired version:

```bash
cd libretime
git clean -xdf
git pull
```

:::

And checkout the latest version:

<CodeBlock language="bash">
git checkout {vars.version}
</CodeBlock>

</TabItem>
</Tabs>

## Run the installer

Install LibreTime with the recommended options, be sure to replace `https://libretime.example.com` with the public url of your installation:

```bash
sudo ./install https://libretime.example.com
```

:::caution

When upgrading be sure to run the installer using the same arguments you used during the initial install.

:::

:::warning

To update the LibreTime nginx configuration file, for example to change the `--listen-port`, make sure to add the `--update-nginx` flag to allow overwriting the existing configuration file.

:::

If you need to change some configuration, the install script can be configured using flags or environment variables. Changing the listening port of LibreTime or whether you want to install some dependency by yourself, you could run the following:

```bash
# Install LibreTime on your system with the following tweaks:
# - don't install the liquidsoap package (remember to install liquidsoap yourself)
# - set the listen port to 8080
# - don't run the PostgreSQL setup (remember to setup PostgreSQL yourself)
sudo \
LIBRETIME_PACKAGES_EXCLUDES='liquidsoap' \
./install \
  --listen-port 8080 \
  --no-setup-postgresql \
  https://libretime.example.com
```

You can persist the install configuration in a `.env` file next to the install script. For example, the above command could be persisted using the `.env` file below, and you should be able to run the install script without arguments:

```
LIBRETIME_PACKAGES_EXCLUDES='liquidsoap'
LIBRETIME_LISTEN_PORT='8080'
LIBRETIME_SETUP_POSTGRESQL=false
LIBRETIME_PUBLIC_URL='https://libretime.example.com'
```

:::note

The install script will use generated passwords to create the PostgreSQL user, RabbitMQ user and to update the default Icecast passwords. Those passwords will be saved to the configuration files.

:::

Feel free to run `./install --help` to get more details.

### Using the system audio output

If you plan to output analog audio directly to a mixing console or transmitter, the user running LibreTime needs to be added to the `audio` user group using the command below:

```bash
sudo adduser libretime audio
```

## Setup LibreTime

Once the installation is completed, edit the [configuration file](../configuration.md) at `/etc/libretime/config.yml` to fill required information and to match your needs.

You may have to configure your timezone to match the one configured earlier:

```git title="/etc/libretime/config.yml"
   # The server timezone, should be a lookup key in the IANA time zone database,
   # for example Europe/Berlin.
   # > default is UTC
-  timezone: UTC
+  timezone: Europe/Paris
```

Next, run the following commands to setup the database:

```bash
sudo -u libretime libretime-api migrate
```

Finally, start the services, and check that they're running using the following commands:

```bash
sudo systemctl start libretime.target

sudo systemctl --all --plain | grep libretime
```

## Securing LibreTime

Once LibreTime is running, it's recommended to [install a reverse proxy](./reverse-proxy.md) to setup SSL termination and secure your installation.

## First login

Once the setup is completed, log in the interface (with the default user `admin` and password `admin`), and edit the project settings (go to **Settings** > **General**) to match your needs.

:::warning

Remember to change your password.

:::
