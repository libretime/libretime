---
layout: article
title: Upgrading Libretime
category: admin
---

## LibreTime versioning

LibreTime 3.x versions support upgrading from Airtime 2.5.x versions. LibreTime follows the [Semantic Versioning (semver)](http://semver.org/spec/v2.0.0.html) standards.

In a nutshell, given a version number MAJOR.MINOR.PATCH we increment the:

1. MAJOR version when we make incompatible API changes,
2. MINOR version when we add functionality in a backwards-compatible manner, and
3. PATCH version when we make backwards-compatible bug fixes.

Any pre-release versions of LibreTime are denoted by appending a hyphen and a series
of dot separated identifiers immediately following the patch version. This pre-release indicates
that the version is unstable in a sense that it might contain incomplete features or not satisfy the
intended compatibility requirements as per semver.

## Upgrading 

> After your LibreTime server has been deployed for a few years, you may need to
upgrade the GNU/Linux distribution that it runs in order to maintain security
update support. If the upgrade does not go smoothly, it may cause significant
downtime, so you should always have a fallback system available during the 
upgrade to ensure broadcast continuity.


Before upgrading a production LibreTime server, you should back up both the PostgreSQL
database and the storage server used by LibreTime. This is especially important if you have not already
set up a regular back up routine. This extra back up is a safety measure in case of accidental data loss
during the upgrade, for example due to the wrong command being entered when moving files. See
[Backing up the server](/docs/backing-up-the-server) in this manual for details of how to perform these back ups.

The LibreTime [installation script](/install) will detect an existing LibreTime or Airtime deployment and back up any configuration files that it finds. We recommend taking your own manual backups of the configuration yourself nevertheless.  The install script also tries to restart the needed services during an upgrade. In any case you should monitor if this happened and also take a quick look at the logs files to be sure everything is still fine. Now might be the time to reboot the system or virtual machine LibreTime is running on since regular reboots are part of a healthy system anyway.

After the upgrade has completed, you may need to clear your web browser's cache  before logging into the new version of the LibreTime administration interface. If the playout engine starts up and detects that a show should be playing at the  current time, it will skip to the correct point in the current item and start playing. 

There will be tested ways to switch from a LibreTime pre-release to a packaged version of LibreTime.

Airtime 2.5.x versions support upgrading from version 2.3.0 and above. If you are
running a production server with a version of Airtime prior to 2.3.0, you should
upgrade it to version 2.3.0 before continuing. 

> **Note:** Airtime's *linked files* and *watched folders* features currently do not work in Libretime.
