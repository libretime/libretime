#!/bin/sh

echo
echo "This will output the pypo log file."
echo "Type in password for pypo user (default password is 'pypo'):"

su -l pypo -c "less /etc/service/pypo/log/main/current"
