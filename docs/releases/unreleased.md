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

## :arrow_up: Upgrading

### Apache and PHP configuration files

The Apache configuration file has been updated and renamed, in addition the PHP configuration has been merged in the Apache configuration. The old configuration files must be removed from the system **before the upgrade procedure**:

```bash
# On Debian/Ubuntu systems
sudo rm -f /etc/apache2/sites-*/airtime*
sudo rm -f /etc/php/*/apache2/conf.d/airtime.ini

# On CentOS systems
sudo rm -f /etc/httpd/conf.d/airtime*
sudo rm -f /etc/php.d/airtime.ini
```

## :warning: Known issues

The following issues may need a workaround for the time being. Please search the [issues](https://github.com/libretime/libretime/issues) before reporting problems not listed below.

## :memo: Colophon
