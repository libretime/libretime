#!/bin/sh

# export home dir
export HOME=/home/liquidsoap/

# start liquidsoap with corresponding user & scrupt
sudo -u liquidsoap /usr/local/bin/liquidsoap ls_script.liq
