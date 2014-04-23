#!/bin/bash -e
#-e Causes bash script to exit if any of the installers
#return with a non-zero return value.

if [[ $EUID -ne 0 ]]; then
    echo "Please run as root user."
    exit 1
fi

set +e
dist=`lsb_release -is`
echo "Generating locales"
for i in `ls /usr/share/airtime/locale | grep ".._.."`; do
    if [ "$dist" = "Debian" ]; then
        grep -qi "^$i" /etc/locale.gen
        if [ $? -ne 0 ]; then
            echo "$i.UTF-8 UTF-8" >> /etc/locale.gen
        fi
    else
        locale-gen "$i.utf8"
    fi
done
set -e

if [ "$dist" = "Debian" ]; then
    /usr/sbin/locale-gen
fi

# Absolute path to this script, e.g. /home/user/bin/foo.sh
SCRIPT=`readlink -f $0`
# Absolute path this script is in, thus /home/user/bin
SCRIPTPATH=`dirname $SCRIPT`

AIRTIMEROOT=$SCRIPTPATH/../../

if [ "$mediamonitor" = "t" ]; then
    python $AIRTIMEROOT/python_apps/media-monitor/install/media-monitor-initialize.py
fi
if [ "$pypo" = "t" ]; then
    python $AIRTIMEROOT/python_apps/pypo/install/pypo-initialize.py
fi

chmod 600 /etc/monit/conf.d/monit-airtime-generic.cfg
chmod 600 /etc/monit/conf.d/monit-airtime-liquidsoap.cfg
if [ "$mediamonitor" = "t" ]; then
    chmod 600 /etc/monit/conf.d/monit-airtime-media-monitor.cfg
fi
chmod 600 /etc/monit/conf.d/monit-airtime-playout.cfg
chmod 600 /etc/monit/conf.d/monit-airtime-liquidsoap.cfg

# Start monit if it is not running, or restart if it is.
# Need to ensure monit is running before Airtime daemons are run. This is
# so we can ensure they can register with monit to monitor them when they start.
# If monit is already running, this step is still useful as we need monit to
# reload its config files.
invoke-rc.d monit restart

#give monit some time to boot-up before issuing commands
sleep 1

set +e
if [ "$mediamonitor" = "t" ]; then
    monit monitor airtime-media-monitor
fi
if [ "$pypo" = "t" ]; then
    monit monitor airtime-playout
    monit monitor airtime-liquidsoap
fi
set -e
