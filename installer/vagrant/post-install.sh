#!/usr/bin/env bash

# append_if_missing <line> <file>
append_if_missing() {
    grep -xqF "$1" "$2" || echo "$1" >> "$2"
}

# Setup postgresql remote access
append_if_missing "listen_addresses = '*'" /etc/postgresql/*/main/postgresql.conf
append_if_missing "host all all 0.0.0.0/0 md5" /etc/postgresql/*/main/pg_hba.conf
append_if_missing "host all all ::/0 md5" /etc/postgresql/*/main/pg_hba.conf

systemctl restart postgresql.service

# Setup rabbitmq management interface
rabbitmq-plugins enable rabbitmq_management
rabbitmqctl set_user_tags libretime administrator
