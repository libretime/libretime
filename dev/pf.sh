#!/bin/sh

su -l pypo -c "tail -F /etc/service/pypo-fetch/log/main/current"
