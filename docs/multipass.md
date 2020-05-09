Testing with multipass
------------------------

Ever wanted to test out LibreTime but didn't want to tango with
Virturalbox, Vagrent, or Docker?

![](https://thumbs.gfycat.com/HauntingDirtyDragon-size_restricted.gif)

Canonical released
[Multipass](https://multipass.run), a tool for setting up Ubuntu VMs with Cloud-init files in a snap. Multipass is available for Windows and macOS, as well as Linux OSes that support snaps.

Similar to Docker, Multipass works through a CLI. To use, clone this repo and then open a Terminal (or Command Prompt) inside the created folder and run
```
multipass launch bionic -n ltTEST --cloud-init libretimeTest.yaml  # to create and launch VM
multipass list  # to display running VMs, IP address displayed here
multipass shell ltTEST  # to enter VM's shell
```

And that's it! At the moment, Multipass isn't patient enough for an automated install, so after you enter the shell for the first time, you will still need to run the install script for LibreTime.

```
sudo ./libretime/install -fiap

sudo service airtime-liquidsoap start
sudo service airtime-playout start
sudo service airtime-celery start
sudo service airtime_analyzer start
```

Copy and paste the VM's IP address into your web browser to access the LibreTime interface.

You can stop the VM with `multipass stop ltTEST` and restart with `multipass start ltTEST`.
If you want to delete the image and start again, run `multipass delete ltTEST && multipass purge`.

---
### Cloud-init options in lttest-multipass.yaml

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
runcmd:
  - cd / && git clone https://github.com/LibreTime/libretime.git
```
