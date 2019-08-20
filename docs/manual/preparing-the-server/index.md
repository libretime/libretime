# Preparing the Server

The following instructions assume that you have root access (**sudo** on most
distributions) to a GNU/Linux server, and are familiar with basic command line
tasks.

The recommended LibreTime server platform is Ubuntu 16.04 LTS (Xenial Xerus).

The server should have at least a 1GHz processor and 1GB of RAM, preferably 2GB
RAM or more. If you are using a desktop environment and web browser directly on
the server you should install at least 2GB RAM, to avoid swapping to disk.

The LibreTime installation does not use much disk space, but you should allow
plenty of storage capacity for the LibreTime library. A hot-swap RAID array is
recommended for media storage, in case of disk failure. You should also consider
a UPS or other battery-powered system to offer some protection against
short-term power failures.

LibreTime depends on infrastructure and services that need to be configured
properly for it to run smoothly. This chapter will go through the individual
parts of a LibreTime install and help you assess how you need to manage them.

Firewall
--------

LibreTime should only be run on a Server behind a firewall. This can either be a
dedicated firewall in the network (like on some cloud providers) or a local
firewall running iptables (as you would use on a root server or a local
machine).

Setting up a local firewall is done differently on all the supported distros.

* [Debian](https://wiki.debian.org/DebianFirewall)
* [Ubuntu](https://help.ubuntu.com/lts/serverguide/firewall.html)
* [FirewallD](http://www.firewalld.org/) (CentOS)

There is a vast amount of ways to configure your network, firewall included.
Choose the way that best fits your deploy and dont internal expose parts of your
LibreTime install on the network.

The following ports are relevant to the deploy and need to be opened to varying
degrees.

| Port | Description |
| ---- | ----------- |
| 80 | Default unsecure web port. Needs to be open for the webserver to serve the LibreTime webinterface or if you enable TLS a redirect to the secure web port.|
| 443 | Default secure web port. This is where your LibreTime webinterface lives if you choose to configure TLS.|
| 8000 | Main Icecast instance. This is where your listeners connect if you plan on using your LibreTime server to directly serve such connections. You can also configure external Icecast or ShoutCast instances for this later.|
| 8001 and 8002 | Master and Show source input ports. Only open these ports if you plan on letting anyone use these features. You might want to consider usinga fixed IP if you use the master source for studio connections on port 8001 and only allow DJ to connect over a VPN link or similar depending your security needs.|

The remaining parts of LibreTime might open additional ports that should not be
accessible from any untrusted networks. You should consider how to configure
their firewall access individually once you configure them.

PostgreSQL
----------

You should set up PostgreSQL properly according to the instructions for your
distro. Distro provided packages are fine for LibreTime as it does not have any
specific version dependencies.

* [Debian](https://wiki.debian.org/PostgreSql)
* [Ubuntu](https://help.ubuntu.com/lts/serverguide/postgresql.html)
* [CentOS](https://www.postgresql.org/download/linux/redhat/)

You should restrict access to the database and create a user specific to
LibreTime. The default installer script creates a user with the password
`airtime`, which is far too open and should be replaced with a self provided user
and a secure password. See the PostgreSQL docs on
[roles](https://www.postgresql.org/docs/7.0/static/newuser.htm) and
[databases](https://www.postgresql.org/docs/10/static/managing-databases.html).

Please take care to ensure that your database is properly backed up at regular
intervals. LibreTime uses the database to store your schedule, settings, playout
history and more. See [backing up the server](../backing-up-the-server) for more
information on this.

RabbitMQ
--------

LibreTime uses RabbitMQ interfacing various components like the main interface
and parts of the system like the audio analyzer and podcast downloader as well
as the playout system through a common message queue. Again, the version
provided by your distro will probably work fine for all LibreTime is concerned.

* [Debian/Ubuntu](https://www.rabbitmq.com/install-debian.html)
* [CentOS](https://www.rabbitmq.com/install-rpm.html)

The install script sets `airtime` as the password on the default user. It is
recommended to provide your own user using a secure password.

You can use [`rabbitmqctl`](https://www.rabbitmq.com/man/rabbitmqctl.1.man.html)
or the [Management Plugin](https://www.rabbitmq.com/management.html) to manage
your RabbitMQ users.

There is no state in the RabbitMQ system that you need to backup but you want to
ensure that your RabbitMQ configuration and user permissions are safe.

### RabbitMQ hostname

RabbitMQ requires a fixed and resolvable hostname (see
[the docs](http://www.rabbitmq.com/ec2.html#issues-hostname)), which is normal
for a server. For a desktop or laptop machine where the hostname changes
frequently or is not resolvable, this issue may prevent RabbitMQ from starting.
When using a desktop or laptop computer with a dynamic IP address, such as an
address obtained from a wireless network, the `rabbitmq-server` daemon must not
start up before the `NetworkManager` service or `network.target`.  You may also
choose to configure RabbitMQ to only listen on the loopback interface with a
localhost node name. You can use [environment variables](https://www.rabbitmq.com/configure.html#define-environment-variables)
or a configuration file to do this.

See these links for more information:

* [Networking and RabbitMQ](https://www.rabbitmq.com/networking.html)
* [Serverfault Instructions for Debian](https://serverfault.com/a/319166)
