---
layout: article
title: Upgrading Libretime
category: admin
---

## LibreTime versioning

In a nutshell, given a version number MAJOR.MINOR.PATCH we increment the:

1. MAJOR version when we make incompatible API changes,
2. MINOR version when we add functionality in a backwards-compatible manner, and
3. PATCH version when we make backwards-compatible bug fixes.

Any pre-release versions of LibreTime are denoted by appending a hyphen and a
series of dot separated identifiers immediately following the patch version.
This pre-release indicates that the version is unstable in a sense that it might
contain incomplete features or not satisfy the intended compatibility
requirements as per semver.

## Upgrading

> After your LibreTime server has been deployed for a few years, you may need to
> upgrade the GNU/Linux distribution that it runs in order to maintain security
> update support. If the upgrade does not go smoothly, it may cause significant
> downtime, so you should always have a fallback system available during the
> upgrade to ensure broadcast continuity.

1. Take a [backup of the server](/docs/backing-up-the-server). This is
   especially important if you have not already set up a regular back up routine.
   This extra back up is a safety measure in case of accidental data loss during
   the upgrade, for example due to the wrong command being entered when moving
   files. It is also recommended to backup all the configuration files under
   `/etc/airtime/`.
2. Run `./install -fiap` as described in the [install documentation](/install).
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

> **Note:** Airtime's _linked files_ and _watched folders_ features currently do
> not work in Libretime.

LibreTime has dropped support for Ubuntu 16.04, which is the last supported
version of Ubuntu that Airtime supports. The following instructions describe how
to migrate from Airtime to LibreTime. If there are issues encountered while
upgrading, please [file a bug](https://github.com/libretime/libretime/issues/new?labels=bug&template=bug_report.md)

1. Take a [backup of the server](/docs/backing-up-the-server)
2. Create a new system for LibreTime and run the install script, as described in
   [install](/install).
3. Before running the web-configuration, restore the Airtime database to the new
   PostgreSQL server, media database and configuration file
4. Edit the configuration file to update any changed values
5. Edit the Icecast password in `/etc/icecast2/icecast.xml` to reflect the
   password used in Airtime
6. Restart the LibreTime services
7. Navigate to the LibreTime web-page
