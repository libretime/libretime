#!/bin/sh
recorder_user="pypo"
export HOME="/home/pypo/"
# Location of pypo_cli.py Python script
recorder_path="/opt/recorder/bin/"
recorder_script="testrecordscript.py"
echo "*** Daemontools: starting daemon"
cd ${recorder_path}
exec 2>&1
# Note the -u when calling python! we need it to get unbuffered binary stdout and stderr
exec setuidgid ${recorder_user} \
               python -u ${recorder_path}${recorder_script} \
               -f
# EOF
