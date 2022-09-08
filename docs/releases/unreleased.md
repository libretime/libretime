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

### Ubuntu Bionic support deprecation

Support for Ubuntu Bionic is being deprecated, and will be removed in LibreTime v3.1.0. Maintenance only versions (3.0.x) for Ubuntu Bionic will be provided until the distribution release reaches its end of life. Please see the [supported distributions release policy](../developer-manual/development/releases.md#distributions-releases-support) for details.

Along with the Ubuntu Bionic deprecation, the following dependencies versions are also being deprecated:

- [liquidsoap 1.1.1](https://packages.ubuntu.com/bionic/liquidsoap)
- [php7.2](https://packages.ubuntu.com/bionic/php7.2)
- [python3.6](https://packages.ubuntu.com/bionic/python3)

### Debian Buster support deprecation

Support for Debian Buster is being deprecated, and will be removed in LibreTime v3.1.0. Maintenance only versions (3.0.x) for Debian Buster will be provided until the distribution release reaches its end of life. Please see the [supported distributions release policy](../developer-manual/development/releases.md#distributions-releases-support) for details.

Along with the Debian Buster deprecation, the following dependencies versions are also being deprecated:

- [liquidsoap 1.3.3](https://packages.debian.org/buster/liquidsoap)
- [php7.3](https://packages.debian.org/buster/php7.3)
- [python3.7](https://packages.debian.org/buster/python3)

## :arrow_up: Before upgrading

:::caution

Please follow this **before the upgrade procedure**!

:::

### File based stream configuration

The stream configuration moved from the database to the [configuration](../admin-manual/setup/configuration.md#stream) file. A configuration sample can be found in the project folder under `installer/config.yml`. Make sure to save your existing stream config to the configuration file.

:::info

To prevent accidental data loss during upgrade, the stream configuration data will only be removed from the database in future releases. You can view the data using the following commands:

```bash
sudo -u libretime libretime-api dbshell --command="
    SELECT *
    FROM cc_stream_setting
    ORDER BY keyname;"

sudo -u libretime libretime-api dbshell --command="
    SELECT *
    FROM cc_pref
    WHERE keystr IN (
        'default_icecast_password',
        'default_stream_mount_point',
        'live_dj_connection_url_override',
        'live_dj_source_connection_url',
        'master_dj_connection_url_override',
        'master_dj_source_connection_url'
    )
    ORDER BY keystr;"
```

:::

### Timezone configuration

The timezone preference moved from the database to the [configuration](../admin-manual/setup/configuration.md#general) file. Make sure to save your existing timezone preference to the configuration file.

:::info

To prevent accidental data loss during upgrade, the timezone preference will only be removed from the database in future releases. You can view the data using the following commands:

```bash
sudo -u libretime libretime-api dbshell --command="SELECT * FROM cc_pref WHERE keystr = 'timezone'";
```

:::

## :arrow_up: Upgrading

### Worker python package and service

The `libretime-celery` python package and service was renamed to `libretime-worker`. Make sure to remove the old python package and service using the following command:

```bash
sudo pip3 uninstall libretime-celery

sudo rm -f \
    /etc/systemd/system/libretime-celery.service \
    /usr/lib/systemd/system/libretime-celery.service
```

## :warning: Known issues

The following issues may need a workaround for the time being. Please search the [issues](https://github.com/libretime/libretime/issues) before reporting problems not listed below.

## :memo: Colophon
