#!/bin/sh
ls_user="pypo"
export HOME="/var/tmp/airtime/pypo/"
api_client_path="/usr/lib/airtime/pypo/"
ls_path="/usr/lib/airtime/pypo/bin/liquidsoap/liquidsoap"
ls_param="/usr/lib/airtime/pypo/bin/scripts/ls_script.liq"
echo "*** Daemontools: starting liquidsoap"
exec 2>&1

cd /usr/lib/airtime/pypo/bin/scripts

export PYTHONPATH=${api_client_path}
exec ${ls_path} ${ls_param}

# EOF
