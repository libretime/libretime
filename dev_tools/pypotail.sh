#!/bin/sh

echo
echo "This will tail the pypo log file."
echo "Type in password for pypo user (default password is 'pypo'):"

su -l pypo -c "tail -F /etc/service/pypo/log/main/current"
