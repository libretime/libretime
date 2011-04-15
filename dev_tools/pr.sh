#!/bin/sh

echo
echo "This will tail the recorder log file."
echo "Type in password for pypo user (default password is 'pypo'):"

su -l pypo -c "tail -F /var/log/airtime/show-recorder/main/current"
