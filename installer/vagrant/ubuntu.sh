#!/bin/bash

# Install modern alsa module for snd-hda-intel
# slightly modernized from  https://github.com/naomiaro/vagrant-alsa-audio
# https://wiki.ubuntu.com/Audio/UpgradingAlsa/DKMS
alsa_deb="oem-audio-hda-daily-dkms_0.201704090301~ubuntu14.04.1_all.deb"
wget -nv https://code.launchpad.net/~ubuntu-audio-dev/+archive/ubuntu/alsa-daily/+files/${alsa_deb}
dpkg -i ${alsa_deb}
rm ${alsa_deb}
DEBIAN_FRONTEND=noninteractive apt-get -y -m --force-yes install alsa

usermod -a -G audio vagrant
# liquidsoap runs as apache
usermod -a -G audio www-data

