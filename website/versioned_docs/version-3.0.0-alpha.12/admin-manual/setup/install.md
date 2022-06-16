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

Install LibreTime with the recommended options:

```bash
sudo ./install -fiap
```

Additional options can be listed with the following command:

```bash
./install --help
```

:::info

When upgrading be sure to run the installer using the same arguments you used during the initial install.

:::

Once the installation is completed, open [http://localhost:80](http://localhost:80) to complete the [setup wizard](#setup-wizard).

:::note

- If installed on a remote device, make sure to replace `localhost` with your server's remote address.
- If installed with a custom port, make sure to replace `80` with the custom port.

:::

:::warning

Make sure that you have configured a **firewall** and it's not blocking connection to the desired ports.

- [How to setup a firewall using UFW](../tutorials/setup-a-firewall-using-ufw.md)

LibreTime requires the following ports to be open:

- `80` for the web interface,
- `8000` for the Icecast streams,
- `8001` and `8002` for the live stream input endpoint.

Consider putting your installation behind a [reverse proxy](./reverse-proxy.md) to increase the security.

:::

#### Using hardware audio output

If you plan to output analog audio directly to a mixing console or transmitter, the user running LibreTime (by default `www-data`) needs to be added to the `audio` user group using the command below:

```bash
sudo adduser www-data audio
```

### Setup wizard

The setup wizard walk you through the rest of the installation process.

#### Changing default passwords

It's recommended that you change the passwords prompted in the setup wizard. Be sure to apply the changes on the server before going to any next step.

You can change the default PostgreSQL user password using:

```bash
sudo -u postgres psql -c "ALTER USER airtime PASSWORD 'new-password';"
```

You can change the default RabbitMQ user password using:

```bash
sudo rabbitmqctl change_password "airtime" "new-password"
```

Once completed, it's recommended to [install a reverse proxy](./reverse-proxy.md) to setup SSL termination and secure your installation.
