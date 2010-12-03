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
/var/svc.d/pypo_push/log/main/current \
/var/svc.d/pypo_fetch/log/main/current \
/var/svc.d/pypo_ls/log/main/current
