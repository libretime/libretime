---
title: Unreleased
---

import ReleaseHead from './\_release-head.md';

<!-- <ReleaseHead date='2022-01-01' version='3.0.0-alpha.11'/> -->

## :sparkling_heart: Contributors

The LibreTime project wants to thank the following contributors for authoring PRs to this release:

## :rocket: Features

## :bug: Bug fixes

## :fire: Deprecation and removal

### Allowed CORS origins configuration location

The allowed CORS origins configuration moved from the database to the configuration file. The field in the general preference form is deprecated and will be removed in the next release. Be sure to move your allowed CORS origins configuration to the [configuration file](../admin-manual/setup/configuration.md).

## :arrow_up: Upgrading

### New configuration file

:::caution

Please run this **before the upgrade procedure**!

:::

The configuration file name changed from `airtime.conf` to `config.yml`. Please rename your configuration file using the following command:

```bash
sudo mv /etc/airtime/airtime.conf /etc/airtime/config.yml
```

The configuration directory changed from `/etc/airtime` to `/etc/libretime`. Please rename your configuration directory using the following command:

```bash
sudo mv /etc/airtime /etc/libretime
```

The configuration file format changed to `yml`. Please rewrite your [configuration file](../admin-manual/setup/configuration.md) using the [yaml format](https://yaml.org/). An example configuration file `installer/config.yml` is present in the sources.

### Apache and PHP configuration files

:::caution

Please run this **before the upgrade procedure**!

:::

The Apache configuration file has been updated and renamed, in addition the PHP configuration has been merged in the Apache configuration. The old configuration files must be removed from the system:

```bash
# On Debian/Ubuntu systems
sudo rm -f /etc/apache2/sites-*/airtime*
sudo rm -f /etc/php/*/apache2/conf.d/airtime.ini

# On CentOS systems
sudo rm -f /etc/httpd/conf.d/airtime*
sudo rm -f /etc/php.d/airtime.ini
```

### Shared files path

:::caution

Please run this **before the upgrade procedure**!

:::

The shared files path changed from `/usr/share/airtime` to `/usr/share/libretime`. The directory must be renamed:

```bash
sudo mv /usr/share/airtime /usr/share/libretime
```

### Replaced uWSGI with Gunicorn

[uWSGI](https://uwsgi-docs.readthedocs.io) was replaced by [Gunicorn](https://gunicorn.org/), the packages and configuration file should be removed from the system:

```bash
# Remove the configuration file
sudo rm -f /etc/airtime/libretime-api.ini

# Remove the packages
sudo apt purge \
    uwsgi \
    uwsgi-plugin-python3
```

### Logrotate config filepath

The legacy logrotate config filepath was changed from `/etc/logrotate.d/airtime-php` to `/etc/logrotate.d/libretime-legacy`. The old configuration file must be removed:

```bash
# Remove the configuration file
sudo rm -f /etc/logrotate.d/airtime-php
```

### Worker user

The worker service no longer uses a dedicated `celery` user to run. The old `celery` user can be removed from the system:

```bash
# Remove the celery user
sudo deluser celery
```

### New configuration schema

The configuration schema was updated.

The `general` section has been changed:

- the `general.protocol`, `general.base_url`, `general.base_port`, `general.base_dir` and `general.force_ssl` entries were replaced with a single `general.public_url` entry, be sure to use a valid url with the new configuration entry.

A new `storage` section has been added:

- the `storage.path` entry was added to move the storage configuration from the database to the configuration file, be sure to edit your configuration with the path to your storage. The default storage path value is `/srv/libretime`.

## :warning: Known issues

The following issues may need a workaround for the time being. Please search the [issues](https://github.com/libretime/libretime/issues) before reporting problems not listed below.

## :memo: Colophon
