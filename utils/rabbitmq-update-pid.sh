#!/bin/bash

#Hack to parse rabbitmq pid and place it into the correct directory. This is also
#done in our rabbitmq init.d script, but placing it here so that monit recognizes 
# it faster (in time for the upcoming airtime-check-system)
codename=`lsb_release -cs`
if [ "$codename" = "lucid" -o "$codename" = "maverick" -o "$codename" = "natty" -o "$codename" = "squeeze" ]
then
    rabbitmqpid=`sed "s/.*,\(.*\)\}.*/\1/" /var/lib/rabbitmq/pids`
else
    #RabbitMQ in Ubuntu Oneiric and newer have a different way of storing the PID.
    rabbitmqstatus=`/etc/init.d/rabbitmq-server status | grep "\[{pid"`
    rabbitmqpid=`echo $rabbitmqstatus | sed "s/.*,\(.*\)\}.*/\1/"`
fi
echo "RabbitMQ PID: $rabbitmqpid"
echo "$rabbitmqpid" > /var/run/rabbitmq.pid
