#!/bin/sh
ls_user="pypo"
export HOME="/home/pypo/"
api_client_path="/opt/pypo/"
ls_path="/opt/pypo/bin/liquidsoap/liquidsoap"
ls_param="/opt/pypo/bin/scripts/ls_script.liq"
echo "*** Daemontools: starting liquidsoap"
exec 2>&1

cd /opt/pypo/bin/scripts
sudo PYTHONPATH=${api_client_path} -u ${ls_user} ${ls_path} ${ls_param}
# EOF
