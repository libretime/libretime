#!/bin/bash

DEBIAN_FRONTEND=noninteractive apt-get -y -m --force-yes install alsa-utils
usermod -a -G audio vagrant
usermod -a -G audio www-data
