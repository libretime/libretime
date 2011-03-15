#!/bin/sh

su -l pypo -c "tail -F /etc/service/pypo-liquidsoap/log/main/current"
