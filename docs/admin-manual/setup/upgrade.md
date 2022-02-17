---
title: Upgrade
sidebar_position: 80
---

:::caution

While upgrading your installation may not cause any station downtime or data loss, always plan for the worst. Only upgrade your installation when LibreTime isn't playing out shows, notify your DJs and essential personnel, and back up your database, configuration files, and media library before you make any changes.

:::

1. [Back up the server](/docs/server-config/backing-up-the-server) and make a copy of all the configuration files under `/etc/airtime/`.
2. Run `./install -fiap` as described in the [installation guide](/docs/getting-started/install).
   This will detect an existing LibreTime deployment and backup any
   configuration files that if finds. The install script also tries to restart
   the needed services during an upgrade. In any case you should monitor if this
   happened and also take a quick look at the logs files to be sure everything
   is still fine. Now might be the time to reboot the system or virtual machine
   LibreTime is running on since regular reboots are part of a healthy system
   anyway.
3. Log into the new version of the LibreTime administration interface. If the
   playout engine starts up and detects that a show should be playing at the
   current time, it will skip to the correct point in the current time and start
   playing. If you encounter issues trying to connect to the new administration
   interface, you may need to clear your web browser's cache.

### Migrating from Airtime

:::note

Airtime's _linked files_ and _watched folders_ features currently do not work in LibreTime.

:::

LibreTime has dropped support for Ubuntu 16.04, which is the last supported
version of Ubuntu that Airtime supports. The following instructions describe how
to migrate from Airtime to LibreTime. If there are issues encountered while
upgrading, please [file a bug](https://github.com/libretime/libretime/issues/new?labels=bug&template=bug_report.md)

1. Take a [backup of the server](/docs/server-config/backing-up-the-server)
2. Create a new system for LibreTime and run the install script, as described in the [install guide](/docs/getting-started/install).
3. Before running the web-configuration, restore the Airtime database to the new
   PostgreSQL server, media database and configuration file
4. Update the configuration file to match the new configuration schema and update any
   changed values. See the [host configuration](/docs/server-config/host-configuration) documentation
   for more details.
5. Edit the Icecast password in `/etc/icecast2/icecast.xml` to reflect the
   password used in Airtime
6. Restart the LibreTime services
7. Open LibreTime's dashboard and verify all services are running
