---
title: Multipass
layout: article
category: dev
---

[Multipass](https://multipass.run) is a tool for easily setting up Ubuntu VMs on Windows, Mac, and Linux.
Similar to Docker, Multipass works through a CLI. To use, clone this repo and then create a new Multipass VM.

```
git clone https://github.com/libretime/libretime.git
cd libretime
multipass launch bionic -n ltTEST --cloud-init cloud-init.yaml
multipass shell ltTEST
```

Multipass isn't currently able to do an automated install from the cloud-init script.
After you enter the shell for the first time, you will still need to run the install script for LibreTime.

```
cd /libretime
sudo bash install -fiap
```

The IP address of your new VM can be found by running `multipass list`. Copy and paste it into your web browser to access the LibreTime interface and complete the setup wizard.

You can stop the VM with `multipass stop ltTEST` and restart with `multipass start ltTEST`.
If you want to delete the image and start again, run `multipass delete ltTEST && multipass purge`.

### Cloud-init options in cloud-init.yaml

You may wish to change the below fields as per your location.
```
timezone: America/New York  # change as needed
ntp:
  pools: ['north-america.pool.ntp.org']
  servers: ['0.north-america.pool.ntp.org', '0.pool.ntp.org']
```

If you are running your forked repo of LibreTime for testing purposes,
modify the URL on this line:

```
- cd / && git clone https://github.com/LibreTime/libretime.git
```