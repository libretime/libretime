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

## :arrow_up: Before upgrading

:::caution

Please follow this **before the upgrade procedure**.

:::

## :arrow_up: Upgrading

### Icecast mount default charset

During the first installation, the installer will configure Icecast to use UTF-8 as default charset for mounts. To upgrade an existing installation, you may want to manually apply the changes in the `/etc/icecast2/icecast.xml`:

```xml
    <mount type="default">
        <charset>UTF-8</charset>
    </mount>
```

Or if only want to set the charset to UTF-8 for specific mounts (for example `/main.mp3`):

```xml
    <mount type="normal">
        <mount-name>/main.mp3</mount-name>
        <charset>UTF-8</charset>
    </mount>
```

Please, see the [documentation for more details](../admin-manual/stream-configuration.md#utf-8-metadata-in-icecast-mp3-streams).

### Unused packages and dependencies

Starting with this release, LibreTime installs `gunicorn`, `uvicorn` and `pika` from PyPi. This renders some previously installed distribution packages useless. You can remove them from your system using the following command:

```bash
sudo apt remove gunicorn python3-gunicorn python3-httptools python3-uvicorn python3-uvloop python3-pika
```

In addition, LibreTime now install its dependencies in a python virtual environment. This renders the python packages that LibreTime installed globally useless. There is no clean way to remove those python packages, because there is no safe way to know if other tools depend on them. If you are experienced enough with CLI and system administration, you may prune the entire `/usr/local/lib/python3.*/dist-packages/` directory, but do this **at your own risk**. Those globally installed python packages may stay on the system, they shouldn't break LibreTime.

## :warning: Known issues

The following issues may need a workaround for the time being. Please search the [issues](https://github.com/libretime/libretime/issues) before reporting problems not listed below.

## :memo: Colophon
