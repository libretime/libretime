#!/bin/sh
media_monitor_user="pypo"

# Location of pypo_cli.py Python script
media_monitor_path="/usr/lib/airtime/media-monitor/"
media_monitor_script="MediaMonitor.py"

api_client_path="/usr/lib/airtime/pypo/"
cd ${media_monitor_path}

echo "*** Daemontools: starting daemon"
exec 2>&1
# Note the -u when calling python! we need it to get unbuffered binary stdout and stderr

export PYTHONPATH=${api_client_path}
setuidgid ${media_monitor_user} python -u ${media_monitor_path}${media_monitor_script}
# EOF
