#!/bin/bash

echo "Are you sure? Press Enter to continue..."
read

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
rm -rf "/var/tmp/airtime"
rm -rf "/opt/pypo"
rm -rf "/opt/recorder"
rm -rf "/srv/airtime"

echo "DROP DATABASE AIRTIME;" | su postgres -c psql
