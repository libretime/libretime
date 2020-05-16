# Testing LibreTime with Canonical's Multipass

Ever wanted to test out LibreTime but didn't want to tango with
Virturalbox, Vagrent, or Docker? Canonical released [Multipass](https://multipass.run), a tool for setting up Ubuntu VMs with cloud-init files in a snap.
Multipass is available for Windows and macOS, as well as Linux OSes that support snaps.

Similar to Docker, Multipass works through a CLI. To use, clone this repo and then open a Terminal
(or Command Prompt) inside the `libretime` folder and run
```
multipass launch bionic -n ltTEST --cloud-init libretimeTest.yaml  # to launch VM
multipass shell ltTEST  # to enter VM's shell
```

Due to a known bug, Multipass is not able to perform the entire install using the cloud-init file.
Run the LibreTime installer and follow the welcome wizard in your web browser.

```
sudo ./libretime/install -fiap
```

The IP address of your new VM can be found by running `multipass list`.
Copy and paste it into your web browser to access the LibreTime interface.

You can stop the VM with `multipass stop ltTEST` and restart with `multipass start ltTEST`.
If you want to delete the image and start again, run `multipass delete ltTEST && multipass purge`.

---
### Cloud-init options in libretimeTest.yaml

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
