#!/bin/sh

clear
echo 
echo "##############################"
echo "# STARTING PYPO MULTI-LOG    #"
echo "##############################"
sleep 1 
clear

# split
multitail -s 2 /var/log/pypo/debug.log \
/var/log/pypo-push/log/main/current \
/var/log/pypo-fetch/log/main/current \
/var/log/pypo-liquidsoap/log/main/current
