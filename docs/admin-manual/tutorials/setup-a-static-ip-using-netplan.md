---
title: How to setup a static ip using Netplan
---

This tutorials will walk you though the steps required to configure a server static IP address by modifying the [Netplan](https://netplan.io/reference/) configuration.

## 1. Edit the configuration

First find the right Netplan configuration filename, and edit the file:

```bash
cd /etc/netplan && ls  # find the netplan filename
sudo nano ##-network-manager-all.yaml
```

If the Netplan configuration is empty, fill in the file with the example below. Otherwise,
input the IP address reserved for the server in `xxx.xxx.xxx.xxx/yy` format, the gateway (the IP address
of your router), and your DNS server's address.

```yaml
network:
  version: 2
  renderer: networkd
  ethernets:
    enp3s0:
      addresses: [192.168.88.8/24]
      gateway4: 192.168.88.1
      nameservers:
        addresses: [192.168.88.1]
```

:::tip

If you don't have your own DNS server you can use the router's address in most cases or a public DNS server like Google `8.8.8.8` or Cloudflare `1.1.1.1`.

:::

## 2. Apply the configuration

After the Netplan file has been saved, apply the changes by running:

```bash
sudo netplan apply
```
