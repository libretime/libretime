---
title: Development environment
---

This page describes the different way to run LibreTime in a development environment.

The recommended development environment is the [docker-compose setup](#docker-compose).

## Docker-compose

To setup a docker-compose development environment, run the following commands:

```bash
# Clean and build
make clean
cp .env.dev .env
DOCKER_BUILDKIT=1 docker-compose build

# Setup
make dev-certs
docker-compose run --rm legacy make build
docker-compose run --rm api libretime-api migrate

# Run
docker-compose up -d
docker-compose logs -f
```

:::info

You may also use the following `make clean dev` shortcut:

```bash
make clean dev

docker-compose logs -f
```

:::

## Vagrant

To use Vagrant, you need to install a virtualization engine: [VirtualBox](https://www.virtualbox.org) or Libvirt. The [vagrant-vbguest] package on Github can help maintain guest extensions on host systems using VirtualBox.

:::tip

If you try run a libvirt provided box after using a VirtualBox one, you will receive an
error:

```
Error while activating network:
Call to virNetworkCreate failed: internal error: Network is already in use by interface vboxnet0.
```

This is fixed by stopping virtualbox and re-creating the vagrant box:

```bash
sudo systemctl stop virtualbox
vagrant destroy focal
vagrant up focal --provider=libvirt
```

:::

### Installing Libvirt

On Debian and Ubuntu:

1. Install Vagrant

```bash
sudo apt install vagrant vagrant-libvirt libvirt-daemon-system vagrant-mutate libvirt-dev
sudo usermod -aG libvirt $USER
```

2. Reboot your computer, and then run

```bash
vagrant box add bento/ubuntu-20.04 --provider=virtualbox
vagrant mutate bento/ubuntu-20.04 libvirt
vagrant up focal --provider=libvirt
```

On other distributions, you will need to install [libvirt](https://libvirt.org/) and `vagrant-mutate` and then run

```bash
vagrant plugin install vagrant-libvirt
sudo usermod -a -G libvirt $USER

# Reboot

vagrant plugin install vagrant-mutate
vagrant box fetch bento/ubuntu-20.04
vagrant mutate bento/ubuntu-20.04 libvirt
vagrant up focal --provider=libvirt
```

### Starting LibreTime Vagrant

To get started you clone the repo and run `vagrant up`. The command accepts a parameter to
change the default provider if you have multiple installed. This can be done by appending
`--provider=virtualbox` or `--provider=libvirt` as applicable.

```bash
git clone https://github.com/libretime/libretime
cd libretime
vagrant up focal
```

If everything works out, you will find LibreTime on [port 8080](http://localhost:8080)
and Icecast on [port 8000](http://localhost:8000).

Once you reach the web setup GUI you can click through it using the default values. To
connect to the vagrant machine you can run `vagrant ssh focal` in the libretime
directory.

### Alternative OS installations

With the above instructions LibreTime is installed on Ubuntu Focal. The Vagrant setup
offers the option to choose a different operation system according to you needs.

| OS           | Command               | Comment                        |
| ------------ | --------------------- | ------------------------------ |
| Ubuntu 20.04 | `vagrant up focal`    | Install on Ubuntu Focal Fossa. |
| Debian 11    | `vagrant up bullseye` | Install on Debian Bullseye.    |

### Troubleshooting

If anything fails during the initial provisioning step you can try running `vagrant provision`
to re-run the installer.

If you only want to re-run parts of the installer, use `--provision-with $step`. The
supported steps are `prepare` and `install`.

## Multipass

[Multipass](https://multipass.run) is a tool for easily setting up Ubuntu VMs on Windows, Mac, and Linux.
Similar to Docker, Multipass works through a CLI. To use, clone this repo and then create a new Multipass VM.

```
git clone https://github.com/libretime/libretime
cd libretime
multipass launch focal -n ltTEST --cloud-init cloud-init.yaml
multipass shell ltTEST
```

Multipass isn't currently able to do an automated install from the cloud-init script.
After you enter the shell for the first time, you will still need to [run the LibreTime installer](../admin-manual/setup/install.md).

The IP address of your new VM can be found by running `multipass list`. Copy and paste it into your web browser to access the LibreTime interface and complete the setup wizard.

You can stop the VM with `multipass stop ltTEST` and restart with `multipass start ltTEST`.
If you want to delete the image and start again, run `multipass delete ltTEST && multipass purge`.

### Cloud-init options in cloud-init.yaml

You may wish to change the below fields as per your location.

```yaml
timezone: America/New York # change as needed
ntp:
  pools: ["north-america.pool.ntp.org"]
  servers: ["0.north-america.pool.ntp.org", "0.pool.ntp.org"]
```
