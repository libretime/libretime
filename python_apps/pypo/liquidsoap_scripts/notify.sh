#!/bin/bash
############################################
# just a wrapper to call the notifyer      #
# needed here to keep dirs/configs clean   #
# and maybe to set user-rights             #
############################################

# Absolute path to this script
SCRIPT=`readlink -f $0`
# Absolute path this script is in
SCRIPTPATH=`dirname $SCRIPT`

cd ${SCRIPTPATH}/../ && ./pypo-notify.py $1 $2 $3 $4 $5 $6 $7 $8 &
