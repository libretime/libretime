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

### The `general.secret_key` configuration field is required

The `general.secret_key` field in the [configuration file](../admin-manual/configuration.md#general) is now **required**, to prevent reusing the `general.api_key` for cryptographic usage.

### The `stream.outputs.system[].kind` configuration field now defaults to `pulseaudio`

The `stream.outputs.system[].kind` field in the [configuration file](../admin-manual/configuration.md#general) default value changed from `alsa` to `pulseaudio`. Make sure to update your configuration file if you rely on the default system output.

## :warning: Known issues

The following issues may need a workaround for the time being. Please search the [issues](https://github.com/libretime/libretime/issues) before reporting problems not listed below.

## :memo: Colophon
