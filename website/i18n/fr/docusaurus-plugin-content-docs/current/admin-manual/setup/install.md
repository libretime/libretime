---
title: Install
sidebar_position: 10
---

import Tabs from '@theme/Tabs';
import TabItem from '@theme/TabItem';
import CodeBlock from '@theme/CodeBlock';
import vars from '@site/vars';

This guide walk you though the steps required to install LibreTime on your system.

:::tip

If you are coming from **Airtime**, please follow the [Airtime migration guide](./migrate-from-airtime.md).

:::

You can install LibreTime using the one of the following methods:

- [:rocket: Using the installer](#using-the-installer)
- :construction: Using Ansible

#### Minimum system requirements

- One of the following Linux distributions
  - Ubuntu [current LTS](https://wiki.ubuntu.com/Releases).
    [Note Ubuntu 22.04 LTS is not yet supported](https://github.com/libretime/libretime/issues/1845)
  - Debian [current stable](https://www.debian.org/releases/)
- 1 Ghz Processor
- 2 GB RAM recommended (1 GB required)
- A static external IP address ([How to setup a static ip using Netplan](../tutorials/setup-a-static-ip-using-netplan.md))

:::warning

Make sure that you have configured a **firewall** and it's not blocking connection to the desired ports.

- [How to setup a firewall using UFW](../tutorials/setup-a-firewall-using-ufw.md)

LibreTime requires the following default ports to be open:

- `80` for the web interface,
- `8000` for the Icecast streams,
- `8001` and `8002` for the live stream input endpoint.

:::

## Using the installer

The installer is shipped in the released tarballs or directly in the project repository.

### Download

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

Don't use the https://github.com/libretime/libretime-debian-packaging repository, it is only used to create LibreTime packages.

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

### Run the installer

Install LibreTime with the recommended options, be sure to replace `PUBLIC_URL` with the public url of your installation,
for example `https://libretime.example.com` or `http://192.168.10.100:80`:

```bash
sudo ./install PUBLIC_URL
```

:::caution

When upgrading be sure to run the installer using the same arguments you used during the initial install.

:::

If you need to change some configuration, the install script can be configured using flags or environment variables. Changing the listening port of LibreTime or whether you want to install some dependency by yourself, you could run the following:

```bash
# Install LibreTime on your system with the following tweaks:
# - do not install the liquidsoap package (remember to install liquidsoap yourself)
# - set the listen port to 8080
# - do not run the PostgreSQL setup (remember to setup PostgreSQL yourself)
sudo \
LIBRETIME_PACKAGES_EXCLUDES='liquidsoap' \
./install \
  --listen-port 8080 \
  --no-setup-postgresql \
  https://libretime.example.com
```

:::note

The install script will use randomly generated passwords to create the PostgreSQL user, RabbitMQ user and to update the default Icecast passwords. Those passwords will be saved to the configuration files.

:::

:::info

By default, the install script will not restart any service for you, this is to prevent unwanted restarts on production environment. To let the install script restart the services, you need to pass the `--allow-restart` flag.

:::

Feel free to run `./install --help` to get more details.

#### Using hardware audio output

If you plan to output analog audio directly to a mixing console or transmitter, the user running LibreTime (by default `www-data`) needs to be added to the `audio` user group using the command below:

```bash
sudo adduser www-data audio
```

### Setup

Once the installation is completed, edit the [configuration file](./configuration.md) at `/etc/libretime/config.yml` to fill required information and to match your needs.

Next, run the following commands to setup the database:

```bash
sudo -u www-data libretime-api migrate
```

Synchronize the new Icecast passwords into the database:

```bash
sudo libretime-api set_icecast_passwords --from-icecast-config
```

Finally, start the services, and check that they are running properly using the following commands:

```bash
sudo systemctl start libretime.target

sudo systemctl --all --plain | grep libretime
```

Once completed, it's recommended to [install a reverse proxy](./reverse-proxy.md) to setup SSL termination and secure your installation.
