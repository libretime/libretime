---
title: Install
sidebar_position: 00
---

This guide walk you though the steps required to install LibreTime on your system.

## Minimum system requirements

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

## Choose your installation method

:::tip

If you are coming from **Airtime**, please follow the [Airtime migration guide](./migrate-from-airtime.md).

:::

You can install LibreTime using the one of the following methods:

- [:rocket: Using docker](./install-using-docker.mdx)
- [:rocket: Using the installer](./install-using-the-installer.mdx)
- :construction: Using ansible
