#!/bin/sh
pypo_user="pypo"
export HOME="/home/pypo/"
# Location of pypo_cli.py Python script
pypo_path="/opt/pypo/bin/"
api_client_path="/opt/pypo/"
pypo_script="pypo-cli.py"
echo "*** Daemontools: starting daemon"
cd ${pypo_path}
exec 2>&1

PYTHONPATH=${api_client_path}:$PYTHONPATH
export PYTHONPATH

# Note the -u when calling python! we need it to get unbuffered binary stdout and stderr
exec setuidgid ${pypo_user} \
               python -u ${pypo_path}${pypo_script}
# EOF
