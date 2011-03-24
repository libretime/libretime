#!/bin/sh
############################################
# just a wrapper to call the notifyer      #
# needed here to keep dirs/configs clean   #
# and maybe to set user-rights             #
############################################
cd ../ && ./pypo-notify.py $1 $2 $3 $4 $5 $6 $7 $8 &
