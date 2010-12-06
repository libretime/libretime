#!/bin/sh
ls_user="pypo"
export HOME="/home/pypo/"
ls_path="/usr/local/bin/liquidsoap"
ls_param="/opt/pypo/bin/scripts/ls_script.liq"
echo "*** Daemontools: starting liquidsoap"
cp /opt/pypo/files/basic/silence.lsp /opt/pypo/cache/current.lsp
exec 2>&1
exec sudo -u ${ls_user} ${ls_path} ${ls_param} 
# EOF
