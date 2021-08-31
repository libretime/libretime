#!/usr/bin/env bash

DEBIAN_FRONTEND=noninteractive apt-get update --allow-releaseinfo-change
DEBIAN_FRONTEND=noninteractive apt-get -y install alsa-utils
usermod -a -G audio vagrant
usermod -a -G audio www-data
