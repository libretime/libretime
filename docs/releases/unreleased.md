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

### Repair broken track types

:::caution

Please run this **before the upgrade procedure**!

:::

The database files track type field was previously not constrained and this might have lead to files referencing a now renamed or missing track type. To preserve as much data as possible during the database migration process, you need to check whether some files have broken or missing track type references and fix them accordingly. To list broken track type references, you can run the following command:

```bash
sudo -u libretime libretime-api dbshell --command="
    SELECT f.id, f.track_type, f.track_title, f.artist_name, f.filepath
    FROM cc_files f
    WHERE NOT EXISTS (
        SELECT FROM cc_track_types tt
        WHERE tt.code = f.track_type
    )
    AND f.track_type IS NOT NULL
    AND f.track_type <> '';"
```

If the above command outputs the following, no file needs fixing.

```
 id | track_type | track_title | artist_name | filepath
----+------------+-------------+-------------+----------
(0 rows)
```

In addition, the database smart block criteria value was previously referencing track types using codes, and should now reference track types using ids. You need to check whether some smart block have broken track type references and fix them accordingly. To list broken track type references, you can run the following command:

```bash
sudo -u libretime libretime-api dbshell --command="
    SELECT b.name, c.criteria, c.modifier, c.value
    FROM cc_blockcriteria c, cc_block b
    WHERE c.block_id = b.id
    AND NOT EXISTS (
        SELECT FROM cc_track_types tt
        WHERE tt.code = c.value
    )
    AND criteria = 'track_type';"
```

If the above command outputs the following, no smart block needs fixing.

```
 name | criteria | modifier | value
------+----------+----------+-------
(0 rows)
```

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

### Nginx, Apache and PHP

:::caution

Please run this **before the upgrade procedure**!

:::

The `apache2` web server has been replaced with `nginx` and `php-fpm`, be sure to uninstall `apache2` and clean related configuration files:

```bash
sudo rm -f /etc/apache2/sites-*/{airtime,libretime}*
sudo rm -f /etc/php/*/apache2/conf.d/{airtime,libretime}*

sudo apt purge apache2 'libapache2-mod-php*'

sudo rm -f /var/lib/php/sessions/sess_*
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

### LibreTime user

The LibreTime services now run using a dedicated `libretime` user instead of the default `www-data` user. Be sure to change the ownership of the LibreTime files:

```bash
# Configuration directory
sudo chown -R libretime:libretime /etc/libretime
# Logs directory
sudo chown -R libretime:libretime /var/log/libretime
# Runtime directory
sudo chown -R libretime:libretime /var/lib/libretime
# Storage directory
sudo chown -R libretime:libretime /srv/libretime
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
