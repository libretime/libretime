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

- [:rocket: Using docker-compose](#using-docker-compose)
- [:rocket: Using the installer](#using-the-installer)
- :construction: Using ansible

### Minimum system requirements

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

We recommend installing on one of the following [distribution releases](../../contributor-manual/releases.md#distributions-releases-support):

- [Debian 11](https://www.debian.org/releases/)
- [Ubuntu 20.04 LTS](https://wiki.ubuntu.com/Releases)

### Before installing

Before installing LibreTime, you need to make sure you operating system is properly configured.

#### Operating system time configuration

Check your operating system time configuration using the following command:

```bash
timedatectl
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

Make sure that your time zone is properly configured, if not you can set it using the [`timedatectl set-timezone` command](https://www.freedesktop.org/software/systemd/man/timedatectl.html#set-timezone%20%5BTIMEZONE%5D).

If the NTP service is inactive, you should consider enabling it using the [`timedatectl set-ntp` command](https://www.freedesktop.org/software/systemd/man/timedatectl.html#set-ntp%20%5BBOOL%5D).

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

### Run the installer

Install LibreTime with the recommended options, be sure to replace `PUBLIC_URL` with the public url of your installation,
for example `https://libretime.example.com` or `http://192.168.10.100:80`:

```bash
sudo ./install PUBLIC_URL
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

The install script will use randomly generated passwords to create the PostgreSQL user, RabbitMQ user and to update the default Icecast passwords. Those passwords will be saved to the configuration files.

:::

Feel free to run `./install --help` to get more details.

#### Using hardware audio output

If you plan to output analog audio directly to a mixing console or transmitter, the user running LibreTime needs to be added to the `audio` user group using the command below:

```bash
sudo adduser libretime audio
```

### Setup

Once the installation is completed, edit the [configuration file](./configuration.md) at `/etc/libretime/config.yml` to fill required information and to match your needs.

Next, run the following commands to setup the database:

```bash
sudo -u libretime libretime-api migrate
```

Finally, start the services, and check that they're running properly using the following commands:

```bash
sudo systemctl start libretime.target

sudo systemctl --all --plain | grep libretime
```

Next, continue by [configuring your installation](#configure).

## Using docker-compose

### Download

Pick the version you want to install:

<CodeBlock language="bash">
echo LIBRETIME_VERSION="{vars.version}" > .env
</CodeBlock>

Download the docker-compose files from the repository:

```bash
# Load LIBRETIME_VERSION variable
source .env

wget "https://raw.githubusercontent.com/libretime/libretime/$LIBRETIME_VERSION/docker-compose.yml"
wget "https://raw.githubusercontent.com/libretime/libretime/$LIBRETIME_VERSION/docker/nginx.conf"
wget "https://raw.githubusercontent.com/libretime/libretime/$LIBRETIME_VERSION/docker/config.yml"
```

### Setup

Once the files are downloaded, generate a set of random passwords for the different docker services used by LibreTime:

```bash
echo "# Postgres
POSTGRES_PASSWORD=$(openssl rand -hex 16)

# RabbitMQ
RABBITMQ_DEFAULT_PASS=$(openssl rand -hex 16)

# Icecast
ICECAST_SOURCE_PASSWORD=$(openssl rand -hex 16)
ICECAST_ADMIN_PASSWORD=$(openssl rand -hex 16)
ICECAST_RELAY_PASSWORD=$(openssl rand -hex 16)" >> .env
cat .env
```

:::info

You can find more details in the `docker-compose.yml` file or on the external services docker specific documentation:

- [Postgres](https://hub.docker.com/_/postgres)
- [RabbitMQ](https://hub.docker.com/_/rabbitmq)
- [Icecast](https://github.com/libretime/icecast-docker#readme)

:::

Next, edit the [configuration file](./configuration.md) at `./config.yml` to set the previously generated passwords, fill required information, and to match your needs.

:::info

The `docker/config.yml` configuration file you previously downloaded already contains specific values required by the container setup, you shouldn't change them:

```yaml
database:
  host: "postgres"
rabbitmq:
  host: "rabbitmq"
playout:
  liquidsoap_host: "liquidsoap"
liquidsoap:
  server_listen_address: "0.0.0.0"
stream:
  outputs:
    .default_icecast_output:
      host: "icecast"
```

:::

Next, run the following commands to setup the database:

```bash
docker-compose run --rm api libretime-api migrate
```

Finally, start the services, and check that they're running properly using the following commands:

```bash
docker-compose up -d

docker-compose ps
docker-compose logs -f
```

Next, continue by [configuring your installation](#configure).

## Configure

Once the setup is completed, log in the interface (with the default user `admin` and password `admin`), and make sure to edit the project settings (go to **Settings** > **General**) to match your needs. Important settings are:

- First day of the week

## Next

Once completed, it's recommended to [install a reverse proxy](./reverse-proxy.md) to setup SSL termination and secure your installation.
