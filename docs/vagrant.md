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
vagrant up ubuntu-trusty
```

If everything works out, you will find LibreTime on [port 8080](http://localhost:8080), icecast on [port 8000](http://localhost:8000) and the docs on [port 8888](http://localhost:8888).

Once you reach the web setup GUI you can click through it using the default values. To connect to the vagrant machine you can run `vagrant ssh ubuntu-trusty` in the libretime directory.

## Alternative OS installations

With the above instructions LibreTime is installed on Ubuntu Trusty Tahir. The Vagrant setup offers the option to choose a different operation system according to you needs.

| OS     | Command             | Comment |
| ------ | ------------------- | ------- |
| Ubuntu 14.04 | `vagrant up ubuntu-trusty` | Current default install since it was used by legacy upstream, based on Trusty Tahir. |
| Debian 8.7   | `vagrant up debian-jessie` | Recommended install on Jessie as per the docs. |
| Ubuntu 16.04 | `vagrant up ubuntu-xenial` | Experimental install on current Ubuntu Xenial Xerus. |
| Debian 7.11  | `vagrant up debian-wheezy` | Recommended install on Wheezy as per the docs. |
| CentOS | `vagrant up centos` | Experimental install on 7.3 with native systemd support and activated SELinux. |
| Ubuntu | `vagrant up ubuntu` | Deprecated Ubuntu Trusty install, replaced by `ubuntu-trusty`. Do not use for new installs! |
| Debian | `vagrant up debian` | Deprecated Debian Jessie install, replaced by `debian-jessie`. Do not use for new installs! |

## Troubleshooting

If anything fails during the initial provisioning step you can try running `vagrant provision` to rerun the installer.

If you only want to re-run parts of the installer, use `--provision-with install`. The supported steps are `prepare`, `install`, `install-mkdocs` and `start-mkdocs`.
