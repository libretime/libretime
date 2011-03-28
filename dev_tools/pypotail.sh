#!/bin/sh

su -l pypo -c "tail -F /etc/service/pypo/log/main/current"
