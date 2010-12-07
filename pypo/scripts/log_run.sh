#!/bin/sh

DATE=$(date '+%Y-%m-%d')
CI_LOG=/var/log/obp/ci/log-$DATE.php

clear
echo 
echo "##############################"
echo "# STARTING PYPO MULTI-LOG    #"
echo "##############################"
sleep 1 
clear

# split
multitail -s 2 -cS pyml /var/log/obp/pypo/debug.log \
-cS pyml /var/log/obp/pypo/error.log \
-l "tail -f -n 50 $CI_LOG | grep API"  \
/var/log/obp/ls/ls_script.log 
