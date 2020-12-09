---
title: Install
layout: article
category: install
permalink: /install
---

> Note: this guide is assuming you are using Ubuntu 18.04 LTS for installation, which comes with `ufw` and `netplan`.

## Minimum System Requirements

- One of the following Linux distributions
  - Ubuntu 16.04 LTS, 18.04 LTS
  - Debian 9, 10
  - Raspberry Pi OS 9, 10 (formerly Raspbian)
- 1 Ghz Processor
- 2 GB RAM recommended (1 GB required)
- Wired internet connection and static IP address for on-prem install

[DigitalOcean](https://www.digitalocean.com/pricing/#Compute) and [Linode](https://www.linode.com/pricing/#row--compute)
 have similar plans that meet Cloud Install requirements. Both plans cost $10/month.

## Preparing the server

Configure the server to have a static IP address by modifying the Netplan configuration.
If you're using a cloud VM, you likely already have a static IP address. Check with your provider to confirm this.

```
cd /etc/netplan && ls  # find the netplan filename
sudo nano ##-netcfg.yaml
```

If the Netplan configuration is empty, fill in the file with the example below. Otherwise,
input the IP address reserved for the server in `xxx.xxx.xxx.xxx/yy` format, the gateway (the IP address
of your router), and the DNS nameserver. If you don't have a nameserver on your network,
feel free to use Cloudflare's: `1.1.1.1` and `1.0.0.1`.

```
network:
  version: 2
  renderer: networkd
  ethernets:
    enp3s0:
      addresses: [192.168.88.8/24]
      gateway4: 192.168.88.1
      nameservers:
        addresses: 192.168.88.1
```

After the netplan file has been saved, run `sudo netplan apply` to apply changes.

Next, configure Ubuntu's firewall by running:

```
sudo ufw enable
sudo ufw allow 22,80,8000/tcp
```

Unblock ports 8001 and 8002 if you plan to use LibreTime's Icecast server to broadcast livestreams without an external Icecast server acting as a repeater.

```
sudo ufw allow 8001,8002/tcp
```

> If needed, instructions for setting up a reverse proxy can be found [here](/docs/reverse-proxy).

## Installing LibreTime

<iframe width="560" height="315" src="https://www.youtube.com/embed/Djo_55LgjXE" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>


Installing LibreTime consists of running the following commands in the terminal:

```
git clone https://github.com/LibreTime/libretime.git
cd libretime
sudo bash install -fiap
```

After the install is completed, head to the IP address of the server LibreTime was just installed on
to complete the welcome wizard. While not strictly necessary, it is recommended that you change the passwords prompted in the welcome wizard if you intend on accessing the server from the Internet. The welcome wizard will
walk you through the rest of the installation process.

## Services

Once all of the services needed to run LibreTime are installed and configured,
it is important that the server starts them during the boot process, to cut down on downtime, especially in live enviornments.
Ubuntu 18.04 uses the `systemctl` command to manage services, so run the following commands to enable all
LibreTime-needed services to run at boot:

```
sudo systemctl enable libretime-liquidsoap
sudo systemctl enable libretime-playout
sudo systemctl enable libretime-celery
sudo systemctl enable libretime-analyzer
sudo systemctl enable apache2
sudo systemctl enable rabbitmq-server
```

> If an error is returned, try adding `.service` to the end of each command.

## User Permissions

If you plan to have LibreTime output analog audio directly to a mixing console or transmitter,
the `www-data` user needs to be added to the `audio` user group using the command below:

```
sudo adduser www-data audio
```