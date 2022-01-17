#!/usr/bin/env bash

sed --in-place 's|http://deb\.debian\.org|http://cdn-fastly.deb.debian.org|g' /etc/apt/sources.list

DEBIAN_FRONTEND=noninteractive apt-get update --allow-releaseinfo-change
DEBIAN_FRONTEND=noninteractive apt-get -y install alsa-utils auto-apt-proxy

usermod -a -G audio vagrant
usermod -a -G audio www-data
