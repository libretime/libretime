---
layout: article
title: Libretime for Terrestrial Broadcasters
category: manager
---

## How to

### 1. Prepare your studio

The server or desktop you plan to run Libretime on should have a built-in soundcard
and ethernet port. A wired approach is strongly recommended over a wireless one.

### 2. Install Ubuntu Server 18.04 LTS

Download Ubuntu Server [here](https://ubuntu.com/download/server). 
A standard install is recommended, on a RAID 1 array if possible (not required, but recommended).

Installation checklist:

- Set correct timezone
- Sync system with national time servers
- Open firewall ports 80 and 8000
- Enable the SSH server for easier remote access (optional)

### 3. Install Libretime

See the [install page](/install).

### 4. Configure soundcard

### 5. Set up SSH tunneling (optional)

SSH tunneling is similar to using a VPN but with the need to manually connect to individual computers
and ports instead of gaining access to the entire network.