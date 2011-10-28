#!/bin/bash -e
#-e Causes bash script to exit if any of the installers
#return with a non-zero return value.

if [ `whoami` != 'root' ]; then
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

#rm -f /etc/airtime/airtime.conf
#rm -f /etc/airtime/api_client.cfg
#rm -f /etc/airtime/recorder.cfg
#rm -f /etc/airtime/media-monitor.cfg
#rm -f /etc/airtime/pypo.cfg
#rm -f /etc/airtime/liquidsoap.cfg

rm -f /etc/cron.d/airtime-crons

echo "API Client"
python $AIRTIMEROOT/python_apps/api_clients/install/api_client_uninstall.py
echo "Pypo"
python $AIRTIMEROOT/python_apps/pypo/install/pypo-remove-files.py
echo "Media-Monitor"
python $AIRTIMEROOT/python_apps/media-monitor/install/media-monitor-remove-files.py
echo "Show-Recorder"
python $AIRTIMEROOT/python_apps/show-recorder/install/recorder-remove-files.py

rm -rf /usr/lib/airtime

#remove symlinks
rm -f /usr/bin/airtime-import
rm -f /usr/bin/airtime-update-db-settings
rm -f /usr/bin/airtime-check-system
rm -f /usr/bin/airtime-log

rm -rf /usr/share/airtime

rm -rf /var/log/airtime
rm -rf /var/tmp/airtime
