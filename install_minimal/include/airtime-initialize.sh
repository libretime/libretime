#!/bin/bash -e
#-e Causes bash script to exit if any of the installers
#return with a non-zero return value.

if [ `whoami` != 'root' ]; then
    echo "Please run as root user."
    exit 1
fi

# Absolute path to this script, e.g. /home/user/bin/foo.sh
SCRIPT=`readlink -f $0`
# Absolute path this script is in, thus /home/user/bin
SCRIPTPATH=`dirname $SCRIPT`

AIRTIMEROOT=$SCRIPTPATH/../../

virtualenv_bin="/usr/lib/airtime/airtime_virtualenv/bin/"
. ${virtualenv_bin}activate

set +e
php --php-ini ${SCRIPTPATH}/../airtime-php.ini ${SCRIPTPATH}/airtime-install.php $@
result=$?

if [ "$result" -ne "0" ]; then
    #There was an error, exit with error code.
    echo "There was an error during install. Exit code $result"
    exit 1
fi
set -e

python $AIRTIMEROOT/python_apps/pypo/install/pypo-initialize.py
python $AIRTIMEROOT/python_apps/media-monitor/install/media-monitor-initialize.py
python $AIRTIMEROOT/python_apps/show-recorder/install/recorder-initialize.py

# Start monit if it is not running, or restart if it is.
# Need to ensure monit is running before Airtime daemons are run. This is
# so we can ensure they can register with monit to monitor them when they start.
# If monit is already running, this step is still useful as we need monit to
# reload its config files.
/etc/init.d/monit restart

#give monit some time to boot-up before issuing commands
sleep 1

set +e
monit monitor airtime-media-monitor
monit monitor airtime-liquidsoap
monit monitor airtime-playout
monit monitor airtime-show-recorder
#monit monitor rabbitmq-server
set -e
