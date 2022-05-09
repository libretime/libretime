#!/usr/bin/env bash

# Setup apt-cacher-ng proxy
sed --in-place 's|http://deb\.debian\.org|http://cdn-fastly.deb.debian.org|g' /etc/apt/sources.list
DEBIAN_FRONTEND=noninteractive apt-get update --allow-releaseinfo-change
DEBIAN_FRONTEND=noninteractive apt-get -y -qq install auto-apt-proxy

# Install utils
DEBIAN_FRONTEND=noninteractive apt-get -y -qq install alsa-utils vim
usermod -a -G audio vagrant
usermod -a -G audio www-data
