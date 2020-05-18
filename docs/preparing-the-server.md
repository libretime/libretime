# Preparing the Server

The following instructions assume that you have root access (**sudo** on most
distributions) to a GNU/Linux server, and are familiar with basic command line
tasks.

The recommended LibreTime server platform is Ubuntu Server 18.04 LTS. The server should have at least a 1GHz
processor, 2GB of RAM, and a _wired_ ethernet connection. A soundcard is only required if you plan to
output audio directly to a mixing console instead of/in addition to using the onboard Icecast2 server.

The LibreTime installation does not use much disk space, but you should allow
plenty of storage capacity for the LibreTime library. A hot-swap RAID array is
recommended for media storage to mitigate the effects of disk failure. You should also consider
a UPS or other battery-powered system to offer some protection against
short-term power failures.

LibreTime depends on infrastructure and services that need to be configured
properly for it to run smoothly. This chapter will go through the individual
parts of a LibreTime install and help you assess how you need to manage them.

Netplan
-------

Starting in Ubuntu 18.04 LTS, network settings are managed by the Netplan daemon (more info [here](https://netplan.io/)). The Netplan config file is written in yaml and located at */etc/netplan/...*; if no yaml file is present, create one with a name like `##-netcfg.yaml` where ## is a number of your choice.

An example Netplan config looks like this:
```
network:
  version: 2
  renderer: networkd
  ethernets:
    enp3s0:
      addresses: [192.168.88.8/24]
      gateway4: 192.168.88.1
      nameservers:
        search: [lan]
        addresses: 192.168.88.1
```

In this example, `enp3s0` is the name of your network card; check to see what your network card's name is by running `ip -a` or `ifconfig`. Spacing in Netplan config files is two (2) spaces per indent. Using tabs will prevent the Netplan config from starting correctly.

- List your desired static IP address under `addresses` in the XXX.XXX.XXX.XXX/YY format (for more information on this, see [this subreddit thread](https://www.reddit.com/r/AskTechnology/comments/1r9x2f/how_does_the_ip_range_format_xxxxxxxxxxxxyy_work/)).
  - If your subnet mask is *255.255.255.0* then your IP address will end in `/24`, just like the example above.
- Set your DNS server under `gateway4` (this will likely be your router's IP address)
- Set your gateway under `nameservers -> addresses`

Once your Netplan config is set up correctly, run `sudo netplan apply` to update the configuration. Check that your IP address is set to the specified address with `ifconfig` and check to see if you are connected to the internet properly by pinging a known IP (ex. `ping 1.1.1.1`, Cloudflare's server) or by running `sudo apt update`. If no errors appear, than your server's IP is configured correctly.

Firewall
--------

LibreTime should only be run on a Server behind a firewall. This can either be a
dedicated firewall in the network (like on some cloud providers) or a local
firewall running iptables (as you would use on a root server or a local
machine).

Setting up a local firewall is done differently on all the supported distros.

* [Debian](https://wiki.debian.org/DebianFirewall)
* [FirewallD](http://www.firewalld.org/) (CentOS)
* [Ubuntu](https://help.ubuntu.com/lts/serverguide/firewall.html)
  * To quickly configure Ubuntu's firewall, `ufw`:
  ```
sudo ufw enable
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw allow 8000/tcp #only if streaming from your server
sudo ufw allow 8001/tcp #only if DJs will be directly connecting to stream in ports, also include 8002/tcp
sudo ufw status #to check setup
  ```

There are a vast amount of ways to configure your network, firewall included.
Choose the way that best fits your deployment and don't expose internal parts of your
LibreTime install on the network.

The following ports are relevant to LibreTime's core services and need to be opened to varying
degrees.

| Port | Description |
| ---- | ----------- |
| 80 | Default unsecure web port. Needs to be open for the webserver to serve the LibreTime webinterface or if you enable TLS a redirect to the secure web port.|
| 443 | Default secure web port. This is where your LibreTime webinterface lives if you choose to configure TLS.|
| 8000 | Main Icecast instance. This is where your listeners connect if you plan on using your LibreTime server to directly serve such connections. You can also configure external Icecast or ShoutCast instances for this later.|
| 8001 and 8002 | Master and Show source input ports. Only open these ports if you plan on letting anyone use these features.|

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
[roles](https://www.postgresql.org/docs/7.0/img/newuser.htm) and
[databases](https://www.postgresql.org/docs/10/img/managing-databases.html).

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

Services
---------

Once all of the services needed to run LibreTime are installed and configured,
it is important that the server starts them during the boot process, to cut down on downtime, especially in live enviornments.
Ubuntu 18.04 uses the `systemctl` command to manage services, so run the following commands to enable all
LibreTime-needed services to run at boot:

```
sudo systemctl enable libretime-liquidsoap
sudo systemctl enable libretime-playout
sudo systemctl enable libretime-celery
sudo systemctl enable libretime-analyzer
sudo systemctl enable apache2
sudo systemctl enable rabbitmq-server
```

If an error is returned, try adding `.service` to the end of each command. For example:

```
sudo systemctl enable apache2.service
```

User groups
------------

If you plan to have LibreTime output audio directly to a mixing console or transmitter, the `www-data` user needs to be added to the `audio` user group using `sudo adduser www-data audio`. Otherwise, if an Icecast or Shoutcast server is going to be used without an analog audio output, this step can be omitted.


Next steps
----------

After completing this guide, please complete the [Setting the server time](manual/setting-the-server-time/index)
guide before continuing to the installer.