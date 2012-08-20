#!/bin/bash

if [[ $EUID -ne 0 ]]; then
   echo "This script must be run as root." 1>&2
   exit 1
fi

echo "This script deletes all traces of Airtime from your system,"
echo "including files uploaded through the web interface."
echo "It will delete files from all known versions of Airtime."
echo
echo "Are you sure you want to do this? Press Enter to continue..."
read

service airtime-playout stop >/dev/null 2>&1
service airtime-liquidsoap stop >/dev/null 2>&1
service airtime-media-monitor stop >/dev/null 2>&1
service airtime-show-recorder stop >/dev/null 2>&1

airtime-pypo-stop >/dev/null 2>&1
airtime-show-recorder-stop >/dev/null 2>&1

killall liquidsoap

rm -rf "/etc/airtime"
rm -rf "/var/log/airtime"
rm -rf "/etc/service/pypo"
rm -rf "/etc/service/pypo-liquidsoap"
rm -rf "/etc/service/recorder"
rm -rf "/usr/share/airtime"
rm -rf "/var/tmp/airtime"
rm -rf "/var/www/airtime"
rm -rf "/usr/bin/airtime-*"
rm -rf "/usr/lib/airtime"
rm -rf "/var/lib/airtime"
rm -rf "/var/tmp/airtime"
rm -rf "/opt/pypo"
rm -rf "/opt/recorder"
rm -rf "/srv/airtime"
rm -rf "/etc/monit/conf.d/airtime-monit.cfg"
rm -rf /etc/monit/conf.d/monit-airtime-*

echo "DROP DATABASE AIRTIME;" | su postgres -c psql
echo "DROP LANGUAGE plpgsql;" | su postgres -c psql
echo "DROP USER AIRTIME;" | su postgres -c psql
