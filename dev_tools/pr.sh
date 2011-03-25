#!/bin/sh

su -l pypo -c "tail -F /etc/service/recorder/log/main/current"
