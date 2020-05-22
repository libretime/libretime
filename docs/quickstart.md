---
title: Quick Install
sidebar: installer
---

Up and running in 10 minutes!
----------------------------

LibreTime is quick and easy to install and get running. Follow this guide to go from zero
to full internet radio station in 10 minutes!

> Note: this guide is assuming you are using Ubuntu 18.04 LTS for installation, which comes with `ufw` and `netplan`,
and that you have already installed `git` and configured `ntp`. NTP configuration instructions can be found [here](setting-the-server-time).
While it is possible to install LibreTime on other OSes, such as CentOS 7, Debian 9 and 10, and Raspbian 9 and 10,
these are less tested. Firewall and static IP address configuration will need to be done according to your OSes instructions.

## Minimum System Requirements

| On-Premises Install  (FM + Internet Radio) | Cloud Install (Internet Radio Only) |
|---------------------|---------------|
| 1 Ghz Processor| 1vCPU |
| 2 GB RAM | 2 GB RAM |
| Wired ethernet connection and a static IP address (see below for instructions) | 2 TB of data transfer/month |

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

Next, configure Ubuntu's firewall by running:

```
sudo ufw enable
sudo ufw allow 80/tcp
sudo ufw allow 8000/tcp
```

Unblock ports 8001 and 8002 if you plan on broadcasting live with Libretime.

```
sudo ufw enable 8001/tcp
sudo ufw enable 8002/tcp
```

> If needed, instuctions for setting up a reverse proxy can be found [here](reverse-proxy).

## Installing LibreTime

Installing LibreTime consists of running the following commands in the terminal:

```
git clone https://github.com/LibreTime/libretime.git
cd libretime
sudo ./install -fiap
```

After the install is completed, head to the IP address of the server LibreTime was just installed on
to complete the welcome wizard. While not strictly necessary, it is recommended that you change the passwords prompted in the welcome wizard if you intend on accessing the server from the Internet. The welcome wizard will
walk you through the rest of the installation process.

Congratulations! You've successfully set up LibreTime!

## Post-install

If you plan to have LibreTime output analog audio directly from its server to a mixing console or transmitter,
the `www-data` user needs to be added to the `audio` user group using the command below.

```
sudo adduser www-data audio
```

Now that the install is complete, use these guides to help you continue to set up your LibreTime server

- [Host Configuration](host-configuration)
- [Setting the Server Time](setting-the-server-time)
- [Configuring Track Types](track-types)
- [Setting up SSL](ssl-config)
