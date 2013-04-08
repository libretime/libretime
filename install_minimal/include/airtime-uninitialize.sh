#!/bin/bash
#-e Causes bash script to exit if any of the installers
#return with a non-zero return value.

if [[ $EUID -ne 0 ]]; then
    echo "Please run as root user."
    exit 1
fi

# Absolute path to this script, e.g. /home/user/bin/foo.sh
SCRIPT=`readlink -f $0`
# Absolute path this script is in, thus /home/user/bin
SCRIPTPATH=`dirname $SCRIPT`
AIRTIMEROOT=$SCRIPTPATH/../../

#unmonitor services
echo "Unmonitoring Airtime Services"
set +e
monit unmonitor airtime-media-monitor >/dev/null 2>&1
monit unmonitor airtime-liquidsoap >/dev/null 2>&1
monit unmonitor airtime-playout >/dev/null 2>&1
set -e

#uninitialize Airtime services
python $AIRTIMEROOT/python_apps/pypo/install/pypo-uninitialize.py
python $AIRTIMEROOT/python_apps/media-monitor/install/media-monitor-uninitialize.py

if [ "$purge" = "t" ]; then
#call Airtime uninstall script
php --php-ini ${SCRIPTPATH}/../airtime-php.ini ${SCRIPTPATH}/airtime-uninstall.php
fi
