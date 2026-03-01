---
title: How to setup a firewall using UFW
---

This tutorials will walk you though the steps required to setup a firewall using [UFW](https://doc.ubuntu-fr.org/ufw).

## 1. Install and enable `UFW`

First you need to make sure UFW is installed and enabled:

```bash
sudo apt install ufw
sudo ufw enable
```

## 2. Configure allowed ports

Next, you need to configure the firewall allowed ports:

```bash
sudo ufw allow 22,80,8000/tcp
```

If you plan to use the live stream input endpoint, be sure to open the `8001` and `8002` ports:

```bash
sudo ufw allow 8001,8002/tcp
```
