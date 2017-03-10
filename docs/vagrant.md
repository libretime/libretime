The **LibreTime Vagrant install** is the fastet way to get LibreTime up and running in a way
to hack on its source code or to test it locally.

## Prerequisites

* [Git](https://git-scm.com/)
* [VirtualBox](https://www.virtualbox.org)
* [Vagrant](https://vagrantup.com)

You might also want to install [vagrant-vbguest](https://github.com/dotless-de/vagrant-vbguest) to update the guest extensions to match your host system on vagrant up.

```bash
vagrant plugin install vagrant-vbguest
```

## Starting LibreTime Vagrant

To get started you clone the repo and run `vagrant up`.

```bash
git clone https://github.com/libretime/libretime.git
cd libretime
vagrant up
```

If everything works out, you will find LibreTime on [port 9080](http://localhost:9080), icecast on [port 8000](http://localhost:8000) and the docs on [port 8888](http://localhost:8888).

Once you reach the web setup GUI you can click through it using the default values. To connect to the vagrant machine you can run `vagrant ssh` in the libretime directory.

## Troubleshooting

If anything fails during the initial provisioning step you can try running `vagrant provision` to rerun the installer.
