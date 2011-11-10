#!/bin/bash

#Hack to parse rabbitmq pid and place it into the correct directory. This is also
#done in our rabbitmq init.d script, but placing it here so that monit recognizes 
# it faster (in time for the upcoming airtime-check-system)
codename=`lsb_release -cs`
if [ "$codename" == "oneiric" ];
then
    rabbitmqstatus=`/etc/init.d/rabbitmq-server status | grep "\[{pid"`
    rabbitmqpid=`echo $rabbitmqstatus | sed "s/.*,\(.*\)\}.*/\1/"`
else
    rabbitmqpid=`sed "s/.*,\(.*\)\}.*/\1/" /var/lib/rabbitmq/pids`
fi
echo "RabbitMQ PID: $rabbitmqpid"
echo "$rabbitmqpid" > /var/run/rabbitmq.pid
