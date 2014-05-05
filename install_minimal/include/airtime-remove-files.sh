#!/bin/bash

if [[ $EUID -ne 0 ]]; then
    echo "Please run as root user."
    exit 1
fi

########################################################################
# Complete list of files that we need to remove is available in
# airtime-copy-files.sh
#######################################################################


# Absolute path to this script, e.g. /home/user/bin/foo.sh
SCRIPT=`readlink -f $0`
# Absolute path this script is in, thus /home/user/bin
SCRIPTPATH=`dirname $SCRIPT`

AIRTIMEROOT=$SCRIPTPATH/../../

rm -f /etc/cron.d/airtime-crons
rm -f /etc/monit/conf.d/monit-airtime*
rm -f /etc/logrotate.d/airtime-php

echo "* API Client"
python $AIRTIMEROOT/python_apps/api_clients/install/api_client_uninstall.py
echo "* Pypo"
python $AIRTIMEROOT/python_apps/pypo/install/pypo-remove-files.py
echo "* Media-Monitor"
python $AIRTIMEROOT/python_apps/media-monitor/install/media-monitor-remove-files.py

#remove symlinks
rm -f /usr/bin/airtime-import
rm -f /usr/bin/airtime-check-system
rm -f /usr/bin/airtime-log
rm -f /usr/bin/airtime-test-soundcard
rm -f /usr/bin/airtime-test-stream
rm -f /usr/bin/airtime-silan

rm -rf /usr/lib/airtime
rm -rf /usr/share/airtime

rm -rf /var/log/airtime
rm -rf /var/tmp/airtime
