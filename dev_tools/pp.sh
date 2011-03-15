#!/bin/sh

su -l pypo -c "tail -F /etc/service/pypo-push/log/main/current"
