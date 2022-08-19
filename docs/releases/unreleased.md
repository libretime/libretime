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
