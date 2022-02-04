#!/usr/bin/env bash

#Create a RabbitMQ airtime_tests user
#This is necessary for tests to run

rabbitmqctl start_app

RABBITMQ_VHOST="/airtime_tests"
RABBITMQ_USER="airtime_tests"
RABBITMQ_PASSWORD="airtime_tests"
EXCHANGES="airtime-pypo|pypo-fetch|airtime-analyzer|media-monitor"

rabbitmqctl list_vhosts | grep $RABBITMQ_VHOST
RESULT="$?"

if [ $RESULT = "0" ]; then
  rabbitmqctl delete_vhost $RABBITMQ_VHOST
  rabbitmqctl delete_user $RABBITMQ_USER
fi

rabbitmqctl add_vhost $RABBITMQ_VHOST
rabbitmqctl add_user $RABBITMQ_USER $RABBITMQ_PASSWORD
rabbitmqctl set_permissions -p $RABBITMQ_VHOST $RABBITMQ_USER "$EXCHANGES" "$EXCHANGES" "$EXCHANGES"

export RABBITMQ_USER
export RABBITMQ_PASSWORD
export RABBITMQ_VHOST

export LIBRETIME_UNIT_TEST="1"

#Change the working directory to this script's directory
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$DIR" || (echo "could not cd in $DIR!" && exit 1)

#Run the unit tests
phpunit --verbose --log-junit test_results.xml
