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

BIN_DIR=/usr/lib/airtime/api_clients

#copy api_client files
cp -R $SCRIPTPATH/../../api_clients $BIN_DIR
