#!/bin/bash

which py.test
pytest_exist=$?

if [ "$pytest_exist" != "0" ]; then
    echo "Need to have py.test installed. Exiting..."
    exit 1
fi

SCRIPT=`readlink -f $0`
# Absolute directory this script is in
SCRIPTPATH=`dirname $SCRIPT`

export PYTHONPATH=$PYTHONPATH:$SCRIPTPATH/..:$SCRIPTPATH/../..

py.test

