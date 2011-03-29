#!/bin/sh
echo
echo "This will tail the pypo-liquidsoap log file."
echo "Type in password for pypo user (default password is 'pypo'):"
su -l pypo -c "tail -F /etc/service/pypo-liquidsoap/log/main/current"
