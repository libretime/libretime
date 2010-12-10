#!/bin/sh
ls_user="pypo"
export HOME="/home/pypo/"
#ls_path="/usr/local/bin/liquidsoap"
ls_path="/opt/pypo/bin/liquidsoap/liquidsoap"
ls_param="/opt/pypo/bin/scripts/ls_script.liq"
echo "*** Daemontools: starting liquidsoap"
echo "cp /opt/pypo/files/basic/silence.lsp /opt/pypo/cache/current.lsp"
cp /opt/pypo/files/basic/silence.lsp /opt/pypo/cache/current.lsp
exec 2>&1
echo "exec sudo -u ${ls_user} ${ls_path} ${ls_param} "
cd /opt/pypo/bin/scripts && sudo -u ${ls_user} ${ls_path} ${ls_param} 
# EOF
