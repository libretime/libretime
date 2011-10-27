#!/bin/bash -e
#-e Causes bash script to exit if any of the installers
#return with a non-zero return value.

if [ `whoami` != 'root' ]; then
    echo "Please run as root user."
    exit 1
fi

#copy files to 
## /etc/airtime
#+ /etc/apache2/sites-available/airtime
#+ /etc/apache2/sites-enabled/airtime
## /etc/cron.d/
## /etc/init.d/
## /etc/monit/conf.d/
# /usr/lib/airtime/airtime_virtualenv
## /usr/lib/airtime/api_clients
## /usr/lib/airtime/media-monitor
# /srv/airtime/stor
## /usr/lib/airtime/pypo
## /usr/lib/airtime/show-recorder
## /usr/lib/airtime/utils
## /usr/bin/airtime-*
## /usr/share/airtime
## /var/log/airtime
## /var/tmp/airtime

# Absolute path to this script, e.g. /home/user/bin/foo.sh
SCRIPT=`readlink -f $0`
# Absolute path this script is in, thus /home/user/bin
SCRIPTPATH=`dirname $SCRIPT`

AIRTIMEROOT=$SCRIPTPATH/../../

mkdir -p /etc/airtime
cp $AIRTIMEROOT/airtime_mvc/build/airtime.conf /etc/airtime
cp $AIRTIMEROOT/python_apps/api_clients/api_client.cfg /etc/airtime
cp $AIRTIMEROOT/python_apps/show-recorder/recorder.cfg /etc/airtime
cp $AIRTIMEROOT/python_apps/media-monitor/media-monitor.cfg /etc/airtime
cp $AIRTIMEROOT/python_apps/pypo/pypo.cfg /etc/airtime
cp $AIRTIMEROOT/python_apps/pypo/liquidsoap_scripts/liquidsoap.cfg /etc/airtime

HOUR=$(($RANDOM%24))
MIN=$(($RANDOM%60))
echo "$MIN $HOUR * * * root /usr/lib/airtime/utils/phone_home_stat" > /etc/cron.d/airtime-crons

cp $AIRTIMEROOT/python_apps/show-recorder/airtime-show-recorder-init-d /etc/init.d/airtime-show-recorder
cp $AIRTIMEROOT/python_apps/media-monitor/airtime-media-monitor-init-d /etc/init.d/airtime-media-monitor
cp $AIRTIMEROOT/python_apps/pypo/airtime-playout-init-d /etc/init.d/airtime-playout

cp $AIRTIMEROOT/python_apps/monit/monit-airtime-generic.cfg /etc/monit/conf.d/
#cp $AIRTIMEROOT/python_apps/monit/monit-airtime-rabbitmq-server.cfg /etc/monit/conf.d/
cp $AIRTIMEROOT/python_apps/media-monitor/monit-airtime-media-monitor.cfg /etc/monit/conf.d/
cp $AIRTIMEROOT/python_apps/show-recorder/monit-airtime-show-recorder.cfg /etc/monit/conf.d/
cp $AIRTIMEROOT/python_apps/pypo/monit-airtime-liquidsoap.cfg /etc/monit/conf.d/
cp $AIRTIMEROOT/python_apps/pypo/monit-airtime-playout.cfg /etc/monit/conf.d/

python $AIRTIMEROOT/python_apps/api_clients/install/api_client_install.py
python $AIRTIMEROOT/python_apps/pypo/install/pypo-install-files.py
python $AIRTIMEROOT/python_apps/media-monitor/install/media-monitor-install-files.py
python $AIRTIMEROOT/python_apps/show-recorder/install/recorder-install-files.py

cp -R $AIRTIMEROOT/utils /usr/lib/airtime

#create symbolic links
ln -sf /usr/lib/airtime/utils/airtime-import/airtime-import /usr/bin/airtime-import
ln -sf /usr/lib/airtime/utils/airtime-update-db-settings /usr/bin/airtime-update-db-settings
ln -sf /usr/lib/airtime/utils/airtime-check-system /usr/bin/airtime-check-system
ln -sf /usr/lib/airtime/utils/airtime-log /usr/bin/airtime-log

mkdir -p /usr/share/airtime
cp -R $AIRTIMEROOT/airtime_mvc/* /usr/share/airtime/

mkdir -p /var/log/airtime
mkdir -p /var/tmp/airtime

#Finished copying files
