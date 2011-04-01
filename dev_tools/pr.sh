#!/bin/sh

echo
echo "This will tail the recorder log file."
echo "Type in password for pypo user (default password is 'pypo'):"

su -l pypo -c "tail -F /etc/service/recorder/log/main/current"
