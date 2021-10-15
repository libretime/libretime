![](https://github.com/LibreTime/libretime/raw/master/logo/logotype.png)

The complete LibreTime documentation is available at [libretime.org](http://libretime.org).

The full tarball for the `3.0.0-alpha.10` release of LibreTime is available [here](https://github.com/LibreTime/libretime/releases/download/3.0.0-alpha.10/libretime-3.0.0-alpha.10.tar.gz).

Since this is an alpha release there will be bugs in the code.

Please report new issues and/or feature requests in [the issue tracker](https://github.com/LibreTime/libretime/issues). Join our [discourse](https://discourse.libretime.org/) or chat to us on our [Mattermost instance](https://chat.libretime.org/e) if you need help and for general discussion.

## Table of Contents

- [Features](#features-3.0.0-alpha.10)
- [Bugfixes](#bugfixes-3.0.0-alpha.10)
- [Deprecated Features](#deprecated-3.0.0-alpha.10)
- [Contributors](#contributors-3.0.0-alpha.10")
- [Installation](#install-3.0.0-alpha.10")
- [Updating](#update-3.0.0-alpha.10")
- [Known Issues](#issues-3.0.0-alpha.10")
  - [Interface Customization Issues](#issues-interface-issues-3.0.0-alpha.10")
  - [No watched folder support](#issues-watched-3.0.0-alpha.10")
  - [No Line In recording support](#issues-line-in-3.0.0-alpha.10")
  - [Playout won't work if locale is missing](#issues-no-locale-3.0.0-alpha.10")

<a id="features-3.0.0-alpha.10"/>

## Features

- Support `force_ssl` configuration option in Python applications
- Move `airtime_mvc` to `legacy` and move all PHP related files under it
- Support `Authorization: Api-Key` header in API v1
- Use pip for LibreTime Python package installation
- Move Python scripts into `/usr/local/bin`
- Add REST API v2 (unstable and subject to change)

<a id="bugfixes-3.0.0-alpha.10">

## Bug Fixes

- Renamed airtime_analyzer to libretime-analyzer
- Prevent autoload playlists running on deleted show instances
- Playout history can be exported as CSV and PDF
- Explicitly fail if the HTTP requests made by the Python applications fail
- Fix API v2 schedule endpoint item filtering and overlapping playout
- Fix pypo overlapping track playout
- Fix installation when Icecast is already installed
- Request 1Gb of memory on libvirt Vagrant boxes
- Clean up CORS setup in the installer
- Pin the `setuptools` version to ensure older versions of LibreTime can still be installed

<a id="deprecated-3.0.0-alpha.10">

## Deprecated Features

- Removed broken Soundcloud integration
- Dropped support for Ubuntu Xenial as it is end-of-life
- Dropped support for Debian Stretch as it is end-of-life
- Removed SysV and Upstart init system files
- Removed broken My Podcasts feature

<a id="contributors-3.0.0-alpha.10">

## Contributors

The LibreTime project wants to thank the following contributors for authoring PRs to this release:

- @jooola
- @paddatrapper
- @xabispacebiker
- @malespiaut
- @zklosko
- @brekemeier
- @jeromelebleu
- @danielhjames
- @rjhelms
- @hairmare

<a id="install-3.0.0-alpha.10">

## Installation

The main installation docs may be found at [https://libretime.org/install/](https://libretime.org/install). They describe a "developer" install using the bundled `install` script.

We are preparing packages for supported distros and you can take those for a spin if you would like to. Usually the packages get built pretty soon after a release is published. If the current version is not available from the below sources you should wait for a while until they get uploaded.

- [Ubuntu packages](https://github.com/LibreTime/libretime-debian-packaging/releases)
- [Debian packages](https://github.com/LibreTime/libretime-debian-packaging/releases)
- [CentOS packages](https://build.opensuse.org/package/show/home:radiorabe:airtime/libretime)

Please reference these links for further information on how to install from packages. The install docs will get updated to show how to install packages once we have validated that the packages work properly and when the packages are available from a repository allowing you to automate updating to a new version.

If you want to skip the installer GUI completely you can configure LibreTime using `legacy/build/airtime.example.conf` as an template. Due to some python/PHP differences you must remove all comments from the example to use it 😞. You'll also have to create some folder structures manually and check if the music dir got properly created directly in the database. Referencing a second `install -fiap` install on a non productive system for reference can help with this type of bootstrap.

<a id="update-3.0.0-alpha.10">

## Updating

See [the docs](https://libretime.org/docs/upgrading) for complete information on updating. Please ensure that you have proper [backups](https://libretime.org/docs/backing-up-the-server) and a rollback scenario in place before updating.
If the update does not go smoothly, it may cause significant downtime, so you should always have a fallback system available during the update to ensure broadcast continuity.

If you installed from GitHub you can `git pull` in your local working copy and re-run the `./install` script with the same `--web-root` and `--web-user` arguments you used during the initial install. Tarball users can leave out the git pull part and just call the new version of the install script.

Once the update has taken place, you will need to run the following commands to clean up old scripts and configuration:

```
# Remove the old packages
sudo pip3 uninstall \
  airtime-playout \
  airtime-celery \
  api_clients

# Remove old entrypoints
sudo rm -f \
  /usr/bin/airtime-liquidsoap \
  /usr/bin/airtime-playout \
  /usr/bin/pyponotify

# Remove old config files
sudo rm -f \
  /etc/logrotate.d/airtime-liquidsoap

# Remove the old runtime and PHP directories
sudo rm -rf \
  /var/run/airtime \
  /run/airtime \
  /usr/share/airtime/php/airtime_mvc

# Remove old python libraries entrypoints
sudo rm -f \
  /usr/bin/airtime-liquidsoap \
  /usr/bin/airtime-playout \
  /usr/bin/libretime-analyzer \
  /usr/bin/libretime-api \
  /usr/bin/collectiongain \
  /usr/bin/django-admin \
  /usr/bin/django-admin.py \
  /usr/bin/markdown_py \
  /usr/bin/mid3cp \
  /usr/bin/mid3iconv \
  /usr/bin/mid3v2 \
  /usr/bin/moggsplit \
  /usr/bin/mutagen-inspect \
  /usr/bin/mutagen-pony \
  /usr/bin/pyponotify \
  /usr/bin/replaygain \
  /usr/bin/sqlformat
```

<a id="issues-3.0.0-alpha.10">

## Known Issues

The following issues may need a workaround for the time being. Please search the [issues](https://github.com/LibreTime/libretime/issues) before reporting problems not listed below.

<a id="issues-interface-issues-3.0.0-alpha.10">

### Interface Customization Issues

The UI works best if you don't use it in an opinionated fashion and change just the bare minimal.

<a id="issues-watched-3.0.0-alpha.10">

### No watched folder support

Currently LibreTime does not support watching folders. Uploading files through the web interface works fine and can be automated via a REST API. Re-implementing watched folder support is on the roadmap. Please consider helping out with the code to help speed things along if you want to use the feature. This is tracked in [#70](https://github.com/LibreTime/libretime/issues/70).

<a id="issues-line-in-3.0.0-alpha.10">

### No line in support

This feature went missing from LibreTime due to the fact that we based our code off of the saas-dev branch of legacy upstream and support for recording hasn't been ported to the new airtime analyzer ingest system. #42 currently tracks the progress being made on line in recording. This is tracked in [#42](https://github.com/LibreTime/libretime/issues/42).

<a id="issues-no-locale-3.0.0-alpha.10">

### Playout won't work if locale is missing

Some minimal OS installs do not have a default locale configured. This only seems to affect some VPS installs as they often do not have a locale setup in the default images provided. This is tracked in [#317](https://github.com/LibreTime/libretime/issues/317).

You can set up the locale using a combination of the following commands. You might also want to consult the documentation of your VPS provider as it may contain an official way to set up locales when provisioning a VPS.

```bash
# Set locale using systemds localectl
localectl set-locale LANG="en_US.utf8"
```

These instructions do not seem to work on all Debian based distros so you might need to use `update-locale` as follows.

```
#Purge all locales but en_US.UTF-8
sudo locale-gen --purge en_US.UTF-8
#Populate LANGUAGE=
sudo update-locale LANGUAGE="en_US.UTF-8"
```

<a id="#issues-no-i18n-3.0.0-alpha.10">
