#!/bin/bash
if [[ $EUID -ne 0 ]]; then
   echo "This script must be run as root." 1>&2
   exit 1
fi

usage () {
    echo "Use --enable <user> or --disable flag. Enable is to set up environment" 
    echo "for specified user. --disable is to reset it back to pypo user"
}

if [ "$1" = "--enable" ]; then

    /etc/init.d/airtime-playout stop
    /etc/init.d/airtime-playout start-liquidsoap

    user=$2

    echo "Changing ownership to user $1"
    chmod -R a+rw /var/log/airtime/pypo
    chmod a+r /etc/airtime/airtime.conf
    chown -Rv $user:$user /var/tmp/airtime/pypo/
    chmod -v a+r /etc/airtime/api_client.cfg
elif [ "$1" = "--disable" ]; then

    user="pypo"

    echo "Changing ownership to user $1"
    chmod 644 /etc/airtime/airtime.conf
    chown -Rv $user:$user /var/tmp/airtime/pypo/
    chmod -v a+r /etc/airtime/api_client.cfg    


    /etc/init.d/airtime-playout stop-liquidsoap
    /etc/init.d/airtime-playout start
else
    usage
fi
